<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class App extends Model
{
    use HasFactory; //, Notifiable;

    const DEFAULT_APP = 1;

    protected $table = 'tbApp';

    // protected $fillable = ['email', 'password'];

    public $timestamps = false;

    private static $bucket;

    public static function get($id)
    {
        $app = App::find($id);
        return $app;
    }


    public function getSettings($key)
    {
        //if(is_string($key)) {
        //  return $this->settings_conteudos[$key];
        //}
        $info = json_decode($this->settings);
        if($info == false) {
            return false;
        }
        if(!isset($info->$key)) {
            return false;
        }
        return $info->$key;
    }


    public function settingsHasBannerHome($id)
    {
        $bannersHome = $this->getSettings('banner_home');
        if(!$bannersHome) {
            return false;
        }
        return array_search($id, $bannersHome) !== false;
    }

    public function settingsHasBannerAovivo($id)
    {
        $bannersAovivo = $this->getSettings('banner_home_aovivo');
        if(!$bannersAovivo) {
            return false;
        }
        return array_search($id, $bannersAovivo) !== false;
    }


    public function settingsLogo($appendName = '')
    {
        if(!self::$bucket) {
            self::$bucket = DB::table('tbBuckets')->first();
        }

        $arquivo = DB::table('tbArquivos')->where('id', $this->logo_app)->first();

        if(!$arquivo) {
            return '';
        }

        return self::$bucket->UrlImg.$arquivo->url.$appendName.".".$arquivo->extensao;
        return self::$bucket->UrlImg.$arquivo;
    }

    public function settingsFavicon($appendName = '')
    {
        if(!self::$bucket) {
            self::$bucket = DB::table('tbBuckets')->first();
        }

        $arquivo = DB::table('tbArquivos')->where('id', $this->favicon_app)->first();

        if(!$arquivo) {
            return $this->settingsLogo();
        }

        return self::$bucket->UrlImg.$arquivo->url.$appendName.".".$arquivo->extensao;
    }

    public function settingsSignupEnabled()
    {
        $enabled = $this->getSettings('signup_enabled');
        if(!$enabled) {
            return false;
        }
        return $enabled;
    }

    public function settingsSignupPlan()
    {
        $plan = $this->getSettings('signup_plan');
        if(!$plan) {
            return false;
        }
        return $plan;
    }

    public function settingsDefaultSubscriber()
    {
        $subscriberId = $this->getSettings('default_subscriber_id');
        if(!$subscriberId) {
            return Assinante::DEFAULT_SUBSCRIBER_ID;
        }
        return $subscriberId;
    }

    public function settingsCdnIpList()
    {
        $cdnIp = $this->getSettings('cdn_ip_list');
        if(!$cdnIp || $cdnIp == '') {
            $cdnIp = SystemSettings::getSettings('cdn_default');
        }
        $cdnIp = explode(',', $cdnIp);
        return $cdnIp;
    }

    public function settingsCdnVodIpList()
    {
        $cdnIp = $this->getSettings('cdn_vod_ip_list');
        if(!$cdnIp || $cdnIp == '') {
            return false;
        }
        $ret = [];
        foreach(explode(PHP_EOL, $cdnIp) as $line) {
          $ret[] = explode(',', $line);
        }
        return $ret;
    }

    public function getCreditoPacotes()
    {
        $appid              = $this->id;
        $creditoPacotesList = [];
        $subscriberCredits  = [];

        $creditoPacotes = CreditoPacotes::byApp($appid);
        $pacotes        = Plan::packagesByApp($appid);

        foreach($pacotes as $pkg) {
            $tot = $pkg->creditosTotalApp($appid);
            $creditoPacotesList[$pkg->id] = array(
                'pacote'        => $pkg->nome,
                'total'         => $tot,
                'utilizado'     => 0,
                'disponivel'    => 0,
            );
        }

        $subscribers = Assinante::byAppToPaginate($appid)->where('status', 1)->get();
        foreach($subscribers as $sub) {
            foreach($sub->getCustoCreditos() as $pkgId => $tot) {
                if(!isset($creditoPacotesList[$pkgId])) {
                    $pkg = Pacote::find($pkgId);
                    $creditoPacotesList[$pkgId] = array('pacote' => $pkg->nome, 'total' => 0, 'utilizado' => 0, 'disponivel' => 0);
                }
                $creditoPacotesList[$pkgId]['utilizado'] += $tot;
            }
        }

        foreach($creditoPacotesList as $pkgId => $cpl) {
            $tt = $cpl['total'];
            $ut = $cpl['utilizado'];
            $creditoPacotesList[$pkgId]['disponivel'] = $tt - $ut;
        }

        return $creditoPacotesList;
    }

    public function settingsLojaEnabled()
    {
        $enabled = $this->getSettings('loja_enabled');
        if(!$enabled) {
            return false;
        }
        return $enabled;
    }

    public function settingsAovivoDisabled()
    {
        $disabled = $this->getSettings('aovivo_disabled');
        if($disabled) {
            return true;
        }
        return (bool) $disabled;
    }

}
/*

function setConfiguration()
{
    if($_SESSION['player'] == null) {
        return false;
    }

    $i = $_SESSION['player'];
    define('_CONF_APP_NAME',      $i['app_name']);
    define('_CONF_APP_ID',        $i['id']);
    define('_CONF_APP_ADDRESS',   $i['endereco']);
    define('_CONF_APP_TELEFONE',  $i['telefone']);
    define('_CONF_APP_LOGO',      'https://s3.amazonaws.com/'.$i['logo_app']);

    return true;
}
*/
