<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;

use Illuminate\Notifications\Notifiable;

use App\Events\SubscriberSaving;
use App\Listeners\SubscriberSavingListener;

use \Exception;
use \DateTime, \DateInterval;

class Assinante extends Authenticatable implements MustVerifyEmail //Model
{
    use HasFactory, Notifiable;

    const DEFAULT_SUBSCRIBER_ID = 5;

	protected $table = 'tbAssinantes';
	//public $timestamps = false;

	public $fillable = ['nome', 'email', 'password', 'app', 'razao_social', 'celular'];

    public $attributes = [
        'status'             => 1,
        'settings_conteudos' => '{"planos": [], "password_parental": false, "livemode_copa_nordeste": false, "livemode_copa_nordeste_exportar": false, "livemode_copa_nordeste_exportacao": false}',
    ];

    protected $dispatchesEvents = [
        'saving' => SubscriberSaving::class,
    ];

    protected static function boot()
    {
        parent::boot();

        // verifica se assinante tem creditos para utilizar plano, no momento em que salva ou atualiza
        Event::listen(
            SubscriberSaving::class,
            [SubscriberSavingListener::class, 'checkCreditos']
        );
    }

    public static function byAppToPaginate($app, $id = null)
    {
        if(!$id) {
            return Assinante::where('app', $app)->orderBy('nome', 'desc');
        }
        return Assinante::where('app', $app)->where('id', $id);//->where('ativo', '1')->get();
    }

    public function getSettingsConteudos($key)
    {
        //if(is_string($key)) {
        //  return $this->settings_conteudos[$key];
        //}
        $info = json_decode($this->settings_conteudos);
        if($info == false) {
            if($key == 'planos') {
                return [];
            }
            return false;
        }

        if(!isset($info->$key)) {
            if($key == 'planos') {
                return [];
            }
            return false;
        }

        return $info->$key;
    }

    public function settingsConteudosHasPlan($id)
    {
        //$plans = explode(',', $this->getSettingsConteudos('planos'));
        $plans = $this->getSettingsConteudos('planos');
        if(!$plans) {
            return false;
        }
        return array_search($id, $plans) !== false;
    }

    public function settingsPasswordParental()
    {
        $password = $this->getSettingsConteudos('password_parental');
        return $password;
    }

    public function updateSettingsConteudos($requestInfo)
    {
        $info = [];
        $info['planos']             = $requestInfo->settings_conteudos_planos;
        $info['password_parental']  = $requestInfo->settings_conteudos_password_parental;
        // - possui ou nao acesso copa nordeste / livemode
        // - assinante deve ou nao ser exportado para arquivo de integracao
        // - data de exportacao
        // sempre que livemode_copa_nordeste mudar de false pra true, assinante deve ser
        // exportado novamente. Quando muda de true para false tambem, para cancelamento
        // no livemode
        $info['livemode_copa_nordeste']             = (bool) $requestInfo->settings_conteudos_livemode_copa_nordeste;
        $info['livemode_copa_nordeste_exportar']    = $this->getSettingsConteudos('livemode_copa_nordeste_exportar');
        $info['livemode_copa_nordeste_exportacao']  = $requestInfo->settings_conteudos_livemode_copa_nordeste_exportacao;


        $livemodeCN = $this->getSettingsConteudos('livemode_copa_nordeste');
        // tinha copa nordeste mas nao vai mais ter, exporta
        if($livemodeCN && !$info['livemode_copa_nordeste']) {
            $info['livemode_copa_nordeste_exportar'] = true;
        }
        // nao tinha copa nordeste mas vai mais ter, exporta
        if(!$livemodeCN && $info['livemode_copa_nordeste']) {
            $info['livemode_copa_nordeste_exportar'] = true;
        }



        if($requestInfo->settings_conteudos_password_parental != $this->settingsPasswordParental()) {
            $info['password_parental']  = Hash::make($requestInfo->settings_conteudos_password_parental);
        }

        $this->settings_conteudos = json_encode($info);
        return true;
    }

    public function updateSettingsPasswordParental($password)
    {
        $info = json_decode($this->settings_conteudos);

        if(!isset($info->password_parental)) {
            $info->password_parental = '';
        }

        $info->password_parental = Hash::make($password);

        $this->settings_conteudos = json_encode($info);
        $this->save();

        return true;
    }

    public function planIsValid(?Plan $plan)
    {
        if(!$plan) {
            return false;
        }
        $validade = (int) $plan->validade;
        if($validade == 0) {
            return true;
        }

        $datePlus = new DateInterval(sprintf('P%sD', $validade));

        $startValidade = $this->created_at;

        if($this->planos_validade_inicio) {
            $startValidade = $this->planos_validade_inicio;
        }

        $startTime = new DateTime($startValidade);
        $endTime   = new DateTime($startValidade);
        $now       = new DateTime;

        $endTime->add($datePlus);

        return $endTime > $now;
    }

