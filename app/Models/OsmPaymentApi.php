<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Models\Log;
use \Exception;

class OsmPaymentApi
{

    //const OSM_API_URL = 'http://l-osm-nxplay2/api/v1';
    // const OSM_API_URL = 'https://osm.ottplay.com.br/api/v1';

    public static function createInvoice($userToken, $kind, $contentKind, $id)
    {
        $client = new \GuzzleHttp\Client();

        $token  = $userToken;
        $url    = sprintf('%s/payments/iugu_create_invoice', config('app.osm_api_endpoint'));

        $body   = array(
                    "kind"          => "$kind",
                    "content_kind"  => "$contentKind",
                    "content_id"    => "$id",
                );
        try {
            $response = $client->request('POST', $url, [
                'body'      => json_encode($body),
                'headers'   => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $token)
                ],
            ]);

            $rBody = json_decode($response->getBody()->getContents());

            if(isset($rBody->access) && $rBody->access == false) {
                throw new Exception($rBody->error->details);
            }

            if(!isset($rBody->success)) {
                throw new Exception($rBody->msg);
            }


            $ret = [];

            $ret['id']              = $rBody->iugu->id;
            $ret['order']           = $rBody->iugu->order;
            $ret['due_date']        = $rBody->iugu->iugu_due_date;
            $ret['total_cents']     = $rBody->iugu->iugu_total_cents;
            $ret['status']          = $rBody->iugu->iugu_status;
            $ret['secure_id']       = $rBody->iugu->iugu_secure_id;
            $ret['secure_url']      = $rBody->iugu->iugu_secure_url;
            $ret['pix_qrcode_url']  = $rBody->iugu->iugu_pix_qrcode_url;
            $ret['pix_qrcode_text'] = $rBody->iugu->iugu_pix_qrcode_text;


            $client = new \GuzzleHttp\Client();

            return $ret;
        }catch(Exception $e) {
            Log::log($e->getMessage(), 'payment');
            throw new Exception('Confirme suas informações cadastrais (cpf/email) em Perfil / Dados pessoais');
        }


    }

}
