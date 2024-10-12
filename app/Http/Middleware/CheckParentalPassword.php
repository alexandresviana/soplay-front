<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\Assinante;

use App\Models\{App, Conteudo, ConteudoCanais, ConteudoSeriesEpisodios, ConteudoSeries, Category};

class CheckParentalPassword
{
    public function handle($request, Closure $next)
    {
        if ($request->session()->get('parental_checked')) {
            $request->session()->forget('parental_checked');
            return $next($request);
        }

        $idContent  = $request->route('id');
        $idCategory = $request->route('category');

        // exibindo video
        if ($request->route()->named('video')) {
            $content = Conteudo::find($idContent);
        }

        // exibindo video ao vivo
        if ($request->route()->named('video_aovivo')) {
            $content = ConteudoCanais::find($idContent);
        }

        // exibindo episodio de serie
        if ($request->route()->named('video.series_play')) {
            $content = ConteudoSeriesEpisodios::find($idContent);
        }

        // exibindo serie
        if ($request->route()->named('video.series')) {
            $content = ConteudoSeries::find($idContent);
        }

        // exibindo categoria
        if ($request->route()->named('home_filmes_categoria') || $request->route()->named('home_series_categoria')) {
            $content = Category::find($idCategory);
        }


        if (isset($content->require_parental_password) && $content->require_parental_password) {
            $request->session()->put('parental_return_url', url()->current());
            return redirect(route('user_profile_parental_password_request'));
        }

        return $next($request);
    }
}
