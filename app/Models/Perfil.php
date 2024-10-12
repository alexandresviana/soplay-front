<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfil';

    protected $fillable = ['nome', 'id_assinante', 'id_arquivo'];

    protected $with = ['arquivo'];

    public function arquivo()
    {
        return $this->belongsTo(Arquivos::class, 'id_arquivo');
    }
}
