<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConteudoDisponivelAluguel extends Model
{
    use HasFactory;

	protected $table = 'tbConteudoDisponivelAluguel';
	public $timestamps = false;
	public $fillable = ['id', 'tipo_conteudo', 'id_conteudo', 'descricao', 'valor', 'tempo_disponivel', 'ativo', 'apps_disponiveis_list'];


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

    public function apps_disponiveis()
    {
        $ids = explode(',', $this->apps_disponiveis_list);
        return App::whereIn('id', $ids)->orderBy('app_nome', 'desc')->where('ativo', '1')->get();
    }

    public function apps_disponiveisToPaginate()
    {
        $ids = explode(',', $this->apps_disponiveis_list);
        return App::whereIn('id', $ids)->orderBy('app_nome', 'asc');
    }

    public function disponivelParaAssinante($id)
    {
        $cnt = $this->conteudo();

        if(!$cnt) {
            return false;
        }

        $activeRent = $cnt->findActiveRentForSubscriber($id);

        return $activeRent;
    }

}
