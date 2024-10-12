<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Conner\Likeable\Likeable;

class ConteudoSeriesEpisodios extends Model
{
  use HasFactory,
    Likeable;

  protected $table = 'tbConteudoSeriesEpisodios';
  public $timestamps = false;

  private static $bucket;

  const CONTEUDO_CATEGORIA_MOVIES = 1; // filmes
  const CONTEUDO_CATEGORIA_AOVIVO = 2; // ao vivo
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
      array_push($idsConteudo, $categoria->idRefConteudoSeries);
    }

    return ConteudoSeries::whereIn('id', $idsConteudo)->orderBy('id', 'desc')->where('status', '1')->get();
  }

  public static function destaqueHome($appId)
  {
    $app = App::get($appId);
    $bh = $app->getSettings('banner_home_series');
    $cnt = ConteudoSeries::where('destaqueHome', 1)->where('status', '1')->orderBy('id', 'desc');

    if (is_array($bh) && sizeof($bh)) {
      //dd($bh);
      $cnt = $cnt->whereIn('id', $bh);
      //$cnt = Conteudo::where('destaqueHome', 1)->where('status', '1')->where('id', $cnt)->orderBy('id', 'desc');
    }
    //dd($bh);
    return $cnt->get();
  }

  public function getSerie()
  {
    return ConteudoSeries::find($this->idRefConteudoSerie);
  }

  public function getEpisodios($id_serie)
  {
    return ConteudoSeriesEpisodios::where("idRefConteudoSerie", $id_serie)
      ->where("status", '1')
      ->orderby("temporada")
      ->orderby("episodio")
      ->get();
  }

  public function getFirstEp($id_serie)
  {
    if ($id_serie == null)
      return false;

    $ep = ConteudoSeriesEpisodios::where('idRefConteudoSerie', $id_serie)
      ->orderby('temporada')
      ->orderby('episodio')
      ->first();

    if ($ep == null)
      return false;

    return $ep->id;
  }

  public function getUrlWithCdnVod($app, $kind)
  {
    if (!in_array($kind, ['url_video', 'url_dash'])) {
      $kind = 'url_video';
    }
    $url = $this->$kind;

    if (!$cdnIps = $app->settingsCdnVodIpList()) {
      return $url;
    }

    foreach ($cdnIps as $cdnInfo) {
      $repDomains = $cdnInfo[0];
      $cdns = $cdnInfo[1];

      $repDomainsUrl = parse_url($repDomains,  PHP_URL_HOST);
      $repDomainsSch = parse_url($repDomains,  PHP_URL_SCHEME);
      $contentUrl = parse_url($url,  PHP_URL_HOST);
      $contentSch = parse_url($url,  PHP_URL_SCHEME);
      $cdnUrl     = parse_url($cdns, PHP_URL_HOST);
      $cdnSch     = parse_url($cdns, PHP_URL_SCHEME);

      if ($repDomainsUrl == $contentUrl) {
        $url = str_replace(
          sprintf('%s://%s', $contentSch, $contentUrl),
          sprintf('%s://%s', $cdnSch, $cdnUrl),
          $url
        );
      }
    }

    return $url;
  }
}
