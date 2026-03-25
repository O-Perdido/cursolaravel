<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoCargo extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_cargos';

    protected $primaryKey = 'id_cargo';

    public $timestamps = false;

    protected $fillable = [
        'nome_cargo',
        'descricao',
        'escolaridade_minima',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function processos()
    {
        return $this->hasMany(SigeConcursoProcessoCargo::class, 'fk_id_cargo', 'id_cargo');
    }
}