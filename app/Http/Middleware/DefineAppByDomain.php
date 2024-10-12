<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\Assinante;

use App\Models\{App};

class DefineAppByDomain
{
    public function handle($request, Closure $next)
    {
        $host  = $request->server('HTTP_HOST');

        $app = App::whereRaw("FIND_IN_SET(?, osm_host) > 0", [$host])->first();
        if(!$app) {
            $app = App::find(App::DEFAULT_APP);
        }

        define('_DEFAULT_APP_BY_DOMAIN', $app->id);

        return $next($request);
    }
}
