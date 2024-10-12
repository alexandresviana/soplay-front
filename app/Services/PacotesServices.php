<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Assinante;
use App\Models\Plan;

class PacotesServices
{
  private $sub;

  public function __construct(Assinante $sub)
  {
    $this->sub = $sub;
  }

  public function list()
  {
    $plans = json_decode($this->sub->settings_conteudos);

    $pacotes = Plan::whereIn('id', $plans->planos)->get();
    $pacoteList = [];

    $pacotes->each(function ($p, $k) use (&$pacoteList) {
      $arrayForString = explode(',', ($p->pacotes_list ?? ''));
      $pacoteList = array_merge($pacoteList, $arrayForString);
    });

    return $pacoteList;
  }
}
