<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Conner\Likeable\Likeable;

use \Datetime;

class ConteudoSeries extends Model
{
    use HasFactory,
    	Likeable;

	protected $table = 'tbConteudoSeries';
	public $timestamps = false;

	private static $bucket;

	const CONTEUDO_CATEGORIA_MOVIES = 1 ; // filmes
	const CONTEUDO_CATEGORIA_AOVIVO = 2 ; // ao vivo
	const CONTEUDO_CATEGORIA_TESTE  = 4 ; // teste


	public function getImagemFullUrl($appendName = '')
	{
		if(!self::$bucket) {
			self::$bucket = DB::table('tbBuckets')->first();
		}

		$arquivo = DB::table('tbArquivos')->where('id', $this->imagem)->first();

		if(!$arquivo) {
			return '';
		}

    	return self::$bucket->UrlImg.$arquivo->url.$appendName.".".$arquivo->extensao;
	}

	public function getImagemBannerFullUrl($appendName = '')
	{
		if(!self::$bucket) {
			self::$bucket = DB::table('tbBuckets')->first();
		}

		$arquivo = DB::table('tbArquivos')->where('id', $this->imagem_banner)->first();

		if(!$arquivo) {
			return '';
		}

    	return self::$bucket->UrlImg.$arquivo->url.$appendName.".".$arquivo->extensao;
	}

	public static function byCategoria($categoria)
	{
		$idsConteudo = [-1];
		$categorias = DB::table('tbConteudoCatSub')->where('idRefCategoria', $categoria)->get();
		foreach ($categorias as $categoria) {
			array_push($idsConteudo, $categoria->idRefConteudoSeries);
		}

		return ConteudoSeries::whereIn('id', $idsConteudo)->orderBy('id', 'desc')->where('status', '1')->where('tipo', 'serie')->get();
	}

    public static function byCategoryAndPackage($categoria, $pacoteList)
    {
        $idsConteudo = [-1];
        return ConteudoSeries::select('tbConteudoSeries.*')
            ->join('tbConteudoCatSub', 'tbConteudoCatSub.idRefConteudoSeries', '=', 'tbConteudoSeries.id')
            ->leftJoin('pacotes_series_vod', 'pacotes_series_vod.conteudo_series_id', '=', 'tbConteudoSeries.id')
            ->where('idRefCategoria', $categoria)
            ->whereIn('pacotes_series_vod.pacote_id', $pacoteList)
            ->where('status', '1')
            ->orderBy('ordem');
    }

	public static function favorito($idPerfil)
	{
		$idsConteudo = [-1];

		return ConteudoSeries::where('status', '1')->whereLikedBy($idPerfil)->with('likeCounter')->get();
	}

    public static function aluguel($appId)
    {
        $idsConteudo = [-1];        

        $rentList = ConteudoDisponivelAluguel::where('tipo_conteudo', 'conteudo_series')
            ->where('ativo', 1)
            ->whereRaw("(FIND_IN_SET(?, apps_disponiveis_list) > 0 or FIND_IN_SET(?, apps_disponiveis_list) > 0)", [$appId, '*'])
            ->whereRaw("(not FIND_IN_SET(?, apps_indisponiveis_list) or apps_indisponiveis_list is null)", [$appId])
            ->get();

        foreach($rentList as $cont) {
            $idsConteudo[] = $cont->id_conteudo;
        }

        return ConteudoSeries::where('status', '1')
                        ->whereIn('id', $idsConteudo)->get();
    }

	public static function allByCategoria($limit = null, $indexedById = false)
	{
		$conteudoCategoria = [];
		$categorias = [];
		$conteudos = [];
		foreach(DB::table('tbConteudoCatSub')->where('idRefConteudoSeries', '<>', null)->get() as $item) {
			$conteudoCategoria[$item->idRefConteudoSeries][] = $item->idRefCategoria;
		}
		foreach(DB::table('tbCategorias')->get() as $item) {
			if($item->show_menu != 1) {
				continue;
			}
			$categoria[$item->id] = $item->descricao;
		}

		$contentList = ConteudoSeries::where('status', '1')->where('tipo', 'serie');

		// limitacao de conteudo esta sendo feito apos buscar todo o conteudo,
		// limitar na busca em vez de buscar tudo
		foreach($contentList->get() as $item) {
			if(!isset($conteudoCategoria[$item->id])) {
				continue;
			}
			$cats = $conteudoCategoria[$item->id];
			foreach($cats as $cat) {
				if(!isset($categoria[$cat])) {
					continue;
				}
				$catDesc = $categoria[$cat];
				if($indexedById) {
					$catDesc = $cat;
				}

				if($limit && isset($conteudos[$catDesc]) && sizeof($conteudos[$catDesc]) >= $limit) {
					continue;
				}

				$conteudos[$catDesc][] = $item;
			}
		}
		return $conteudos;
	}

    public static function allByCategoriaByPackage($limit = null, $indexedById = false, array $package = [])
    {
        $conteudoCategoria = [];
        $categorias = [];
        $conteudos = [];
        foreach (DB::table('tbConteudoCatSub')->where('idRefConteudoSeries', '<>', null)->get() as $item) {
            $conteudoCategoria[$item->idRefConteudoSeries][] = $item->idRefCategoria;
        }
        foreach (DB::table('tbCategorias')->get() as $item) {
            if ($item->show_menu != 1) {
                continue;
            }
            $categoria[$item->id] = $item->descricao;
        }

        $contentList = ConteudoSeries::select('tbConteudoSeries.*')
            ->where('status', '1')
            ->leftJoin('pacotes_series_vod', 'pacotes_series_vod.conteudo_series_id', '=', 'tbConteudoSeries.id')
            ->whereIn('pacotes_series_vod.pacote_id', $package)
            ->orderBy('ordem');

        // limitacao de conteudo esta sendo feito apos buscar todo o conteudo,
        // limitar na busca em vez de buscar tudo
        foreach ($contentList->get() as $item) {
            if (!isset($conteudoCategoria[$item->id])) {
                continue;
            }
            $cats = $conteudoCategoria[$item->id];
            foreach ($cats as $cat) {
                if (!isset($categoria[$cat])) {
                    continue;
                }
                $catDesc = $categoria[$cat];
                if ($indexedById) {
                    $catDesc = $cat;
                }

                if ($limit && isset($conteudos[$catDesc]) && sizeof($conteudos[$catDesc]) >= $limit) {
                    continue;
                }

                $conteudos[$catDesc][] = $item;
            }
        }
        return $conteudos;
    }


