<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreditoPacotes extends Model
{
    use HasFactory;

	protected $table = 'tbCreditoPacotes';
	public $timestamps = false;
	public $fillable = ['id', 'id_pacote', 'creditos', 'app', ];

	public static function byApp($app, $id = null)
	{
		if(!$id) {
			return CreditoPacotes::where('app', $app)->get();//->where('ativo', '1')->get();
		}
		return CreditoPacotes::where('app', $app)->where('id', $id)->first();//->where('ativo', '1')->get();
	}


	public function pacoteNome()
	{
		$pct = Pacote::find($this->id_pacote);
		return $pct->nome;
	}
}
