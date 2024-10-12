<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Conteudo;
use App\Models\ConteudoCanais;
use App\Models\ConteudoSeries;
use App\Models\ConteudoSeriesEpisodios;
use App\Models\AssinantesCurrentTokenLogins;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
  public function showVideo($id)
  {
    $conteudo = Conteudo::find($id);
    $conteudo_home = Conteudo::find($id);

    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    if (!$sub->getConteudosVODPlanos()) {
      return redirect('/');
    }

    $userOr = session()->get('id_perfil') ? session()->get('id_perfil') : $sub->id;

    $allConteudosPlanos = $sub->getAllConteudosPlanosByPackageCategory();
    $all_conteudo_by_category = $allConteudosPlanos;

    $indexedCategories = Category::indexedCategories();

    $conteudo_movies = Conteudo::byCategoria(Conteudo::CONTEUDO_CATEGORIA_MOVIES)->limit(20)->get();


    $conteudo_by_categorias = Conteudo::allByCategoria(10, true); // obtem TODOS os filmes por categorias

    if (!$sub->getConteudosVODPlanos()) {
      $conteudo_by_categorias = [];
    }

    AssinantesCurrentTokenLogins::updateDateActionForUserToken(request()->session()->get('current_token_login'), 'playing');

    return view('video.show_video', [
      'userEvent' => $userOr,
      'conteudo' => $conteudo, 'conteudo_home' => $conteudo_home, 'conteudo_by_categorias' => $conteudo_by_categorias, 'categorias_indexadas' => $indexedCategories, 'all_conteudo_by_category' => $all_conteudo_by_category, 'token' => 'tok'
    ]);
  }

  public function showVideoAovivo($id)
  {
    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    $conteudo = ConteudoCanais::find($id);

    $conteudosPlanos = $sub->getConteudosPlanos();
    $conteudos = $conteudosPlanos;

    if (!$conteudo || !sizeof($conteudosPlanos)) {
      return redirect('/');
    }

    $conteudosCanaisPlanos = $sub->getConteudosCanaisPlanosByPackageCategory();

    $conteudo_aovivo_favoritos = ConteudoCanais::whereLikedBy($sub->id)->with('likeCounter')->get();
    $cAVF = [];
    foreach ($conteudos as $c) {
      foreach ($conteudo_aovivo_favoritos as $cfav) {
        if ($cfav->id == $c->id) {
          $cAVF[] = $cfav;
        }
      }
    }
    $conteudo_aovivo_favoritos = $cAVF;

    AssinantesCurrentTokenLogins::updateDateActionForUserToken(request()->session()->get('current_token_login'), 'playing');

    return view('home.tvaovivo_index', ['conteudo' => $conteudo, 'conteudo_aovivo' => $conteudos, 'conteudo_aovivo_favoritos' => $conteudo_aovivo_favoritos, 'conteudos_ao_vivo_by_category' => $conteudosCanaisPlanos]);

    #return view('video.show_video_ao_vivo', ['conteudo_aovivo' => $conteudos, 'conteudo_aovivo_favoritos' => $conteudo_aovivo_favoritos, 'conteudo' => $conteudo, 'token' => 'tok']);
  }

  public function showVideoSeries($id)
  {
    $conteudo = ConteudoSeries::find($id);
    $destaques_home = [$conteudo];
    $conteudo_series_episodios = $conteudo->getEpisodios();
    //dd($conteudo);
    #return view('video.show_video_series', ['conteudo' => $conteudo, 'token' => 'tok']);

    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    switch ($conteudo->tipo) {
      case 'serie':
        if (!$sub->getConteudosSeriesVODPlanos()) {
          $conteudo_by_categorias = [];
          return redirect('/');
        }
        break;
      case 'desenho':
        if (!$sub->getConteudosDesenhosVODPlanos()) {
          $conteudo_by_categorias = [];
          return redirect('/');
        }
        break;
    }

    return view('video.series_episodios', ['conteudo' => $conteudo, 'destaques_home' => $destaques_home, 'conteudo_series_episodios' => $conteudo_series_episodios, 'token' => 'tok']);
  }

  public function showVideoSeriesPlay($id)
  {
    $conteudoSeries = new ConteudoSeriesEpisodios();
    $conteudo_selecionado = ConteudoSeriesEpisodios::find($id);

    $episodios = $conteudoSeries->getEpisodios($conteudo_selecionado->idRefConteudoSerie);
    $playlist = [];

    foreach ($episodios as $key => $ep) :
      $playlist[$key]['name'] = $ep->titulo;
      $playlist[$key]['description'] = $ep->descricao;
      $playlist[$key]['hls'] = $ep->url_video;
      $playlist[$key]['legenda_pt'] = $ep->legenda_pt;
      $playlist[$key]['id'] = $ep->id;
      $playlist[$key]['id_serie'] = $conteudo_selecionado->idRefConteudoSerie;
      $playlist[$key]['temporada'] = $ep->temporada;
    endforeach;


    $destaques_home = [$conteudo_selecionado];
    $conteudo_series_episodios = $conteudo_selecionado->getSerie()->getEpisodios();

    $sub = $this->__subscriber();

    if (!$sub->getMainPlan()) {
      return redirect(route('home.noplan'));
    }

    $serie = $conteudo_selecionado->getSerie();

    switch ($serie->tipo) {
      case 'serie':
        if (!$sub->getConteudosSeriesVODPlanos()) {
          $conteudo_by_categorias = [];
          return redirect('/');
        }
        break;
      case 'desenho':
        if (!$sub->getConteudosDesenhosVODPlanos()) {
          $conteudo_by_categorias = [];
          return redirect('/');
        }
        break;
    }

    $userOr = session()->get('id_perfil') ? session()->get('id_perfil') : $sub->id;

    AssinantesCurrentTokenLogins::updateDateActionForUserToken(request()->session()->get('current_token_login'), 'playing');

    #return view('video.show_video_series', ['conteudo' => $conteudo, 'token' => 'tok']);
    return view('video.show_video_series', [
      'userEvent' => $userOr,
      'conteudo_selecionado' => $conteudo_selecionado,
      'destaques_home' => $destaques_home,
      'conteudo_series_episodios' => $conteudo_series_episodios,
      'token' => 'tok',
      'playlist' =>  json_encode($playlist)
    ]);
  }

  public function getFirstEp($serie_id)
  {

    return $firstEp = (new ConteudoSeriesEpisodios)->getFirstEp($serie_id);
  }
}
