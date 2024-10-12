<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use \Datetime, \DateInterval;

class AssinantesConteudoAlugado extends Model
{
    use HasFactory;

	protected $table = 'tbAssinantesConteudoAlugado';
	public $timestamps = false;
	public $fillable = ['id', 'id_conteudo_disponivel_aluguel', 'tipo_conteudo', 'id_conteudo', 'id_assinante', 'valor', 'data_inicio', 'data_fim'];


    public function assinante()
    {
        return Assinante::find($this->id_assinante);
    }

    public function conteudo()
    {
        if($this->tipo_conteudo == 'conteudo_series') {
            return ConteudoSeries::find($this->id_conteudo);
        }

        if($this->tipo_conteudo == 'conteudo') {
            return Conteudo::find($this->id_conteudo);
        }

        return null;
    }

    public function available()
    {
        $dEnd   = new Datetime($this->data_fim);
        $dStart = new Datetime($this->data_inicio);
        $dNow   = new Datetime;

        return ( $this->ativo && ($dEnd > $dNow) && ($dStart <= $dNow) );
    }

    public static function findOrCreateForOrder($order)
    {
        $ret = AssinantesConteudoAlugado::where('order', $order)->first();

        if(!$ret) {
            $ret = new AssinantesConteudoAlugado;
            $ret->order = $order;
        }

        return $ret;
    }

}
