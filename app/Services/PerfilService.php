<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Perfil;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Services\BaseService;

class PerfilService extends BaseService
{
  public function all($idAssinante): Collection
  {
    return \App\Models\Perfil::where('id_assinante', $idAssinante)
      ->where('status', '1')
      ->where('kids', '0')
      ->get();
  }

  public function save(array $data): Perfil
  {
    $dat['id_assinante'] = $this->subscriber()->id;
    return \App\Models\Perfil::create($data);
  }

  public function update(array $data, int $id): Perfil
  {
    $perfil = $this->find($id);
    $perfil->update($data);
    return $perfil;
  }

  public function delete(int $id): void
  {
    $perfil = $this->find($id);
    \App\Models\Perfil::destroy($id);
  }

  public function find(int $id): Perfil
  {
    $sub = $this->subscriber();
    $perfil = \App\Models\Perfil::find($id);

    if ($perfil->id_assinante != $sub->id) {
      throw new \Exception('Acesso inválido');
    }

    return $perfil;
  }
}
