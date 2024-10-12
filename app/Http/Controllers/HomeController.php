<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Conteudo;
use App\Models\ConteudoSeries;
use App\Models\ConteudoCanais;
use App\Models\Arquivos;
use App\Models\Assinante;
use App\Models\Category;
use App\Models\App;
use App\Models\AssinantesCurrentTokenLogins;
use App\Models\BannerCustom;
use App\Services\PacotesServices;
use Cookie;

class HomeController extends Controller
{
  public function index()
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    $conteudosPlanos    = $sub->getConteudosPlanos('tv');
    $allConteudosPlanos = $sub->getAllConteudosPlanosByPackageCategory('tv');

    //$conteudo_aovivo_favoritos = ConteudoCanais::whereLikedBy($sub->id)->with('likeCounter')->get();


    $servicesPacotes = new PacotesServices($sub);
    $pacotes = $servicesPacotes->list();

    $indexedCategories = Category::indexedCategories();

    //$conteudos = Conteudo::all()->sortByDesc('id');
    $conteudo_movies = Conteudo::byCategoria(Conteudo::CONTEUDO_CATEGORIA_MOVIES)->limit(20)->get();

    //$conteudo_aovivo = ConteudoCanais::byCategoria(ConteudoCanais::CONTEUDO_CATEGORIA_AOVIVO)->limit(3)->get();
    $conteudo_recomendados = Conteudo::byCategoryAndPackage(Conteudo::CONTEUDO_CATEGORIA_RECOMENDADOS, $pacotes)
      ->get();

    $conteudo_favorito = Conteudo::favorito(Assinante::getIdPerfilAtual());

    $destaques_home_custom = BannerCustom::activeBanners($this->__subscriber()->app);
    $destaques_home = Conteudo::destaqueHome($this->__subscriber()->app);
    $destaques_home_aovivo = ConteudoCanais::destaqueHome($this->__subscriber()->app);

    $conteudo_aovivo = array_slice($conteudosPlanos, 0);

    $cAVF = [];
    // foreach ($conteudo_aovivo as $c) {
    //   foreach ($conteudo_aovivo_favoritos as $cfav) {
    //     if ($cfav->id == $c->id) {
    //       $cAVF[] = $cfav;
    //     }
    //   }
    // }
    $conteudo_aovivo_favoritos = $cAVF;

    $all_conteudo_by_category = $allConteudosPlanos;

    $app = App::get($sub->app);
    $currentOrder = $app->getSettings('canais_ordem');
    $conteudo_aovivo = ConteudoCanais::sortConteudoCanaisListUsingArray($conteudo_aovivo, $currentOrder);

    if (!$sub->getConteudosVODPlanos()) {
      $conteudo = [];
      $conteudo_movies = [];
      $conteudo_movies_teste = [];
    }

    if (!$sub->getConteudosVODPlanos()) {
      $conteudo_recomendados = [];
    }

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    $tmp = [];
    foreach ($destaques_home_custom as $dh) {
      $tmp[] = $dh;
    }
    foreach ($destaques_home as $dh) {
      $tmp[] = $dh;
    }
    $destaques_home = $tmp;

    $indice = request()->cookie('indice_banner');
    $time = 60 * 60 * 24 * 365;

