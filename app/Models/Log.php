<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    use HasFactory;

    protected $table = 'tbLogs';
    public $timestamps = false;

    public static function log($msg, $tags = null, $user = null)
    {
        Log::logApp(null, $msg, $tags, $user);
    }

    public static function logApp($app, $msg, $tags = null, $user = null)
    {
        $l = new Log;
        $l->message = $msg;
        $l->tags    = $tags;
        $l->id_user = $user;
        $l->app     = $app;
        $l->save();

    }
}
