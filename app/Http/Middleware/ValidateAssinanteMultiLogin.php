<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{App,AssinantesCurrentTokenLogins,};

use Illuminate\Support\Facades\Route;

class ValidateAssinanteMultiLogin
{
    public function handle($request, Closure $next)
    {
        $token = $request->session()->get('current_token_login');
        // se nao tem o token nao esta logado, nao verifica multilogin
        if(!$token) {
            return $next($request);
        }

        // esta logado, verifica multilogin

        $actl = AssinantesCurrentTokenLogins::validateMultiLoginForUserToken($token);

        if(!$actl) {
            request()->session()->flash('multilogin_max', 'Limite de dispositivos simultâneos atingido. Para completar esse acesso finalize a visualização em outros dispositivos.');
            return redirect(route('home'));
        }


        $action = explode('\\', Route::currentRouteAction());
        $action = explode('@', end($action));
        $action = $action[1];

        $actl = AssinantesCurrentTokenLogins::updateDateActionForUserToken($token, $action);

        return $next($request);
    }
}
