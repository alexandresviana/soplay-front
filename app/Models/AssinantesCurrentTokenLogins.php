<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\AssinantesCurrentTokenLogins;
use Illuminate\Support\Str;

use \Datetime;

class AssinantesCurrentTokenLogins extends Model
{
    use HasFactory;

    // um dispositivo constará como ativo e entrará na contagem
    // de uso para totalizar o total quando o campo "block" for true
    // e o campo "last_update" for maior que now()    - DEFAULT_TIMEOUT_LAST_UPDATE_IN_SECONDS
    //            16:31:00      >           (16:32:00 - 60s) = true
    //const DEFAULT_TIMEOUT_LAST_UPDATE_IN_SECONDS = 60;
    const DEFAULT_TIMEOUT_LAST_UPDATE_IN_SECONDS = 60 * 60 * 1; // 1h

    // acoes em execucao que constam como um dispositivo ativo
    // necessario que actions estejam no grupo com o middleware
    // assinante.multilogin para que a requisicao seja bloqueada,
    // caso contrario apenas contara como um dispositivo que bloqueia
    // mas nao bloqueará
    const BLOCKABLE_ACTIONS = [ 'playing',		// AppsController
								'assistindo',	// ConteudoAssistido
								'startplay',	// AppsController
								];

	protected $table = 'tbAssinantesCurrentTokenLogins';
	public $timestamps = false;


	public static function newTokenLogin(Assinante $subscriber)
	{
		$d = date('Y-m-d H:i:s');

		$actl = new AssinantesCurrentTokenLogins;
		$actl->id_assinante	  = $subscriber->id;
		$actl->token 		  =  Str::random(32);
		$actl->current_action = 'login';
		$actl->block 		  = false;
		$actl->created_at 	  = $d;
		$actl->last_update	  = $d;
		$actl->device_info 	  = '{}';

		$actl->save();

		return $actl;
	}

	public static function validateTokenLogin($token)
	{
		$actl = AssinantesCurrentTokenLogins::where('token', $token)->first();
		if(!$actl) {
			return false;
		}
		return $actl->id_assinante;
	}

	public static function validateMultiLoginForUserToken($token)
	{
		$actl = AssinantesCurrentTokenLogins::where('token', $token)->first();

		if(!$actl) {
			return false;
		}

		$subscriber = $actl->getSubscriber();

		if(!$subscriber) {
			return false;
		}

		$mainPlan   = $subscriber->getMainPlan();

		if(!$mainPlan) {
			return false;
		}

		$devicesLimit = $mainPlan->dispositivos;


		// alterar query pra retornar somente registros que nao ultrassaram o timeout
		// - api devera atualizar o lastupdate e block do registro
		// - criar endpoint "playing" ...

		$actls = AssinantesCurrentTokenLogins::where('id_assinante', $actl->id_assinante)->get();

		$totalDevices = 0;
		$dNow = new Datetime;
		$timeoutInterval = date_interval_create_from_date_string(sprintf('%s seconds', AssinantesCurrentTokenLogins::DEFAULT_TIMEOUT_LAST_UPDATE_IN_SECONDS));
		foreach($actls as $login) {
			if(!$login->block) {
				continue;
			}
			$dTimeout = new Datetime($login->last_update);
			$dTimeout->add($timeoutInterval);

			// verifica se o token ja nao passou o horario de timeout,
			// indicando que nao esta mais em uso
			if($dNow < $dTimeout) {
				$totalDevices += 1;
			}
		}

				// qtd logins     limite de dispositivos do plano
		//return $totalDevices <= $devicesLimit;
		return $totalDevices < $devicesLimit;
	}

	public static function updateDateActionForUserToken($token, $action)
	{
		$actl = AssinantesCurrentTokenLogins::where('token', $token)->first();

		if(!$actl) {
			return false;
		}

		$dNow = new Datetime;
		$actl->last_update = $dNow;
		$actl->current_action = $action;
		$actl->block = in_array($action, AssinantesCurrentTokenLogins::BLOCKABLE_ACTIONS);
		$actl->update();

		return true;
	}

	public function getSubscriber()
	{
		return Assinante::find($this->id_assinante);
	}
}
