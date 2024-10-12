<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Conner\Likeable\Likeable;
use Illuminate\Http\Request;


class ConteudoCanais extends Model
{
  use HasFactory,
    Likeable;

  protected $table = 'tbConteudoCanais';
  public $timestamps = false;

  private static $bucket;

  const CONTEUDO_CATEGORIA_MOVIES = 1; // filmes
  const CONTEUDO_CATEGORIA_AOVIVO = 5; // ao vivo
  const CONTEUDO_CATEGORIA_TESTE  = 4; // teste


  public function getImagemFullUrl($appendName = '')
  {
    if (!self::$bucket) {
      self::$bucket = DB::table('tbBuckets')->first();
    }

    $arquivo = DB::table('tbArquivos')->where('id', $this->imagem)->first();

    if (!$arquivo) {
      return '';
    }

    return self::$bucket->UrlImg . $arquivo->url . $appendName . "." . $arquivo->extensao;
  }

  public function getImagemBannerFullUrl($appendName = '')
  {
    if (!self::$bucket) {
      self::$bucket = DB::table('tbBuckets')->first();
    }

    $arquivo = DB::table('tbArquivos')->where('id', $this->imagem_banner)->first();

    if (!$arquivo) {
      return '';
    }

    return self::$bucket->UrlImg . $arquivo->url . $appendName . "." . $arquivo->extensao;
  }

  public static function byCategoria($categoria)
  {
    $idsConteudo = [-1];
    $categorias = DB::table('tbConteudoCatSub')->where('idRefCategoria', $categoria)->get();
    foreach ($categorias as $categoria) {
      array_push($idsConteudo, $categoria->idRefConteudoCanais);
    }

    return ConteudoCanais::whereIn('id', $idsConteudo)->orderBy('id', 'desc')->where('status', '1');
  }

  public static function destaqueHome($appId)
  {
    $app = App::get($appId);
    $bh = $app->getSettings('banner_home_aovivo');
    $cnt = ConteudoCanais::where('destaqueHome', 1)->where('status', '1')->orderBy('id', 'desc');

    if (is_array($bh) && sizeof($bh)) {
      //dd($bh);
      $cnt = $cnt->whereIn('id', $bh);
      //$cnt = Conteudo::where('destaqueHome', 1)->where('status', '1')->where('id', $cnt)->orderBy('id', 'desc');
    } else {
      $cnt = $cnt->where('id', '-1');
    }
    //dd($bh);
    return $cnt->get();
  }


  public static function sortConteudoCanaisListUsingArray($conteudoCanaisList, $sortList)
  {
    if (!is_array($sortList)) {
      $sortList = [];
    }

    $cclIndexed = [];
    $cclOrdered = [];

    foreach ($conteudoCanaisList as $cc) {
      $cclIndexed[$cc->id] = $cc;
    }

    foreach ($sortList as $cid) {
      $cid = (int) $cid;
      if (!isset($cclIndexed[$cid])) {
        continue;
      }
      $cclOrdered[] = $cclIndexed[$cid];
      unset($cclIndexed[$cid]);
    }

    foreach ($cclIndexed as $cc) {
      $cclOrdered[] = $cc;
    }

    return $cclOrdered;
  }

  public function getUrlWithCdn($app, $drm = false)
  {
    $url = null;
    if ($drm)
      $url = $this->url_dash;
    else
      $url = $this->url_video;

    if (!$cdnIp = $app->settingsCdnIpList()) {
      return $url;
    }

    $repDomains = SystemSettings::getSettings('cdn_replace_domains');
    $repDomains = explode(',', $repDomains);

    //$url = $this->url_hls;

    $cdns = $cdnIp[0];

    $contentUrl = parse_url($url,  PHP_URL_HOST);
    $contentSch = parse_url($url,  PHP_URL_SCHEME);
    $cdnUrl     = parse_url($cdns, PHP_URL_HOST);
    $cdnSch     = parse_url($cdns, PHP_URL_SCHEME);

    if (!in_array($contentUrl, $repDomains)) {
      return $url;
    }

    $retUrl = str_replace(
      sprintf('%s://%s', $contentSch, $contentUrl),
      sprintf('%s://%s', $cdnSch, $cdnUrl),
      $url
    );

    return $retUrl;
  }

