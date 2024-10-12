<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeforeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $s = $request->session();
        //dd($request->user());
        return $next($request);
    }
}
