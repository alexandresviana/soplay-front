<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plan;
use App\Models\UserLogged;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoggedService
{
    public function saveLogged(Request $request, string $auth): void
    {
        $id = $auth;
        $device = $request->header();
        $ip = $request->ip();

        $unique = uniqid();

        session()->put('auth_session_limite', $unique);

        UserLogged::create([
            'user_id' => $id,
            'device' => json_encode($device),
            'auth_session_limite' => $unique,
            'ip' => $ip,
            ''
        ]);
    }

    public function hasLogged($user): bool
    {
        // $planoContent = json_decode($user->settings_conteudos);
        // $plano = Plan::find($planoContent->planos[0]);
        $plano = $user->getMainPlan();
        if(!$plano) {
            return false;
        }

        $userLogged = UserLogged::where('user_id', $user->id)->count();

        return ($userLogged < $plano->dispositivos);
    }

    public function removeSession($authSessionLimite): void
    {
        UserLogged::where('auth_session_limite', $authSessionLimite)->delete();
    }

    public function all($id): Collection
    {
        return UserLogged::where('user_id', $id)->get();
    }

    public function delete($id): void
    {
        UserLogged::where('id', $id)->delete();
    }

    public function find($id): UserLogged
    {
        return UserLogged::findOrFail($id);
    }
}
