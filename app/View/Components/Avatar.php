<?php

namespace App\View\Components;

use App\Services\PerfilService;
use Illuminate\View\Component;

class Avatar extends Component
{

  private $perfilService;

  public $perfil;

  public function __construct(PerfilService $perfilService)
  {
    $this->perfilService = $perfilService;
  }

  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\Contracts\View\View|\Closure|string
   */
  public function render()
  {


    $session = session()->get('id_perfil');
    if ($session) {
      $this->perfil = $this->perfilService->find($session);
    }

    return view('components.avatar');
  }
}
