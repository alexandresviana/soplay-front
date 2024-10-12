<?php

namespace App\Http\Middleware;

class UserLoggedMiddleware
{

    public function handle($request, \Closure $next)
    {

        $session = $request->session()->get('user_logged');

        if (!$session) {
            session()->flash('limit_error', 'Limite de usuários atigindos. Desconecte um usuário para continuar.');
            return redirect(route('user_devices'));
        }

        return $next($request);
    }
}