  public function getUrlWithToken(Request $request, $app)
  {
    $url = $this->getUrlWithCdn($app, true);

    // verifica se requisicao vem de ipv4 ou 6, sera usado pra direcionar
    // requisicao para cdn 4 ou 6, conforme disponibilidade (cadastrada)
    $requestIp     = $request->ip();
    $requestIp     = '::1'; // simula acesso via ipv6
    $requestIsIpv4 = (strlen(inet_pton($requestIp))) == 4;
    $requestIsIpv6 = (strlen(inet_pton($requestIp))) == 16;

    $cdnAddress = false;
    if ($cdnInfo = $app->settingsCdnIpList()) {
      $cdnInfo4    = $cdnInfo[1];
      $cdnInfo6    = @$cdnInfo[2];
      $cdnAddress6 = null;

      $cdnAddress = parse_url($cdnInfo4, PHP_URL_HOST);

      if ($cdnInfo6) { // ipv6, parse_url nao funciona para 6, endereço deve estar cadastrado corretamente cdn_info osm-admin
        $tmp = str_replace('https://', '', $cdnInfo6);
        $tmp = str_replace('http://', '', $tmp);
        $cdnAddress6 = $tmp;
      }

      if ($requestIsIpv6 && $cdnAddress6 != null) {
        $cdnAddress = $cdnAddress6;
      }

      if ($cdnAddress == null && $cdnAddress6 != null) {
        $cdnAddress = $cdnAddress6;
      }
    }


    $set         = SystemSettings::getSettings('flussonic_1');
    $key        = $set; // The key from flussonic.conf file. KEEP IT IN SECRET.
    $lifetime    = 3600 * 12;     // The link will become invalid in 3 hours.
    $stream       = current(array_filter(explode("/", parse_url($url, PHP_URL_PATH))));

    //echo $stream;

    //$ipaddr		= $request->ip(); // (v20.07) Set $ipaddr = 'no_check_ip' if you want to exclude IP address of client devices from checking.
    $ipaddr     = 'no_check_ip'; // (v20.07) Set $ipaddr = 'no_check_ip' if you want to exclude IP address of client devices from checking.
    $desync     = 300; // Allowed time desync between Flussonic and hosting servers in seconds.
    $starttime     = time() - $desync;
    $endtime     = $starttime + $lifetime;
    $salt         = bin2hex(openssl_random_pseudo_bytes(16));

    if ($cdnAddress) {
      $ipaddr = $cdnAddress;
    }

    $hashsrt     = $stream . $ipaddr . $starttime . $endtime . $key . $salt;
    $hash         = sha1($hashsrt);

    $token         = $hash . '-' . $salt . '-' . $endtime . '-' . $starttime;

    $ret = sprintf('%s?token=%s', $url, $token, $ipaddr);
    return $ret;
  }

  public static function pesquisa($string, $pacoteList)
  {
    $filmesAluguel = [];
    foreach (DB::select('select id_conteudo from tbConteudoDisponivelAluguel where tipo_conteudo = "conteudo"') as $item) {
      $filmesAluguel[] = $item->id_conteudo;
    }

    $seriesAluguel = [];
    foreach (DB::select('select id_conteudo from tbConteudoDisponivelAluguel where tipo_conteudo = "conteudo_series"') as $item) {
      $seriesAluguel[] = $item->id_conteudo;
    }

    if (strlen($string) >= 3) {

      $retorno = [];
      $contador = 0;

      $canais = DB::table('tbConteudoCanais')->where('titulo', "LIKE", sprintf('%%%s%%', $string))->where('status', "1")->get()->toArray();
      $filmes = DB::table('tbConteudo')->where('titulo', "LIKE", sprintf('%%%s%%', $string))->where('status', "1")->whereNotIn('id', $filmesAluguel)->get()->toArray();
      $series = DB::table('tbConteudoSeries')->where('titulo', "LIKE", sprintf('%%%s%%', $string))->where('status', "1")->whereNotIn('id', $seriesAluguel)->get()->toArray();
      return array_merge($filmes, $series);
    } else {
      return null;
    }
  }

  public static function getImageById($id = null)
  {

    if ($id != null) {

      if (!self::$bucket) {
        self::$bucket = DB::table('tbBuckets')->first();
      }

      $arquivo = DB::table('tbArquivos')->where('id', $id)->first();

      if (!$arquivo) {
        return '';
      }

      return self::$bucket->UrlImg . $arquivo->url . "." . $arquivo->extensao;
    } else {
      return "https://imagens.nxplay.com.br/60a18c63ab005_7e22ea56f3972696631ce35db1b2bcaa.jpg";
    }
  }
}