    public function getConteudosPlanos($tipo = null)
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');

        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            $pkgs = $plan->getPackages();
            $cnts = $plan->getPlanConteudos($tipo);
            foreach($cnts as $conteudo) {
                array_push($cont, $conteudo);
            }
        }

        $cont = array_unique($cont);

        return $cont;
    }

    public function getAllConteudosPlanosByPackageCategory($tipo = null)
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            $cnts = $plan->getPlanAllConteudosByCategory($tipo);
            foreach($cnts as $conteudo) {
                if(!sizeof($conteudo)) {
                    continue;
                }
                foreach($conteudo as $k => $x) {
                    foreach($x as $i) {
                        $cont[$k][] = $i;
                    }
                }
            }
        }

        return $cont;
    }

    public function getConteudosPlanosByPackageCategory($tipo = null)
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            $cnts = $plan->getPlanAllConteudosByCategory($tipo);
            $cnts = $cnts['conteudos'];
            foreach($cnts as $cat => $conteudo) {
                if(!sizeof($conteudo)) {
                    continue;
                }
                foreach($conteudo as $k => $x) {
                    $cont[$cat][] = $x;
                }
            }
        }

        return $cont;
    }

    public function getConteudosCanaisPlanosByPackageCategory($tipo = null)
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            $cnts = $plan->getPlanAllConteudosByCategory($tipo);
            $cnts = $cnts['conteudosCanais'];
            foreach($cnts as $cat => $conteudo) {
                if(!sizeof($conteudo)) {
                    continue;
                }
                foreach($conteudo as $k => $x) {
                    $cont[$cat][] = $x;
                }
            }
        }

        return $cont;
    }

    public function getConteudosSeriesPlanosByPackageCategory()
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            $cnts = $plan->getPlanAllConteudosByCategory();
            $cnts = $cnts['conteudosSeries'];
            foreach($cnts as $cat => $conteudo) {
                if(!sizeof($conteudo)) {
                    continue;
                }
                foreach($conteudo as $k => $x) {
                    $cont[$cat][] = $x;
                }
            }
        }

        return $cont;
    }

    public function getConteudosDesenhosPlanosByPackageCategory()
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            $cnts = $plan->getPlanAllConteudosByCategory();
            $cnts = $cnts['conteudosDesenhos'];
            foreach($cnts as $cat => $conteudo) {
                if(!sizeof($conteudo)) {
                    continue;
                }
                foreach($conteudo as $k => $x) {
                    $cont[$cat][] = $x;
                }
            }
        }

        return $cont;
    }


    public function getConteudosVODPlanos()
    {
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            if($plan->conteudo_vod == 1) {
                return true;
            }
        }
        return false;
    }

    public function getConteudosSeriesVODPlanos()
    {
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            if($plan->conteudo_series_vod == 1) {
                return true;
            }
        }
        return false;
    }

    public function getConteudosDesenhosVODPlanos()
    {
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            if(!$this->planIsValid($plan)){
                continue;
            }
            if($plan->conteudo_desenhos_vod == 1) {
                return true;
            }
        }
        return false;
    }

    public function getMainPlan()
    {
        $plans = $this->getSettingsConteudos('planos');
        if(!$plans) {
            return false;
        }

        $plan = false;
        foreach($plans as $planId) {
            $plan = Plan::find($planId);
            if(!$plan) {
                continue;
            }
            if(!$this->planIsValid($plan)){
                $plan = false;
                continue;
            }
            break;
        }

        return $plan;
    }

    public function getCustoCreditos()
    {
        if($this->status == 0) {
            return [];
        }

        $custoCreditoPacotes = [];
        $plans = $this->getSettingsConteudos('planos');

        foreach($plans as $planId) {
            $plan = Plan::find($planId);
            if(!$plan) {
                continue;
            }
            if(!$this->planIsValid($plan)){
                continue;
            }
            foreach($plan->getPackages() as $pkg) {
                if(!isset($custoCreditoPacotes[$pkg->id])) {
                    $custoCreditoPacotes[$pkg->id] = 0;
                }
                $custoCreditoPacotes[$pkg->id] += 1;
            }
        }

        return $custoCreditoPacotes;
    }

    public function resetPasswordToken()
    {
        // token gerado a partir do md5 da senha original do assinante
        // como teoricamente a senha original do assinante é desconhecida,
        // nao ha problemas com esse uso
        return md5($this->password);
    }

    public static function getIdPerfilAtual()
    {
        // alterar logica para retornar o id do perfil atual,
        // para que cada perfil de um mesmo usuario possa ter seus
        // proprios favoritos.
        
        // $sub = Auth::user();
        // if(!$sub) {
        //     return 0;
        // }

        // retornar perfil em uso
        // return $sub->id;

        if(!$idPerfil = request()->session()->get('id_perfil')) {
            return 0;
        }
        return $idPerfil;
    }

    public function getSettingsIugu($key)
    {
        $info = json_decode($this->settings_iugu);
        if($info == false) {
            if($key == 'customer' || $key == 'cards' || $key == 'subscription') {
                return [];
            }
            return false;
        }

        if(!isset($info->$key)) {
            if($key == 'customer' || $key == 'cards' || $key == 'subscription') {
                return [];
            }
            return false;
        }

        return $info->$key;
    }

    public function updateSettingsIugu(Array $requestInfo)
    {
        $info = json_decode($this->settings_iugu, true);
        if(!$info) {
            $info = [];
        }
        $info = array_replace_recursive($info, $requestInfo);

        $this->settings_iugu = json_encode($info);
        return true;
    }

    public function verifyEmail()
    {
        $d = new Datetime;
        $this->email_verified_at = $d->format('Y-m-d H:i:s');
        return $this->save();
    }

}
