<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pacote extends Model
{
    use HasFactory;

    protected $table = 'tbPacotes';
    public $timestamps = false;
    public $fillable = ['id', 'nome', 'codigo', 'descricao', 'conteudo_canais_list', 'categorias_list', 'conteudo_vod', 'conteudo_series_vod', 'app_exclusivo_list'];


    public function conteudos($tipo = null)
    {
        $ids = explode(',', $this->conteudo_canais_list);
        $cnt = ConteudoCanais::whereIn('id', $ids);
        if($tipo) {
            $cnt = $cnt->where('tipo', $tipo);
        }
        return $cnt->orderBy('id', 'desc')->where('status', '1')->get();
    }

    public function categorias()
    {
        $ids = explode(',', $this->categorias_list);
        return Category::whereIn('id', $ids)->orderBy('id', 'desc')->where('status', '1')->get();
    }

    public function apps_exclusivos()
    {
        $ids = explode(',', $this->app_exclusivo_list);
        return App::whereIn('id', $ids)->orderBy('app_nome', 'desc')->where('ativo', '1')->get();
    }

    public function creditosTotalApp($app)
    {
        $total = 0;
        $creditoPacotes = CreditoPacotes::where('app', $app)->where('id_pacote', $this->id)->get();
        //dd($creditoPacotes);
        foreach($creditoPacotes as $cp) {
            $total += $cp->creditos;
        }
        return $total;
    }

    public function limiteCreditosAtingidoForApp($appid)
    {
        $creditoPacotes = App::find($appid)->getCreditoPacotes();
        if(!isset($creditoPacotes[$this->id])) {
            return true;
        }
        return $creditoPacotes[$this->id]['disponivel'] <= 0;
    }

}
