<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\{Plan};

use \Exception;

class SubscriberSavingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $model = $event->model;
        dd($model);
    }

    public function checkCreditos($event)
    {
        $model = $event->model;

        $setCPlanos    = json_decode($model->settings_conteudos)->planos;
        $oSetCPlanos   = json_decode($model->getOriginal('settings_conteudos'))->planos;
        $isActivating  = ($model->getOriginal('status') == 0 && $model->status == 1);
        $alreadyActive = $model->getOriginal('status') == 1 && $model->status == 1;

        if(!$setCPlanos) {
            $setCPlanos = [];
        }

        if(!$oSetCPlanos) {
            $oSetCPlanos = [];
        }

        asort($setCPlanos);
        asort($oSetCPlanos);

        if($model->status == 0) {
            return true;
        }

        // verifica se alterou planos ou status
        if($setCPlanos == $oSetCPlanos && !$isActivating) {
            return true;
        }

        // itera em cada plano para verificar limite de credits
        foreach($setCPlanos as $planId) {
            $plan = Plan::find($planId);

            if(!$plan) {
                continue;
            }

            // caso tenha atingido limite de creditos...
            if($plan->limiteCreditosAtingido()) {
                // se cliente ja usava o plano, libera
                if(array_search($planId, $oSetCPlanos) !== false && $alreadyActive) {
                    continue;
                }

                // caso nao estava em uso o plano e tentou adicionar, e o limite foi atingido
                // nao libera o uso
                throw new Exception(sprintf('Limite de créditos para plano "%d/%s" atingido.', $planId, $plan->nome));
            }
        }
    }
}
