<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogged extends Model
{
    protected $table = 'user_logged';
    protected $fillable = ['device', 'user_id', 'ip', 'auth_session_limite'];


    protected function getAgentAttribute()
    {
        $agent = json_decode($this->device, true);
        return $this->handlerAgent($agent['user-agent'][0]);
    }

    protected function handlerAgent($agent): string
    {

        if (preg_match('/MSIE (\d+\.\d+);/', $agent)) {
            return "Navegador Internet Explorer";
        }
        if (preg_match('/Chrome[\/\s](\d+\.\d+)/', $agent)) {
            return "Navegador Chrome";
        }
        if (preg_match('/Edge\/\d+/', $agent)) {
            return "Navegador Edge";
        }
        if (preg_match('/Firefox[\/\s](\d+\.\d+)/', $agent)) {
            return "Navegador Firefox";
        }
        if (preg_match('/OPR[\/\s](\d+\.\d+)/', $agent)) {
            return "Navegador Opera";
        }
        if (preg_match('/Safari[\/\s](\d+\.\d+)/', $agent)) {
            return "Navegador Safari";
        }

        return "Other";
    }
}
