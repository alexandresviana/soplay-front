<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use HasFactory;

	protected $table = 'tbCategorias';
	public $timestamps = false;
	public $fillable = ['id', 'descricao', 'show_menu', 'idRef'];

    public static function indexedCategories($orderBy = false)
	{
        if($orderBy) {
            $cList = Category::orderBy($orderBy)->get();
        } else {
            $cList = Category::all();
        }

		$ret = [];
		foreach($cList as $cat) {
			$ret[$cat->id] = $cat;
		}
		return $ret;
	}
}
