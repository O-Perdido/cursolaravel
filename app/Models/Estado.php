<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'tb_estado';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = [
        'nm_estado',
        'uf_estado',
        'cd_uf'
    ];

    public function cidades()
    {
        return $this->hasMany(Cidade::class, 'fk_id_estado', 'id_estado');
    }
}
