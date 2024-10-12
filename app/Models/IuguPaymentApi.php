<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use \Exception;
use \Datetime;
use \DateInterval;

class IuguPaymentApi extends Model
{
    use HasFactory;

    const apiUrl             = 'https://api.iugu.com/v1';

    public function getCliente(Assinante $sub)
    {

    }

    public static function createCliente(Assinante $subscriber)
    {
        $token          = SystemSettings::getSettings('iugu_api_token');
        $url            = sprintf('%s/customers?api_token=%s', self::apiUrl, $token);
        $client         = new \GuzzleHttp\Client();

        $body = array(
                    //"email"          => 'slv.eziel+iugu@gmail.com',
                    "email"             => sprintf('%s@teste-dev.ottplay.com.br', $subscriber->id),
                    "name"      		=> $subscriber->nome,
                    "custom_variables"  => [
                    	['name' => 'id', 'value' => $subscriber->id],
                    ]
                );

        try {
            $response = $client->request('POST', $url, [
                'body'      => json_encode($body),
                'headers'   => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
            $rBody = json_decode($response->getBody()->getContents());

            $retInfo = [];

            $retInfo['id']              = $rBody->id;
            $retInfo['email']           = $rBody->email;
            $retInfo['name']    		= $rBody->name;

            return $retInfo;
        }catch(Exception $e) {
            throw $e;
        }

        return false;
    }

    public static function createFormaPagamento(Assinante $subscriber)
    {
        $token  = SystemSettings::getSettings('iugu_api_token');
        $client = new \GuzzleHttp\Client();

        try {
            $iuguCustomerId = @$subscriber->getSettingsIugu('customer')->id;
            $iuguCardToken  = @$subscriber->getSettingsIugu('cards')->default;

            if(!$iuguCustomerId) {
                throw new Exception('Cliente iugu não cadastrado');
            }

            if(!$iuguCardToken) {
                throw new Exception('Cartão não cadastrado / Token não encontrado');
            }

        }catch(Exception $e) {
            throw $e;
        }

        $url = sprintf('%s/customers/%s/payment_methods?api_token=%s', self::apiUrl, $iuguCustomerId, $token);

        $body = array(
                    "description"       => sprintf('Cartao padrão', $subscriber->id),
                    "token"             => $iuguCardToken,
                    "set_as_default"    => true,
                );

        try {
            $response = $client->request('POST', $url, [
                'body'      => json_encode($body),
                'headers'   => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
            $rBody = json_decode($response->getBody()->getContents());

            $retInfo = [];

            $retInfo['id']              = $rBody->id;
            $retInfo['description']     = $rBody->description;
            $retInfo['customer_id']     = $rBody->customer_id;
            $retInfo['data']            = (array) $rBody->data;

            return $retInfo;
        }catch(Exception $e) {
            throw $e;
        }

        return false;
    }

    public static function createAssinatura(Assinante $subscriber, Plan $plan)
    {
        $token          = SystemSettings::getSettings('iugu_api_token');
        $url            = sprintf('%s/subscriptions?api_token=%s', self::apiUrl, $token);
        $client         = new \GuzzleHttp\Client();

        try {
            $iuguCustomerId = @$subscriber->getSettingsIugu('customer')->id;
            $iuguPlanId     = $plan->id;

            if(!$iuguCustomerId) {
                throw new Exception('Cliente iugu não cadastrado');
            }

            if(!$iuguPlanId) {
                throw new Exception('Plano inválido');
            }

        }catch(Exception $e) {
            throw $e;
        }

        $iuguPlan = IuguPaymentApi::getOrCreatePlanByIdentifier($iuguPlanId);
        if(!$iuguPlan) {
            throw new Exception('Não foi possível obter/criar plano para assinatura');            
        }

        $currentDate = new Datetime;
        $currentDate->add(new DateInterval(sprintf('P%sD', $plan->validade)));
        $expiresDate = clone $currentDate;


        $body = array(
                    "customer_id"               => $iuguCustomerId,
                    "plan_identifier"           => $iuguPlanId,
                    "expires_at"                => $expiresDate->format('Y-m-d H:i:s'),
                    "only_charge_on_due_date"   => true,
                    "only_on_charge_success"    => false,
                );

        try {
            $response = $client->request('POST', $url, [
                'body'      => json_encode($body),
                'headers'   => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
            $rBody = json_decode($response->getBody()->getContents());
            $retInfo = [];

            $retInfo['id']              = $rBody->id;
            $retInfo['active']          = $rBody->active;
            $retInfo['suspended']       = $rBody->suspended;
            $retInfo['plan']            = $iuguPlanId;

            return $retInfo;
        }catch(Exception $e) {
            $msg = sprintf('%s', str_replace($token, '***', $e->getMessage()));
            throw new Exception($msg);
        }

        return false;
    }

    public static function deleteAssinaturas(Assinante $subscriber, $subscriptionId)
    {
        $token          = SystemSettings::getSettings('iugu_api_token');
        $url            = sprintf('%s/subscriptions', self::apiUrl);
        $client         = new \GuzzleHttp\Client();


        $url = sprintf('%s/%s', $url, $subscriptionId);
        /*
        try {
            $iuguCustomerId = @$subscriber->getSettingsIugu('customer')->id;
            $iuguPlanId     = $plan->id;

            if(!$iuguCustomerId) {
                throw new Exception('Cliente iugu não cadastrado');
            }
        }catch(Exception $e) {
            throw $e;
        }


        $body = array(
                    "customer_id"       => $iuguCustomerId,
                    "plan_identifier"   => $iuguPlanId,
                );
        */
        $body = [];

        try {
            $response = $client->request('DELETE', $url, [
                'body'      => json_encode($body),
                'headers'   => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic '. base64_encode($token),
                    //Cookie: __cfruid=45d3c132585d9d277025a56502dc3fb5016073f9-1634913572
                ],
            ]);
            $rBody = json_decode($response->getBody()->getContents());

            dd($rBody);
 
            $retInfo = [];

            $retInfo['id']              = $rBody->id;
            $retInfo['active']          = $rBody->active;
            $retInfo['suspended']       = $rBody->suspended;
            $retInfo['plan']            = $iuguPlanId;

            return $retInfo;
        }catch(Exception $e) {
            throw $e;
        }

        return false;
    }

    public static function getOrCreatePlanByIdentifier($planIdentifier)
    {
        $token          = SystemSettings::getSettings('iugu_api_token');
        $url            = sprintf('%s/plans/identifier', self::apiUrl);
        $client         = new \GuzzleHttp\Client();


        $url = sprintf('%s/%s', $url, $planIdentifier);
        $url = sprintf('%s?api_token=%s', $url, $token);

        $body = [];

        try {
            $response = $client->request('GET', $url, [
                'body'      => json_encode($body),
                'headers'   => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    //Cookie: __cfruid=45d3c132585d9d277025a56502dc3fb5016073f9-1634913572
                ],
            ]);
            $rBody = json_decode($response->getBody()->getContents());

 
            $retInfo = [];

            $retInfo['id']          = $rBody->id;
            $retInfo['name']        = $rBody->name;
            $retInfo['price_cents'] = $rBody->prices[0]->value_cents;
            $retInfo['identifier']  = $rBody->identifier;

            return $retInfo;
        }catch(Exception $e) {
            // vai criar o plano em seguida
        }

        try {
            return IuguPaymentApi::_createPlan($planIdentifier);
        }catch(Exception $e) {
        }

        return false;
    }

    private static function _createPlan($planIdentifier)
    {
        $plan = Plan::find($planIdentifier);

        if(!$plan) {
            return false;
        }

        $token          = SystemSettings::getSettings('iugu_api_token');
        $url            = sprintf('%s/plans', self::apiUrl);
        $client         = new \GuzzleHttp\Client();

        $url = sprintf('%s?api_token=%s', $url, $token);

        $body = array(
                    "name"          => strtoupper($plan->nome),
                    "identifier"    => (string) $plan->id,
                    "interval"      => 1,
                    "interval_type" => 'months',
                    "value_cents"   => (int) ($plan->valor * 100),
                    "payable_with"  => ['credit_card'],
                );

        try {
            $response = $client->request('POST', $url, [
                'body'      => json_encode($body),
                'headers'   => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    //Cookie: __cfruid=45d3c132585d9d277025a56502dc3fb5016073f9-1634913572
                ],
            ]);
            $rBody = json_decode($response->getBody()->getContents());

 
            $retInfo = [];

            $retInfo['id']          = $rBody->id;
            $retInfo['name']        = $rBody->name;
            $retInfo['price_cents'] = $rBody->prices[0]->value_cents;
            $retInfo['identifier']  = $rBody->identifier;

            return $retInfo;
        }catch(Exception $e) {
        }

        return false;
    }


    public static function getPayments(Assinante $subscriber)
    {
        $token          = SystemSettings::getSettings('iugu_api_token');
        $url            = sprintf('%s/invoices', self::apiUrl);
        $client         = new \GuzzleHttp\Client();

        try {
            $iuguCustomerId = @$subscriber->getSettingsIugu('customer')->id;

            if(!$iuguCustomerId) {
                return [];
                //throw new Exception('Cliente iugu não cadastrado');
            }

        }catch(Exception $e) {
            throw $e;
        }



        $url = sprintf('%s?customer_id=%s', $url, $iuguCustomerId);
        $url = sprintf('%s&api_token=%s', $url, $token);

        $body = [];

        try {
            $response = $client->request('GET', $url, [
                'body'      => json_encode($body),
                'headers'   => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    //Cookie: __cfruid=45d3c132585d9d277025a56502dc3fb5016073f9-1634913572
                ],
            ]);
            $rBody = json_decode($response->getBody()->getContents());

            if(!$rBody->totalItems) {
                return [];
            }
            $retInfo = [];

            // 
            foreach($rBody->items as $item) {
                $rInfo['due_date']      = new Datetime($item->due_date);
                $rInfo['paid_at']       = new Datetime($item->paid_at);
                $rInfo['status']        = $item->status;
                $rInfo['total']         = $item->total_cents / 100;
                $rInfo['total_paid']    = $item->total_paid_cents / 100;
                $retInfo[] = $rInfo;
            }

            return $retInfo;
        }catch(Exception $e) {
            throw $e;
            // vai criar o plano em seguida
        }

        return false;

    }


}

