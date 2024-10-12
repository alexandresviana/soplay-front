<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SystemSettings extends Model
{
    use HasFactory;

	protected $table = 'tbSystemSettings';
	public $timestamps = false;

	public static function getSettings($name)
	{
		$set = SystemSettings::firstWhere('name', $name);

		if(!$set) {
			return null;
		}

		return $set->value;
	}
}
