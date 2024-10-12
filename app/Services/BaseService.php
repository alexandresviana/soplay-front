<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Perfil;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class BaseService
{
    protected function subscriber()
    {
        $usr = Auth::user();
        if($usr == null) {
            $app = App::find(_DEFAULT_APP_BY_DOMAIN);
            $usr = Assinante::find($app->settingsDefaultSubscriber());
        }
        return $usr;
    }

}
