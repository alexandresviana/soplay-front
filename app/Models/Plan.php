<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'tbPlanos';
    public $timestamps = false;
    public $fillable = ['id', 'nome', 'codigo', 'descricao', 'dispositivos', 'valor', 'pacotes_list', 'conteudo_vod', 'conteudo_series_vod', 'ativo'];

    public static function byApp($app, $id = null)
    {
        if(!$id) {
            return Plan::where('app', $app)->orderBy('nome', 'desc')->get();//->where('ativo', '1')->get();
        }
        return Plan::where('app', $app)->where('id', $id)->first();//->where('ativo', '1')->get();
    }

    public function hasPackage($id)
    {
        $lst = $this->pacotes_list;
        if(!is_array($lst)) {
            $lst = explode(',', $lst);
        }
        return array_search($id, $lst) !== false;
    }

    public static function packages()
    {
        return Pacote::all();
    }

    public static function packagesByApp($app, $id = null)
    {
        // inicialmente buscava todos os pacotes que nao eram exclusivos para alguem E mais os exclusivos
        //return Pacote::whereRaw("FIND_IN_SET(?, app_exclusivo_list) > 0", [$app])->orWhereNull('app_exclusivo_list')->get();

        // busca somente os pacotes em que o id do provedor tenha sido adicionado
        return Pacote::whereRaw("FIND_IN_SET(?, app_exclusivo_list) > 0", [$app])->get();//->orWhereNull('app_exclusivo_list')->get();

        //return Pacote::whereNull('app_exclusivo_list')->get();
    }

    public static function package($id)
    {
        return Pacote::find($id);
    }

    public function getPackages()
    {
        return Pacote::whereIn('id', explode(',', $this->pacotes_list))->get();
    }

    public function getPlanConteudos($tipo = null)
    {
        $pkgs = $this->getPackages();
        $contentList = [];
        foreach($pkgs as $pkg) {
            foreach($pkg->conteudos($tipo) as $conteudo) {
                array_push($contentList, $conteudo);
            }
        }

        $contentList = array_unique($contentList);
        return $contentList;
    }

    public function getPlanAllConteudosByCategory($tipo = null)
    {
        $pkgs = $this->getPackages();
        $conteudoCategoria = [];
        $conteudoCanaisCategoria = [];
        $conteudoSeriesCategoria = [];
        $conteudoDesenhosCategoria = [];
        $categorias = [];
        $conteudosRet = [];

        foreach(DB::table('tbConteudoCatSub')->get() as $item) {
            if($item->idRefConteudo != null) {
                $conteudoCategoria[$item->idRefConteudo][] = $item->idRefCategoria;
            }
            if($item->idRefConteudoCanais != null) {
                $conteudoCanaisCategoria[$item->idRefConteudoCanais][] = $item->idRefCategoria;
            }
            if($item->idRefConteudoSeries != null) {
                $conteudoSeriesCategoria[$item->idRefConteudoSeries][] = $item->idRefCategoria;
            }
        }

        foreach($pkgs as $pkg) {
            foreach($pkg->categorias() as $category) {
                if($category->show_menu != 1) {
                    continue;
                }
                $categorias[$category->id] = $category->descricao;
            }
        }

        $idsConteudo        = array_keys($conteudoCategoria);
        $idsConteudoCanais  = array_keys($conteudoCanaisCategoria);
        $idsConteudoSeries  = array_keys($conteudoSeriesCategoria);

        $conteudosRet = $conteudosCanaisRet = $conteudosSeriesRet = $conteudosDesenhosRet = [];

        foreach(Conteudo::where('status', '1')->whereIn('id', $idsConteudo)->get() as $item) {
            $cats = $conteudoCategoria[$item->id];
            foreach($cats as $cat) {
                if(!isset($categorias[$cat])) {
                    continue;
                }
                $catDesc = $categorias[$cat];
                $conteudosRet[$catDesc][] = $item;
            }
        }

        $cnt = ConteudoCanais::where('status', '1')->whereIn('id', $idsConteudoCanais);
        if($tipo) {
            $cnt = $cnt->where('tipo', $tipo);
        }
        foreach($cnt->get() as $item) {
            $cats = $conteudoCanaisCategoria[$item->id];
            foreach($cats as $cat) {
                if(!isset($categorias[$cat])) {
                    continue;
                }
                $catDesc = $categorias[$cat];
                $conteudosCanaisRet[$catDesc][] = $item;
            }
        }

        foreach(ConteudoSeries::where('status', '1')->whereIn('id', $idsConteudoSeries)->get() as $item) {
            $cats = $conteudoSeriesCategoria[$item->id];
            foreach($cats as $cat) {
                if(!isset($categorias[$cat])) {
                    continue;
                }
                $catDesc = $categorias[$cat];

                if($item->tipo == 'serie') {
                    $conteudosSeriesRet[$catDesc][] = $item;
                } elseif($item->tipo == 'desenho') {
                    $conteudosDesenhosRet[$catDesc][] = $item;
                }
            }
        }

        $ret = [
            'conteudos'         => $conteudosRet,
            'conteudosCanais'   => $conteudosCanaisRet,
            'conteudosSeries'   => $conteudosSeriesRet,
            'conteudosDesenhos' => $conteudosDesenhosRet,
        ];


        return $ret;
    }

    public function getCreditosCusto()
    {
        $pkgs = $this->getPackages();
        $credits = [];
        $cPacotes = [];

        $creditoPacotes = CreditoPacotes::byApp($this->app);
        foreach($creditoPacotes as $cp) {
            if(!isset($cPacotes[$cp->id_pacote])) {
                $cPacotes[$cp->id_pacote] = 0;
            }
            $cPacotes[$cp->id_pacote] += $cp->creditos;
        }

        foreach($pkgs as $pkg) {
            if(!isset($cPacotes[$pkg->id])) {
                $credits[$pkg->id] = 0;
                continue;
            }
            $credits[$pkg->id] = $cPacotes[$pkg->id];
        }

        return $credits;
    }

    public function limiteCreditosAtingido()
    {
        foreach($this->getPackages() as $package) {
            if($package->limiteCreditosAtingidoForApp($this->app)) {
                return true;
            }
        }
        return false;
    }


}