	public static function activeList()
	{
		return ConteudoSeries::where('status', '1')->where('tipo', 'serie')->orderBy('id', 'desc')->get();
	}

	public static function destaqueHome($appId)
	{
		$app = App::get($appId);
		$bh = $app->getSettings('banner_home_series');
		$cnt = ConteudoSeries::where('destaqueHome', 1)->where('status', '1')->where('tipo', 'serie')->orderBy('id', 'desc');

		if(is_array($bh) && sizeof($bh)) {
			//dd($bh);
			$cnt = $cnt->whereIn('id', $bh);
			//$cnt = Conteudo::where('destaqueHome', 1)->where('status', '1')->where('id', $cnt)->orderBy('id', 'desc');
		} else {
			$cnt = $cnt->where('id', '-1');
		}
		//dd($bh);
		return $cnt->get();
	}

	public static function byCategoriaDesenho($categoria)
	{
		$idsConteudo = [-1];
		$categorias = DB::table('tbConteudoCatSub')->where('idRefCategoria', $categoria)->get();
		foreach ($categorias as $categoria) {
			array_push($idsConteudo, $categoria->idRefConteudo);
		}

		return ConteudoSeries::whereIn('id', $idsConteudo)->orderBy('id', 'desc')->where('status', '1')->where('tipo', 'desenho')->get();
	}

	public static function allByCategoriaDesenho($limit = null, $indexedById = false)
	{
		$conteudoCategoria = [];
		$categorias = [];
		$conteudos = [];
		foreach(DB::table('tbConteudoCatSub')->where('idRefConteudoSeries', '<>', null)->get() as $item) {
			$conteudoCategoria[$item->idRefConteudoSeries][] = $item->idRefCategoria;
		}
		foreach(DB::table('tbCategorias')->get() as $item) {
			if($item->show_menu != 1) {
				continue;
			}
			$categoria[$item->id] = $item->descricao;
		}

		$contentList = ConteudoSeries::where('status', '1')->where('tipo', 'desenho');

		// limitacao de conteudo esta sendo feito apos buscar todo o conteudo,
		// limitar na busca em vez de buscar tudo
		foreach($contentList->get() as $item) {
			if(!isset($conteudoCategoria[$item->id])) {
				continue;
			}
			$cats = $conteudoCategoria[$item->id];
			foreach($cats as $cat) {
				if(!isset($categoria[$cat])) {
					continue;
				}
				$catDesc = $categoria[$cat];
				if($indexedById) {
					$catDesc = $cat;
				}

				if($limit && isset($conteudos[$catDesc]) && sizeof($conteudos[$catDesc]) >= $limit) {
					continue;
				}

				$conteudos[$catDesc][] = $item;
			}
		}
		return $conteudos;
	}


	public static function activeListDesenho()
	{
		return ConteudoSeries::where('status', '1')->where('tipo', 'desenho')->orderBy('id', 'desc')->get();
	}

	public static function destaqueHomeDesenho($appId)
	{
		$app = App::get($appId);
		$bh = $app->getSettings('banner_home_series');
		$cnt = ConteudoSeries::where('destaqueHome', 1)->where('status', '1')->where('tipo', 'desenho')->orderBy('id', 'desc');

		if(is_array($bh) && sizeof($bh)) {
			//dd($bh);
			$cnt = $cnt->whereIn('id', $bh);
			//$cnt = Conteudo::where('destaqueHome', 1)->where('status', '1')->where('id', $cnt)->orderBy('id', 'desc');
		} else {
			$cnt = $cnt->where('id', '-1');
		}
		//dd($bh);
		return $cnt->get();
	}

	public function getEpisodios()
	{
		return ConteudoSeriesEpisodios::where('idRefConteudoSerie', $this->id)->where('status', '1')->orderBy('episodio')->orderBy('titulo')->get();
	}

	public function findAvailableRent($appId)
	{
        $rent = ConteudoDisponivelAluguel::where('tipo_conteudo', 'conteudo_series')
                        ->where('id_conteudo', $this->id)
                        ->where('ativo', 1)
                        ->whereRaw("(FIND_IN_SET(?, apps_disponiveis_list) > 0 or FIND_IN_SET(?, apps_disponiveis_list) > 0)", [$appId, '*'])
                        ->whereRaw("(not FIND_IN_SET(?, apps_indisponiveis_list) or apps_indisponiveis_list is null)", [$appId])
                        ->first();
        return $rent;
	}

	public function findActiveRentForSubscriber($subscriberId)
	{
        $dStart = new Datetime;

        $rent = AssinantesConteudoAlugado::where('tipo_conteudo', 'conteudo_series')
                        ->where('id_conteudo', $this->id)
                        ->where('id_assinante', $subscriberId)
                        ->where('data_inicio', '<=', $dStart->format('Y-m-d H:i:s'))
                        ->where('data_fim', '>=', $dStart->format('Y-m-d H:i:s'))
                        ->where('ativo', 1)
                        ->first();
        return $rent;
	}
}
