<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Models\Assinante;
use App\Models\SystemSettings;
use App\Models\IuguPaymentApi;
use App\Models\Plan;

use \Exception, \Datetime, \DateInterval;


class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $sub = $this->__subscriber();
        $paymentsList = IuguPaymentApi::getPayments($sub);
        $iuguIdConta  = SystemSettings::getSettings('iugu_id_conta');

        //$paymentsList = [];
        //dd($paymentsList);

        $plan =  false;
        $planId = @$sub->getSettingsIugu('subscription')->plan;

        if(!$planId){
            $app = $this->__app();
            $planId = $app->settingsSignupPlan();
        }

        if($planId) {
            $plan = Plan::find($planId);
        }

        return view('checkout.index', ['payments_list' => $paymentsList, 'iugu_id_conta' => $iuguIdConta, 'subscription_plan' => $plan]);
    }

    public function confirm(Request $request)
    {
        $sub    = $this->__subscriber();
        $app    = $this->__app();
        $plan   = $app->settingsSignupPlan();

        if($plan) {
            $plan = Plan::find($plan);
        }

        $iuguIdConta    = SystemSettings::getSettings('iugu_id_conta');
        $paymentsList   = IuguPaymentApi::getPayments($sub);
        $token          = $request->post('token');

        if(!$plan) {
            $request->session()->flash('alert', 'Plano para cadastro não selecionado.');
            return view('checkout.index', ['payments_list' => $paymentsList, 'iugu_id_conta' => $iuguIdConta, 'subscription_plan' => $plan]);
        }

        if(!$token) {
            $request->session()->flash('alert', 'Token inválido');
            return view('checkout.index', ['payments_list' => $paymentsList, 'iugu_id_conta' => $iuguIdConta, 'subscription_plan' => $plan]);
        }

        try {
            $iuguCustomerId     = @$sub->getSettingsIugu('customer')->id;
            $iuguCards          = @$sub->getSettingsIugu('cards');
            $iuguSubscriptionId = @$sub->getSettingsIugu('subscription')->id;
            $tokenInUse         = false;
            $iuguSettings       = [];

            if(!$iuguCustomerId) {
                $iuguRet = IuguPaymentApi::createCliente($sub);

                $iuguSettings['customer'] = [
                    'id' => $iuguRet['id'],
                ];
            }

            $iuguSettings['cards']['default'] = $token;

            $tokenInUse = isset($iuguCards->$token);
            if(!$tokenInUse) {
                $iuguSettings['cards'][$token] = $token;
            }

            $sub->updateSettingsIugu($iuguSettings);
            $sub->save();

            if(!$tokenInUse) {
                $ret = IuguPaymentApi::createFormaPagamento($sub);

                $iuguSettings['payment_methods'] = [
                        $ret['id'] => [
                            'description'   => $ret['description'],
                            'data'          => $ret['data'],
                            'token'         => $token,
                        ],
                        'default' => $ret['id'],
                ];

                $sub->updateSettingsIugu($iuguSettings);
                $sub->save();
            }

            if(!$iuguSubscriptionId) {
                $ret = IuguPaymentApi::createAssinatura($sub, $plan);

                $iuguSettings['subscription']['id']     = $ret['id'];
                $iuguSettings['subscription']['plan']   = $ret['plan'];

                $sub->updateSettingsIugu($iuguSettings);
                $sub->save();
            }


        }catch(Exception $e) {
            $request->session()->flash('alert', sprintf('Ocorreu um erro ao cadastrar cliente/assinatura (%s)', htmlentities($e->getMessage())));
            return view('checkout.index', ['payments_list' => $paymentsList, 'iugu_id_conta' => $iuguIdConta, 'subscription_plan' => $plan]);
        }

        $paymentsList   = IuguPaymentApi::getPayments($sub);

        $request->session()->flash('success', 'Informações de pagamento adicionada. A assinatura estará disponível em alguns instantes.');

        return view('checkout.index', ['payments_list' => $paymentsList, 'iugu_id_conta' => $iuguIdConta, 'subscription_plan' => $plan]);
    }

}
