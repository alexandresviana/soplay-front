<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Conner\Likeable\Likeable;

use \Datetime;

class Conteudo extends Model
{
  use HasFactory,
    Likeable;

  protected $table = 'tbConteudo';
  public $timestamps = false;

  private static $bucket;

  protected $with = ['file'];

  const CONTEUDO_CATEGORIA_MOVIES = 1; // filmes
  const CONTEUDO_CATEGORIA_AOVIVO = 2; // ao vivo
  const CONTEUDO_CATEGORIA_TESTE  = 4; // teste

  const CONTEUDO_CATEGORIA_RECOMENDADOS = 266; // recomendados, no campo id_importacao

  const CONTEUDO_CATEGORIA_ACAO         = 255; // acao
  const CONTEUDO_CATEGORIA_AVENTURA     = 262; // aventura
  const CONTEUDO_CATEGORIA_BIOGRAFIA    = 261; // biografia
  const CONTEUDO_CATEGORIA_COMEDIA      = 277; // comedia
  const CONTEUDO_CATEGORIA_DRAMA        = 258; // drama
  const CONTEUDO_CATEGORIA_FAROESTE     = 264; // faroeste
  const CONTEUDO_CATEGORIA_FICCAO       = 270; // ficcao
  const CONTEUDO_CATEGORIA_ROMANCE      = 256; // romance
  const CONTEUDO_CATEGORIA_SUSPENSE     = 260; // suspense
  const CONTEUDO_CATEGORIA_TERROR       = 279; // terror
  const CONTEUDO_CATEGORIA_ADULTO       = 347; // adulto
  const CONTEUDO_CATEGORIA_RECENTE      = 344; // Adicionados recentemente


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

  public function file()
  {
    return $this->belongsTo(\App\Models\Arquivos::class, 'imagem', 'id');
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

  public function getTrailerUrl($appendName = '')
  {
    if (!self::$bucket) {
      self::$bucket = DB::table('tbBuckets')->first();
    }

    $arquivo = DB::table('tbArquivos')->where('id', $this->trailer)->first();

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
      array_push($idsConteudo, $categoria->idRefConteudo);
    }

    return Conteudo::whereIn('id', $idsConteudo)->orderBy('ordem')->where('status', '1');
  }

  public static function byCategoryAndPackage($categoria, $pacoteList)
  {
    $idsConteudo = [-1];
    return Conteudo::select('tbConteudo.*')
      ->join('tbConteudoCatSub', 'tbConteudoCatSub.idRefConteudo', '=', 'tbConteudo.id')
      ->leftJoin('pacotes_vod', 'pacotes_vod.conteudo_id', '=', 'tbConteudo.id')
      ->where('idRefCategoria', $categoria)
      ->whereIn('pacotes_vod.pacote_id', $pacoteList)
      ->where('status', '1')
      ->where('disponivel_aluguel', '0')
      ->orderBy('ordem');
    // foreach ($categorias as $categoria) {
    //     array_push($idsConteudo, $categoria->idRefConteudo);
    // }

    // return Conteudo::whereIn('id', $idsConteudo)->orderBy('ordem')->where('status', '1');
  }

  public static function favorito($idPerfil)
  {
    $idsConteudo = [-1];

    return Conteudo::where('status', '1')->whereLikedBy($idPerfil)->with('likeCounter')->get();
  }

  public static function aluguel($appId)
  {
    $idsConteudo = [-1];

    $rentList = ConteudoDisponivelAluguel::where('tipo_conteudo', 'conteudo')
      ->where('ativo', 1)
      ->whereRaw("(FIND_IN_SET(?, apps_disponiveis_list) > 0 or FIND_IN_SET(?, apps_disponiveis_list) > 0)", [$appId, '*'])
      ->whereRaw("(not FIND_IN_SET(?, apps_indisponiveis_list) or apps_indisponiveis_list is null)", [$appId])
      ->get();

    foreach ($rentList as $cont) {
      $idsConteudo[] = $cont->id_conteudo;
    }

    return Conteudo::where('status', '1')
      ->whereIn('id', $idsConteudo)
      ->orderBy('ordem', 'ASC')
      ->get();
  }

