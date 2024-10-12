<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\Assinante;

use App\Models\{App,Conteudo,ConteudoCanais,ConteudoSeriesEpisodios,ConteudoSeries,Category, ConteudoDisponivelAluguel};

class CheckVideoIsRent
{
    public function handle($request, Closure $next)
    {
        $idContent  = $request->route('id');
        $idCategory = $request->route('category');
        $contentKind= 'conteudo';

        $content = false;

        // exibindo video
        if ($request->route()->named('video')) {
            $content = Conteudo::find($idContent);
        }

        // exibindo video ao vivo
        ///if ($request->route()->named('video_aovivo')) {
        //    $content = ConteudoCanais::find($idContent);
        //}

        // exibindo episodio de serie
        if ($request->route()->named('video.series_play')) {
            $contentEp = ConteudoSeriesEpisodios::find($idContent);
            $content   = ConteudoSeries::find($contentEp->idRefConteudoSerie);
            $contentKind = 'conteudo_series';
        }

        // exibindo serie
        if ($request->route()->named('video.series')) {
            $content = ConteudoSeries::find($idContent);
            $contentKind = 'conteudo_series';
        }

        if(!$content) {
            return $next($request);
        }

        // obtem lista de alugel ativas para o conteudo sendo acessado
        $rentList = ConteudoDisponivelAluguel::where('tipo_conteudo', $contentKind)
                        ->where('id_conteudo', $content->id)
                        ->where('ativo', 1)->get();

        // caso nao tenha aluguel ativo para o conteudo, continua normalmente
        if(!sizeof($rentList)) {
            return $next($request);
        }

        $assinante = Auth::user();

        // verifica se o usuario tem acesso ao conteudo
        foreach($rentList as $rentContent) {
            // caso tenha, continua normalmente
            if($rentContent->disponivelParaAssinante($assinante->id)) {
                $request->session()->forget('content.rent');
                return $next($request);
            }
        }

        // conteudo é para aluguel e usuario nao tem acesso a ele
        $request->session()->put('content.rent', ['kind' => $contentKind, 'id' => $content->id]);
        return redirect(route('videorent.show', ['id' => $content->id, 'kind' => $contentKind]));
    }
}
