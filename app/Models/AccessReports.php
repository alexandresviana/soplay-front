<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AccessReports extends Model
{
    use HasFactory;

    protected $table = 'tb_registro_login';
    public $fillable = ['id', 'id_usuario', 'token', 'data_cad', 'update'];

    public function userInserts($data)
    {
        $userRegister = DB::table('tb_registro_login')->updateOrInsert($data);
        return $userRegister;
    }

    public function userUpdate($id, $data)
    {
        $userUpdate = DB::table('tb_registro_login')->where('id', $id)->update($data);
        return $userUpdate;
    }
}
