<?php

namespace App\Http\Controllers;

use App\Models\ConteudoCanais;
use Illuminate\Http\Request;
use App\Services\PacotesServices;

class PesquisarController extends Controller
{
  public function index(Request $request)
  {
    $sub = $this->__subscriber();
    $meusCanais = $sub->getConteudosPlanos("tv");

    $servicesPacotes = new PacotesServices($sub);
    $pacotes = $servicesPacotes->list();
    $conteudo_pesquisa = ConteudoCanais::pesquisa($request->route('string'), $pacotes);

    $cList = [];
    foreach ($conteudo_pesquisa as $pesquisa) :
      if ($pesquisa->tipo == "tv" || $pesquisa->tipo == 'radio') {
        foreach ($meusCanais as $canal) :
          if ($pesquisa->id == $canal->id) {
            $cList[] = $pesquisa;
            break;
          }
        endforeach;
      } else {
        $cList[] = $pesquisa;
      }
    endforeach;
    return view('video.pesquisar', ['conteudo_pesquisa' => $cList]);
  }
}
