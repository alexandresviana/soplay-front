<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\Assinante;

use App\Models\{App};

class HeadersCache
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->header('Cache-Control', 'max-age=3000, public, stale-if-error=86400');
        $response->header('Pragma', 'public');
        $response->header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');


        return $response;
    }
}
