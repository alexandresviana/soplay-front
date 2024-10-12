<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class AssinanteDBComets extends Authenticatable //Model
{
    use HasFactory;

    const DEFAULT_SUBSCRIBER_ID = 5;

    protected $connection = 'mysql_comets';
	protected $table = 'tbAssinantes';
	//public $timestamps = false;

	public $fillable = ['nome', 'email', 'password', 'app'];

    public $attributes = [
        'settings_conteudos' => '{"planos": [], "password_parental": false}',
    ];


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

    public function getConteudosPlanos()
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');

        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            $pkgs = $plan->getPackages();
            $cnts = $plan->getPlanConteudos();
            foreach($cnts as $conteudo) {
                array_push($cont, $conteudo);
            }
        }

        $cont = array_unique($cont);

        return $cont;
    }

    public function getAllConteudosPlanosByPackageCategory()
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            $cnts = $plan->getPlanAllConteudosByCategory();
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

    public function getConteudosPlanosByPackageCategory()
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            $cnts = $plan->getPlanAllConteudosByCategory();
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

    public function getConteudosCanaisPlanosByPackageCategory()
    {
        $cont = [];
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
            $cnts = $plan->getPlanAllConteudosByCategory();
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


    public function getConteudosVODPlanos()
    {
        $plansId = $this->getSettingsConteudos('planos');
        foreach($plansId as $pid) {
            $plan = Plan::find($pid);
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
            if($plan->conteudo_series_vod == 1) {
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

        if(!$plans[0]) {
            return false;
        }

        return Plan::find($plans[0]);
    }

}
