<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Models\Assinante;
use App\Models\App;
use App\Models\Conteudo;
use App\Models\AssinantesCurrentTokenLogins;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Get Subscriver
     *
     * @return void
     * @deprecated version 1.0.0
     */
    public function __subscriber()
    {
        $usr = Auth::user();
        if($usr == null) {
            /*
            $usr = new Assinante;
            $usr->app = 1;
            $usr = Assinante::find(5);
            */
            $app = App::find(_DEFAULT_APP_BY_DOMAIN);
            $usr = Assinante::find($app->settingsDefaultSubscriber());
        }
        return $usr;
    }

    public function checkUserStatus()
    {
        $sub = $this->__subscriber();
        if($sub->status == 1) {
            return true;
        }

        // não está ativo, provavelmente estava, fez login, mas não está mais
        Auth::logout();
        request()->session()->flush();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    }

    protected function subscriber()
    {
        $usr = Auth::user();
        if($usr == null) {
            /*
            $usr = new Assinante;
            $usr->app = 1;
            $usr = Assinante::find(5);
            */
            $app = App::find(_DEFAULT_APP_BY_DOMAIN);
            $usr = Assinante::find($app->settingsDefaultSubscriber());
        }
        return $usr;
    }


    public  function __app()
    {
        $rApp = (int) request()->get('app');
        if($rApp) {
            return App::get($rApp);
        }
        $id = $this->__subscriber()->app;
        return App::get($id);
    }

    public function setCurrentTokenLogin()
    {
        // caso ja exista a sessao ou se o usuario nao esta autenticado,
        // nao seta novo token
        $tokl = request()->session()->get('current_token_login');
        if($tokl || !Auth::user()) {
            // se tem o token, atualiza action do usuario
            // atualizacao podera ser mais refinada, sendo colocada por action do controller
            // - nao atualiza se estiver em action de player de video
            if($tokl && !in_array(strtolower(Route::getCurrentRoute()->getActionMethod()), ['showvideoaovivo', 'showvideo', 'showvideoseriesplay', 'aovivoindex', 'aovivoradioindex'])) {
                AssinantesCurrentTokenLogins::updateDateActionForUserToken($tokl, 'site_navigation');
            }
            return false;
        }

        $sub = $this->__subscriber();
        $actl = AssinantesCurrentTokenLogins::newTokenLogin($sub);
        $tok = $actl->token;

        request()->session()->put('current_token_login', $tok);
        return true;
    }


}

