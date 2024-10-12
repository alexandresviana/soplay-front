<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PerfilService;

class GerenciarPerfilController extends Controller
{
  private $service;

  public function __construct(PerfilService $service)
  {
    $this->service = $service;
  }


  public function index()
  {
    $user = $this->subscriber();

    $data = $this->service->all($user->id);
    return view('perfil.manager', compact('data'));
  }
}
