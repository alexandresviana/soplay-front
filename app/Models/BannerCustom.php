<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BannerCustom extends Model
{
    use HasFactory;

    protected $table = 'tbBannerCustom';
    public $timestamps = false;

    public $fillable = ['arquivo', 'titulo', 'descricao', 'idRefUsuario', 'extensao', 'tipo', 'url', 'status', 'data','app'];

    private static $bucket;

    public $file;

    public function __construct()
    {
        $this->file = new \stdclass;
        $this->file->imageUrl = '';
    }

    public function getImagemFullUrl($appendName = '')
    {
        if(!self::$bucket) {
            self::$bucket = DB::table('tbBuckets')->first();
        }

        $arquivo = DB::table('tbBannerCustom')->where('id', $this->id)->first();

        if(!$arquivo) {
            return '';
        }

        return self::$bucket->UrlImg.$arquivo->url.$appendName.".".$arquivo->extensao;
    }

    public static function allSent($app)
    {
        return BannerCustom::where('app', $app)->orderBy('id', 'desc');
    }

    public static function activeBanners($appId)
    {
        $app = App::get($appId);
        $bh = $app->getSettings('banner_custom');
        $cnt = BannerCustom::where('app', $appId)->where('status', '1')->orderBy('id', 'desc');

        if(is_array($bh) && sizeof($bh)) {
            $cnt = $cnt->whereIn('id', $bh);
        } else {
            $cnt = $cnt->where('id', '-1');
        }
        return $cnt->get();
    }
}
