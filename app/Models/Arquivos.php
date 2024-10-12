<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Arquivos extends Model
{
    use HasFactory;

    protected $table = 'tbArquivos';

    public $timestamps = false;

    public $fillable = ['arquivo', 'descricao', 'idRefUsuario', 'extensao', 'tipo', 'url', 'idRefArquivo', 'm3u8', 'coconutid', 'sincronia', 'status', 'data'];

    private static $bucket;

    public static function configureAws()
    {
        if (!self::$bucket) {
            self::$bucket = DB::table('tbBuckets')->first();
        }

        $b = self::$bucket;

		config(['filesystems.disks.s3.key' 		=> $b->AccessKeyImg]);
		config(['filesystems.disks.s3.secret' 	=> $b->SecretKeyImg]);
		config(['filesystems.disks.s3.bucket' 	=> $b->NameImg]);
		config(['filesystems.disks.s3.url' 		=> $b->UrlImg]);
		config(['filesystems.disks.s3.region' 	=> $b->DefaultRegion]);
	}

	public function getImagemFullUrl($appendName = '')
	{
		if (!self::$bucket) {
			self::$bucket = DB::table('tbBuckets')->first();
		}

		$arquivo = DB::table('tbArquivos')->where('id', $this->id)->first();

		if (!$arquivo) {
			return '';
		}

		return self::$bucket->UrlImg.$arquivo->url.$appendName.".".$arquivo->extensao;
	}

    public function getImageUrlAttribute()
    {
        if (!self::$bucket) {
			self::$bucket = DB::table('tbBuckets')->first();
		}

        return self::$bucket->UrlImg.$this->url.".".$this->extensao;
    }
}
