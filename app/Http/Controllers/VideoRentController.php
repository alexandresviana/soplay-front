<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Models\Assinante;
use App\Models\App;
use App\Models\Plan;
use App\Models\Conteudo;
use App\Models\ConteudoSeries;
use App\Models\ConteudoSeriesEpisodios;
use App\Models\AssinantesCurrentTokenLogins;
use App\Models\AssinantesConteudoAlugado;
use App\Models\OsmPaymentApi;

use \Exception, \Datetime, \DateInterval;


class VideoRentController extends Controller
{
    public function showVideo(Request $request, $id, $kind)
    {
        $cr         = $request->session()->get('content.rent');
        $contentId  = $cr['id'];
        $contentKind= $cr['kind'];

        if($contentId != $id || $contentKind != $kind) {
            $request->session()->flash('success', 'Acesso incorreto para aluguel');
            return redirect(route('home'));
        }

        $sub = $this->__subscriber();

        $conteudo = false;

        if($contentKind == 'conteudo') {
            if(!$sub->getConteudosVODPlanos()) {
                return redirect('/');
            }
            $conteudo = Conteudo::find($contentId);
        }else if($contentKind == 'conteudo_series') {
            if(!$sub->getConteudosSeriesVODPlanos() && !$sub->getConteudosDesenhosVODPlanos()) {
                return redirect('/');
            }
            $conteudo = ConteudoSeries::find($contentId);
            //$conteudo    = ConteudoSeries::find($conteudotEp->idRefConteudoSerie);
        }

        $rentInfo = $conteudo->findAvailableRent($sub->app);

        if(!$rentInfo) {
            $request->session()->flash('success', 'Conteúdo indisponível para aluguel');
            return redirect(route('home'));
        }

        $activeRent = $conteudo->findActiveRentForSubscriber($sub->id);

        $infoInvoice = false;

        AssinantesCurrentTokenLogins::updateDateActionForUserToken(request()->session()->get('current_token_login'), 'content_rent');

        return view('videorent.show_video', ['conteudo' => $conteudo, 'rent_info' => $rentInfo, 'active_rent' => $activeRent, 'rented_content' => $activeRent, 'invoice_info' => false]);
    }

    public function rentConfirm(Request $request, $id, $kind)
    {
        $cr         = $request->session()->get('content.rent');
        $contentId  = @$cr['id'];
        $contentKind= @$cr['kind'];

        if($contentId != $id || $contentKind != $kind) {
            $request->session()->flash('success', 'Acesso incorreto para aluguel');
            return redirect(route('home'));
        }

        AssinantesCurrentTokenLogins::updateDateActionForUserToken(request()->session()->get('current_token_login'), 'content_rent_confirm');

        $sub = $this->__subscriber();

        $conteudo = false;

        if($contentKind == 'conteudo') {
            if(!$sub->getConteudosVODPlanos()) {
                return redirect('/');
            }
            $conteudo = Conteudo::find($contentId);
        }else if($contentKind == 'conteudo_series') {
            if(!$sub->getConteudosSeriesVODPlanos() && !$sub->getConteudosDesenhosVODPlanos()) {
                return redirect('/');
            }
            $conteudo = ConteudoSeries::find($contentId);
        }

        $activeRent = $conteudo->findActiveRentForSubscriber($sub->id);
        $rentInfo   = $conteudo->findAvailableRent($sub->app);

        if(!$rentInfo) {
            $request->session()->flash('success', 'Conteúdo indisponível para aluguel');
            return redirect(route('home'));
        }

        if($activeRent) {
            return view('videorent.show_video', ['conteudo' => $conteudo, 'rent_info' => $rentInfo, 'active_rent' => $activeRent, 'rented_content' => $activeRent, 'invoice_info' => false]);
        }

        $dStart = new Datetime;
        $dEnd   = clone $dStart;
        $dEnd   = $dEnd->add(new DateInterval(sprintf('PT%sH', $rentInfo->tempo_disponivel)));

        $infoInvoice = false;

        try {
            $token          = request()->session()->get('current_token_login');
            $rentKind       = 'video_rent';
            $infoInvoice    = OsmPaymentApi::createInvoice($token, $rentKind, $contentKind, $conteudo->id);

            // gravar invoice osm
            // verificar se usará api pra confirmar aluguel

            $order = $infoInvoice['order'];
            $rentedContent = AssinantesConteudoAlugado::findOrCreateForOrder($order);
            $rentedContent->id_conteudo_disponivel_aluguel = $rentInfo->id;
            $rentedContent->id_assinante    = $sub->id;
            $rentedContent->id_conteudo     = $conteudo->id;
            $rentedContent->tipo_conteudo   = $contentKind;
            $rentedContent->valor           = $rentInfo->valor;
            $rentedContent->data_inicio     = $dStart->format('Y-m-d H:i:s');
            $rentedContent->data_fim        = $dEnd->format('Y-m-d H:i:s');
            $rentedContent->ativo           = 0;
            $rentedContent->save();

        }catch(Exception $e) {
            $msg = sprintf('Ocorreu um erro ao iniciar a cobrança: %s', $e->getMessage());
            $request->session()->flash('success', $msg);
            return view('videorent.show_video', ['conteudo' => $conteudo, 'rent_info' => $rentInfo, 'active_rent' => false, 'invoice_info' => false]);
        }

        $activeRent = $conteudo->findActiveRentForSubscriber($sub->id);

        $request->session()->flash('success', 'Aluguel confirmado, o conteúdo estará disponível após confirmação do pagamento');
        return view('videorent.show_video', ['conteudo' => $conteudo, 'rent_info' => $rentInfo, 'active_rent' => $activeRent, 'rented_content' =>$rentedContent, 'invoice_info' => $infoInvoice]);
    }

    public function rentals(Request $request)
    {
        $sub = $this->__subscriber();
        $rentals = AssinantesConteudoAlugado::where('id_assinante', $sub->id)->orderBy('created_at', 'desc')->get();

        return view('videorent.rentals', ['rentals' => $rentals]);
    }

}
