<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;

    protected $table = 'tb_cidade';
    protected $primaryKey = 'id_cidade';
    public $timestamps = false;

    protected $fillable = [
        'nm_cidade',
        'cd_ibge',
        'fk_id_estado'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'fk_id_estado', 'id_estado');
    }
}