    if (!isset($_COOKIE['indice_banner'])) {
      $indice = 0;
      Cookie::queue('indice_banner', $indice, $time);
    } else {
      if ((int)$indice < sizeof($destaques_home) - 1) {
        (int)$indice++;
        Cookie::queue('indice_banner', $indice, $time);
      } else {
        $indice = 0;
        Cookie::queue('indice_banner', $indice, $time);
      }
    }
    return view('home.index', ['conteudo_recomendados' => $conteudo_recomendados, 'conteudo_aovivo' => $conteudo_aovivo, 'conteudo_aovivo_favoritos' => $conteudo_aovivo_favoritos, 'all_conteudo_by_category' => $all_conteudo_by_category, 'conteudo_movies' => $conteudo_movies, 'categoria' => Conteudo::CONTEUDO_CATEGORIA_MOVIES, 'destaques_home' => $destaques_home, 'destaques_home_aovivo' => $destaques_home_aovivo, 'destaques_home_custom' => $destaques_home_custom, 'categorias_indexadas' => $indexedCategories, 'indice_banner' => $indice, 'conteudo_favorito' => $conteudo_favorito,]);
  }

  public function aoVivoRadioIndex(Request $request)
  {
    return $this->aoVivoIndex($request, 'radio');
  }

  public function aoVivoIndex(Request $request, $tipo = null)
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!in_array($tipo, array('tv', 'radio'))) {
      $tipo = 'tv';
    }

    $conteudosPlanos = $sub->getConteudosPlanos($tipo);

    $conteudosCanaisPlanos = $sub->getConteudosCanaisPlanosByPackageCategory($tipo);

    $conteudo_aovivo_favoritos = ConteudoCanais::whereLikedBy($sub->id)->with('likeCounter')->get();
    $cAVF = [];
    foreach ($conteudosPlanos as $c) {
      foreach ($conteudo_aovivo_favoritos as $cfav) {
        if ($cfav->id == $c->id) {
          $cAVF[] = $cfav;
        }
      }
    }
    $conteudo_aovivo_favoritos = $cAVF;

    //$i = array_search($conteudo, $conteudosPlanos);

    $app = App::get($sub->app);
    $currentOrder = $app->getSettings('canais_ordem');
    $conteudosPlanos = ConteudoCanais::sortConteudoCanaisListUsingArray($conteudosPlanos, $currentOrder);

    if (!isset($conteudosPlanos[0]) || $conteudosPlanos[0]->require_parental_password) {
      $conteudosPlanos[0] = new ConteudoCanais;
    }
    $conteudo = $conteudosPlanos[0];
    unset($conteudosPlanos[0]);

    AssinantesCurrentTokenLogins::updateDateActionForUserToken(request()->session()->get('current_token_login'), 'playing');

    return view('home.tvaovivo_index', ['conteudo' => $conteudo, 'conteudo_aovivo' => $conteudosPlanos, 'conteudo_aovivo_favoritos' => $conteudo_aovivo_favoritos, 'conteudos_ao_vivo_by_category' => $conteudosCanaisPlanos]);
  }

  public function soon()
  {
    $conteudos = Conteudo::all()->sortByDesc('id');

    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    $conteudosSeriesPlanos = $sub->getConteudosSeriesPlanosByPackageCategory();
    $allConteudosPlanos = $sub->getAllConteudosPlanosByPackageCategory();

    $indexedCategories = Category::indexedCategories();

    $conteudo_movies = Conteudo::byCategoria(Conteudo::CONTEUDO_CATEGORIA_MOVIES)->limit(20)->get();

    $conteudo_movies_teste = Conteudo::byCategoria(Conteudo::CONTEUDO_CATEGORIA_TESTE)->limit(20)->get();
    $destaques_home = Conteudo::destaqueHome($this->__subscriber()->app);
    $destaques_home_aovivo = ConteudoCanais::destaqueHome($this->__subscriber()->app);

    $conteudo_aovivo = $conteudosSeriesPlanos;
    $all_conteudo_by_category = $allConteudosPlanos;
    $conteudo_by_categorias = Conteudo::allByCategoria(10, true); // obtem TODOS os filmes por categorias

    $conteudos_series_by_category = ConteudoSeries::allByCategoria();

    if (!$sub->getConteudosVODPlanos()) {
      $conteudo = [];
      $conteudo_movies = [];
      $conteudo_movies_teste = [];
      $conteudo_by_categorias = [];
    }

    return view('home.soon_index', ['conteudo_aovivo' => $conteudo_aovivo, 'conteudos_series_by_category' => $conteudos_series_by_category, 'all_conteudo_by_category' => $all_conteudo_by_category, 'conteudo_by_categorias' => $conteudo_by_categorias, 'conteudo_movies' => $conteudo_movies, 'categoria' => 'Filmes', 'conteudo_movies_teste' => $conteudo_movies_teste, 'destaques_home' => $destaques_home, 'destaques_home_aovivo' => $destaques_home_aovivo, 'categorias_indexadas' => $indexedCategories]);
  }

  public function moviesIndex(Request $request)
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!$sub->getConteudosVODPlanos()) {
      return redirect('/');
    }

    $servicesPacotes = new PacotesServices($sub);
    $pacotes = $servicesPacotes->list();

    $tmpCatFav = new Category;
    $tmpCatFav->descricao = 'Favoritos';

    $tmpCatRent = new Category;
    $tmpCatRent->descricao = 'Disponível para aluguel';

    $indexedCategories = Category::indexedCategories('ordem');
    $indexedCategories['favorito'] = $tmpCatFav;
    $indexedCategories['aluguel'] = $tmpCatRent;

    $conteudosMoviesPlanos = $sub->getConteudosPlanosByPackageCategory();


    $conteudo_movies = Conteudo::byCategoryAndPackage(Conteudo::CONTEUDO_CATEGORIA_MOVIES, $pacotes)->orderByDesc('tbConteudo.id')->limit(20)->get();
    $conteudo_teste = Conteudo::byCategoryAndPackage(Conteudo::CONTEUDO_CATEGORIA_TESTE, $pacotes)->orderByDesc('tbConteudo.id')->limit(20)->get();
    $destaques_home = Conteudo::destaqueHome($this->__subscriber()->app);

    // variaveis com nomes semelhantes, teste para utilizar codigo base da view
    $conteudo_by_categorias = Conteudo::allByCategoriaByPackage(10, true, $pacotes); // obtem TODOS os filmes por categorias
    $conteudos_by_category = $conteudosMoviesPlanos; // sao somente filmes das categorias que eventualmente fazem parte de um pacote

    foreach ($conteudo_by_categorias as $k => $conteudos) {
      $contentList[$k] = $conteudos;
    }

    $retConteudos = [];
    foreach ($contentList as $k => $contents) {
      if (!$contents) {
        continue;
      }
      foreach ($contents as $content) {
        $retConteudos[$k][] = $content;
      }
    }

    $ret = [];
    foreach ($indexedCategories as $category) {
      $catId = $category->id;
      if (!isset($retConteudos[$catId])) {
        continue;
      }
      $ret[$catId] = @$retConteudos[$catId];
    }
    $conteudo_by_categorias = $ret;

    $favorito       = Conteudo::favorito(Assinante::getIdPerfilAtual());

    $cnt = [];
    if (count($favorito) > 0) {
      $cnt['favorito']                                = $favorito;
    }

    $conteudo_by_categorias = $cnt + $conteudo_by_categorias;

    if (!$sub->getConteudosVODPlanos()) {
      $conteudo_movies = [];
      $conteudo_teste = [];
      $conteudo_by_categorias = [];
      $destaques_home = [];
    }

    return view('home.movies_index', ['conteudo_movies' => $conteudo_movies, 'conteudos_movies_by_category' => $conteudos_by_category, 'conteudo_by_categorias' => $conteudo_by_categorias, 'destaques_home' => $destaques_home, 'conteudo_movies_teste' => $conteudo_teste, 'categorias_indexadas' => $indexedCategories]);
  }

  public function moviesIndexCategory(Request $request, $category)
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!$sub->getConteudosVODPlanos()) {
      return redirect('/');
    }

    $servicesPacotes = new PacotesServices($sub);
    $pacotes = $servicesPacotes->list();

    $tmpCatFav = new Category;
    $tmpCatFav->descricao = 'Favoritos';

    $tmpCatRent = new Category;
    $tmpCatRent->descricao = 'Disponível para aluguel';

    $indexedCategories = Category::indexedCategories();
    $indexedCategories['favorito'] = $tmpCatFav;
    $indexedCategories['aluguel'] = $tmpCatRent;

    if ($category == 'favorito') {
      $conteudo_movies = Conteudo::favorito(Assinante::getIdPerfilAtual());
    } elseif ($category == 'aluguel') {
      $conteudo_movies = Conteudo::aluguel($sub->app);
    } else {
      $conteudo_movies = Conteudo::byCategoryAndPackage($category, $pacotes)->get();
    }

    //$destaques_home = Conteudo::destaqueHome($this->__subscriber()->app);

    // variaveis com nomes semelhantes, teste para utilizar codigo base da view
    $conteudo_by_categorias = array($category => $conteudo_movies);
    $conteudos_by_category = []; // sao somente filmes das categorias que eventualmente fazem parte de um pacote

    //dd($conteudo_by_categorias);

    $indexedCategories[$category]->show_menu = 1;

    if (!$sub->getConteudosVODPlanos()) {
      $conteudo_movies = [];
      $conteudo_by_categorias = [];
      $destaques_home = [];
    }

    //return view('home.movies_index', ['conteudo_movies' => $conteudo_movies, 'conteudos_movies_by_category' => $conteudos_by_category, 'conteudo_by_categorias' => $conteudo_by_categorias, 'destaques_home' => $destaques_home, 'categorias_indexadas' => $indexedCategories]);
    return view('home.partials.filmes_grid', [
      'conteudo_movies' => $conteudo_movies,
      'conteudos_movies_by_category' => $conteudos_by_category,
      'conteudo_by_categorias' => $conteudo_by_categorias,
      'categorias_indexadas' => $indexedCategories
    ]);
  }

  public function moviesIndexFavorito(Request $request)
  {
    return $this->moviesIndexCategory($request, 'favorito');
  }

  public function moviesIndexAluguel(Request $request)
  {
    return $this->moviesIndexCategory($request, 'aluguel');
  }

  public function seriesIndex(Request $request)
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!$sub->getConteudosSeriesVODPlanos()) {
      return redirect('/');
    }

    $servicesPacotes = new PacotesServices($sub);
    $pacotes = $servicesPacotes->list();

    $conteudosSeriesPlanos = $sub->getConteudosSeriesPlanosByPackageCategory();

    $conteudo_series = ConteudoSeries::activeList();
    $conteudo_series_favoritos = ConteudoSeries::favorito(Assinante::getIdPerfilAtual());
    $destaques_home = ConteudoSeries::destaqueHome($this->__subscriber()->app);

    $conteudo_by_categorias = ConteudoSeries::allByCategoriaByPackage(10, true, $pacotes); // obtem TODOS os filmes por categorias
    $conteudos_by_category = $conteudosSeriesPlanos;

    $tmpCatFav = new Category;
    $tmpCatFav->descricao = 'Favoritos';

    $tmpCatRent = new Category;
    $tmpCatRent->descricao = 'Disponível para aluguel';

    $indexedCategories = Category::indexedCategories();
    $indexedCategories['favorito'] = $tmpCatFav;
    $indexedCategories['aluguel'] = $tmpCatRent;

    $conteudo_by_categorias = ['favorito' => $conteudo_series_favoritos] + ['aluguel' => ConteudoSeries::aluguel($sub->app)] + $conteudo_by_categorias;

    // dup
    if (!$sub->getConteudosSeriesVODPlanos()) {
      $conteudo_series = [];
      $conteudo_teste = [];
      $conteudo_by_categorias = [];
      $destaques_home = [];
    }

    return view('home.series_index', ['by_category' => false, 'conteudo_series' => $conteudo_series, 'conteudo_series_favoritos' => $conteudo_series_favoritos, 'conteudos_series_by_category' => $conteudos_by_category, 'conteudo_by_categorias' => $conteudo_by_categorias, 'destaques_home' => $destaques_home, 'categorias_indexadas' => $indexedCategories]);
  }

  public function seriesIndexCategory(Request $request, $category)
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!$sub->getConteudosSeriesVODPlanos()) {
      return redirect('/');
    }

    $servicesPacotes = new PacotesServices($sub);
    $pacotes = $servicesPacotes->list();

    $tmpCatFav = new Category;
    $tmpCatFav->descricao = 'Favoritos';

    $tmpCatRent = new Category;
    $tmpCatRent->descricao = 'Disponível para aluguel';

    $indexedCategories = Category::indexedCategories();
    $indexedCategories['favorito'] = $tmpCatFav;
    $indexedCategories['aluguel'] = $tmpCatRent;


    $conteudo_series_favoritos = ConteudoSeries::favorito(Assinante::getIdPerfilAtual());
    // $conteudo_series = ConteudoSeries::byCategoria($category); //->get();
    $destaques_home = ConteudoSeries::destaqueHome($this->__subscriber()->app);

    if ($category == 'favorito') {
      $conteudo_series = ConteudoSeries::favorito(Assinante::getIdPerfilAtual());
      $conteudo_series_favoritos = [];
    } elseif ($category == 'aluguel') {
      $conteudo_series = ConteudoSeries::aluguel($sub->app);
      $conteudo_series_favoritos = [];
    } else {
      $conteudo_series = ConteudoSeries::byCategoryAndPackage($category, $pacotes)->get();
    }

    //$conteudo_series = ConteudoSeries::find($category)->getEpisodios->get();

    // variaveis com nomes semelhantes, teste para utilizar codigo base da view
    $conteudo_by_categorias = array($category => $conteudo_series);
    $conteudos_by_category = []; // sao somente filmes das categorias que eventualmente fazem parte de um pacote

    //dd($conteudo_by_categorias);

    // dup
    if (!$sub->getConteudosSeriesVODPlanos()) {
      $conteudo_series = [];
      $conteudo_by_categorias = [];
      $destaques_home = [];
    }

    return view('home.series_index', ['by_category' => $category, 'conteudo_series' => $conteudo_series, 'conteudo_series_favoritos' => $conteudo_series_favoritos, 'conteudos_series_by_category' => $conteudos_by_category, 'conteudo_by_categorias' => $conteudo_by_categorias, 'destaques_home' => $destaques_home, 'categorias_indexadas' => $indexedCategories]);
  }

  public function seriesIndexFavorito(Request $request)
  {
    return $this->seriesIndexCategory($request, 'favorito');
  }

  public function seriesIndexAluguel(Request $request)
  {
    return $this->seriesIndexCategory($request, 'aluguel');
  }

  public function seriesDesenhosIndex(Request $request)
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!$sub->getConteudosDesenhosVODPlanos()) {
      return redirect('/');
    }

    $conteudosSeriesPlanos = $sub->getConteudosSeriesPlanosByPackageCategory();

    $conteudo_series = ConteudoSeries::activeListDesenho();
    $destaques_home = ConteudoSeries::destaqueHomeDesenho($this->__subscriber()->app);

    $conteudo_by_categorias = ConteudoSeries::allByCategoriaDesenho(50, true); // obtem TODOS os filmes por categorias
    $conteudos_by_category = $conteudosSeriesPlanos;

    $indexedCategories = Category::indexedCategories();

    // dup
    if (!$sub->getConteudosDesenhosVODPlanos()) {
      $conteudo_series = [];
      $conteudo_teste = [];
      $conteudo_by_categorias = [];
      $destaques_home = [];
    }


    return view('home.series_index', ['conteudo_series' => $conteudo_series, 'conteudos_series_by_category' => $conteudos_by_category, 'conteudo_by_categorias' => $conteudo_by_categorias, 'destaques_home' => $destaques_home, 'categorias_indexadas' => $indexedCategories]);
  }

  public function seriesDesenhosIndexCategory(Request $request, $category)
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!$sub->getConteudosDesenhosVODPlanos()) {
      return redirect('/');
    }

    $indexedCategories = Category::indexedCategories();

    $conteudo_series = ConteudoSeries::byCategoria($category); //->get();
    $destaques_home = ConteudoSeries::destaqueHomeDesenho($this->__subscriber()->app);

    //$conteudo_series = ConteudoSeries::find($category)->getEpisodios->get();

    // variaveis com nomes semelhantes, teste para utilizar codigo base da view
    $conteudo_by_categorias = array($category => $conteudo_series);
    $conteudos_by_category = []; // sao somente filmes das categorias que eventualmente fazem parte de um pacote

    //dd($conteudo_by_categorias);

    // dup
    if (!$sub->getConteudosDesenhosVODPlanos()) {
      $conteudo_series = [];
      $conteudo_by_categorias = [];
      $destaques_home = [];
    }

    return view('home.series_index', ['conteudo_series' => $conteudo_series, 'conteudos_series_by_category' => $conteudos_by_category, 'conteudo_by_categorias' => $conteudo_by_categorias, 'destaques_home' => $destaques_home, 'categorias_indexadas' => $indexedCategories]);
  }

  public function lojaIndex(Request $request)
  {
    $category1 = 'aluguel_movies';
    $category2 = 'aluguel_series';
    $sub = $this->__subscriber();
    $app = $this->__app();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!$app->settingsLojaEnabled()) {
      return redirect('/');
    }

    if (!$sub->getConteudosVODPlanos() && !$sub->getConteudosDesenhosVODPlanos() && !$sub->getConteudosSeriesVODPlanos()) {
      return redirect('/');
    }

    $tmpCatRent1 = new Category;
    $tmpCatRent1->descricao = 'Filmes para aluguel';

    $tmpCatRent2 = new Category;
    $tmpCatRent2->descricao = 'Séries para aluguel';

    $tmpCatRent3 = new Category;
    $tmpCatRent3->descricao = 'Aluguel';

    $indexedCategories = Category::indexedCategories();
    $indexedCategories['aluguel_movies'] = $tmpCatRent1;
    $indexedCategories['aluguel_series'] = $tmpCatRent2;
    $indexedCategories['aluguel'] = $tmpCatRent3;

    $conteudo_movies = Conteudo::aluguel($sub->app);
    $conteudo_series = ConteudoSeries::aluguel($sub->app);

    // variaveis com nomes semelhantes, teste para utilizar codigo base da view
    $conteudo_by_categorias = array($category1 => $conteudo_movies, $category2 => $conteudo_series);
    $conteudos_by_category = []; // sao somente filmes das categorias que eventualmente fazem parte de um pacote

    $indexedCategories[$category1]->show_menu = 1;
    $indexedCategories[$category2]->show_menu = 1;

    if (!$sub->getConteudosVODPlanos()) {
      $conteudo_movies = [];
      $conteudo_by_categorias = [];
      $destaques_home = [];
    }

    return view('home.loja_index', [
      'conteudo_movies' => $conteudo_movies,
      'conteudos_movies_by_category' => $conteudos_by_category,
      'conteudo_by_categorias' => $conteudo_by_categorias,
      'categorias_indexadas' => $indexedCategories
    ]);
  }



  public function favorite(Request $request)
  {
    $type = $request->get('type');
    $id   = $request->get('id');

    $usr = $this->__subscriber();

    switch ($type) {
      case 'aovivo':
        $content = ConteudoCanais::find($id);
        //$content->unlike($usr->id);
        //$content->like();
        $msg = $this->_toggleLike($content);
        break;

      case 'movie':
        $content = Conteudo::find($id);
        $msg = $this->_toggleLike($content);
        break;

      case 'serie':
        $content = ConteudoSeries::find($id);
        $msg = $this->_toggleLike($content);
        break;

      default:
        break;
    }

    return response()->json($msg, 200);
    dd($type);
  }


  private function _toggleLike($content)
  {
    $perfilAtual = Assinante::getIdPerfilAtual();
    if (!$perfilAtual) {
      return array('status' => false, 'msg' => 'Selecione um perfil');
    }

    if ($content->liked($perfilAtual)) {
      $content->unlike($perfilAtual);
      return array('status' => true, 'act' => 'unlike');
    }
    $content->like($perfilAtual);
    return array('status' => true, 'act' => 'like');
  }

  public function appIos(Request $request)
  {
    $m = 60 * 60 * 24 * 365;
    Cookie::queue('OS', 'ios', $m);
    return redirect('/');
  }

  public function appAndroid(Request $request)
  {
    $m = 60 * 60 * 24 * 365;
    Cookie::queue('OS', 'android', $m);
    return redirect('/');
  }

  public function privacidade(Request $request)
  {
    $appNome = $this->__app()->app_nome;
    $razao_social = $this->__app()->razao_social;

    return view('home.privacidade', ['appNome' => $appNome, 'razao_social' => $razao_social]);
  }

  public function privacidade_kids(Request $request)
  {
    $appNome = $this->__app()->app_nome;
    $razao_social = $this->__app()->razao_social;

    return view('home.privacidade_kids', ['appNome' => $appNome, 'razao_social' => $razao_social]);
  }

  public function suporte(Request $request)
  {
    $appNome = $this->__app()->app_nome;
    $appEmail = $this->__app()->set_email_suporte;
    $appTelefone = $this->__app()->set_telefone_suporte;
    $appWhatsapp = $this->__app()->set_whatsapp_suporte;
    $razao_social = $this->__app()->razao_social;

    return view(
      'home.suporte',
      [
        'appNome' => $appNome,
        'appEmail' => $appEmail,
        'appTelefone' => $appTelefone,
        'appWhatsapp' => $appWhatsapp,
      ]
    );
  }

  public function showQRCode(Request $request)
  {
    $app = $this->__app();

    $rt = route('assinante.new');
    $url = sprintf('%s', $rt);

    return view('home.show_qrcode', ['qrcode_text' => $url]);
  }

  public function noPlan(Request $request)
  {
    // request()->session()->flash('no_main_plan', 'No momento não há um plano de acesso válido. Atualize as informações.');
    // return redirect(route('checkout.index'));
    request()->session()->flash('no_main_plan', 'Usuário sem plano, por favor entre em contato com o seu provedor');

    return view('home.noplan');
  }
}
