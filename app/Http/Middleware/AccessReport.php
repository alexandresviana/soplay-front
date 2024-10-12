<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AssinantesCurrentTokenLogins;
use Browser;
use App\Models\{AccessReports};
use DateTime;

class AccessReport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $token = request()->session()->get('current_token_login');
        $time = new DateTime();
        $userId = $user->id;
        $platformName = Browser::browserFamily();
        $data = [
            'id_usuario' => $userId,
            'data_cad' => $time,
            'update' => $time,
            'device' => $platformName
        ];

        // $tokenVerify = (new AccessReports)->where('token', '=', $token)
        //     ->orderBy('id', 'desc')
        //     ->first();

        // if (!$tokenVerify) {
        //     $data = (new AccessReports)->userInserts($data);
        // }

        $userVerify = (new AccessReports)->where('id_usuario', '=', $userId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$userVerify) {
            $data = (new AccessReports)->userInserts($data);
        }

        if ($userVerify) {
            $data = (new AccessReports)->userUpdate($userVerify->id, ['update' => $time, 'device' => $platformName]);
        }

        return $next($request);
    }
}