  /**
   * @deprecated
   */
  public static function allByCategoria($limit = null, $indexedById = false)
  {
    $conteudoCategoria = [];
    $categorias = [];
    $conteudos = [];
    foreach (DB::table('tbConteudoCatSub')->where('idRefConteudo', '<>', null)->get() as $item) {
      $conteudoCategoria[$item->idRefConteudo][] = $item->idRefCategoria;
    }
    foreach (DB::table('tbCategorias')->get() as $item) {
      if ($item->show_menu != 1) {
        continue;
      }
      $categoria[$item->id] = $item->descricao;
    }

    $contentList = Conteudo::where('status', '1')
      ->orderBy('ordem');

    // limitacao de conteudo esta sendo feito apos buscar todo o conteudo,
    // limitar na busca em vez de buscar tudo
    foreach ($contentList->get() as $item) {
      if (!isset($conteudoCategoria[$item->id])) {
        continue;
      }
      $cats = $conteudoCategoria[$item->id];
      foreach ($cats as $cat) {
        if (!isset($categoria[$cat])) {
          continue;
        }
        $catDesc = $categoria[$cat];
        if ($indexedById) {
          $catDesc = $cat;
        }

        if ($limit && isset($conteudos[$catDesc]) && sizeof($conteudos[$catDesc]) >= $limit) {
          continue;
        }

        $conteudos[$catDesc][] = $item;
      }
    }
    //ksort($conteudos, SORT_STRING);
    return $conteudos;
  }

  public static function allByCategoriaByPackage($limit = null, $indexedById = false, array $package = [])
  {
    $conteudoCategoria = [];
    $categorias = [];
    $conteudos = [];
    foreach (DB::table('tbConteudoCatSub')->where('idRefConteudo', '<>', null)->get() as $item) {
      $conteudoCategoria[$item->idRefConteudo][] = $item->idRefCategoria;
    }
    foreach (DB::table('tbCategorias')->get() as $item) {
      if ($item->show_menu != 1) {
        continue;
      }
      $categoria[$item->id] = $item->descricao;
    }

    $contentList = Conteudo::select('tbConteudo.*')
      ->where('status', '1')
      ->where('disponivel_aluguel', '0')
      ->leftJoin('pacotes_vod', 'pacotes_vod.conteudo_id', '=', 'tbConteudo.id')
      ->whereIn('pacotes_vod.pacote_id', $package)
      ->orderBy('ordem');

    // limitacao de conteudo esta sendo feito apos buscar todo o conteudo,
    // limitar na busca em vez de buscar tudo
    foreach ($contentList->get() as $item) {
      if (!isset($conteudoCategoria[$item->id])) {
        continue;
      }
      $cats = $conteudoCategoria[$item->id];
      foreach ($cats as $cat) {
        if (!isset($categoria[$cat])) {
          continue;
        }
        $catDesc = $categoria[$cat];
        if ($indexedById) {
          $catDesc = $cat;
        }

        if ($limit && isset($conteudos[$catDesc]) && sizeof($conteudos[$catDesc]) >= $limit) {
          continue;
        }

        $conteudos[$catDesc][] = $item;
      }
    }
    //ksort($conteudos, SORT_STRING);
    return $conteudos;
  }

  public static function destaqueHome($appId)
  {
    $app = App::get($appId);
    $bh = $app->getSettings('banner_home');
    $cnt = Conteudo::where('destaqueHome', 1)
      ->where('status', '1')
      ->where('disponivel_aluguel', '0')
      ->orderBy('id', 'desc');

    if (is_array($bh) && sizeof($bh)) {
      //dd($bh);
      $cnt = $cnt->whereIn('id', $bh);
      //$cnt = Conteudo::where('destaqueHome', 1)->where('status', '1')->where('id', $cnt)->orderBy('id', 'desc');
    } else {
      $cnt = $cnt->where('id', '-1');
    }
    return $cnt->get();
  }

  public function findAvailableRent($appId)
  {
    $rent = ConteudoDisponivelAluguel::where('tipo_conteudo', 'conteudo')
      ->where('id_conteudo', $this->id)
      ->where('ativo', 1)
      ->whereRaw("(FIND_IN_SET(?, apps_disponiveis_list) > 0 or FIND_IN_SET(?, apps_disponiveis_list) > 0)", [$appId, '*'])
      ->whereRaw("(not FIND_IN_SET(?, apps_indisponiveis_list) or apps_indisponiveis_list is null)", [$appId])
      ->first();
    return $rent;
  }

  public function findActiveRentForSubscriber($subscriberId)
  {
    $dStart = new Datetime;

    $rent = AssinantesConteudoAlugado::where('tipo_conteudo', 'conteudo')
      ->where('id_conteudo', $this->id)
      ->where('id_assinante', $subscriberId)
      ->where('data_inicio', '<=', $dStart->format('Y-m-d H:i:s'))
      ->where('data_fim', '>=', $dStart->format('Y-m-d H:i:s'))
      ->where('ativo', 1)
      ->first();
    return $rent;
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


  public function getUrlWithToken(Request $request)
  {
    $taSettings = SystemSettings::getSettings('token_api_settings');
    $taSettings = json_decode($taSettings);

    $apiKey = $taSettings->api_key;
    $apiUrl = $taSettings->url;

    // todo: implementar

    return $this->url_video;
  }
}
