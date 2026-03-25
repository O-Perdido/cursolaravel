<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoLocalProva extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_locais_prova';

    protected $primaryKey = 'id_local_prova';

    public $timestamps = false;

    protected $fillable = [
        'nome_local',
        'numero_cep',
        'endereco',
        'numero_endereco',
        'complemento_endereco',
        'bairro',
        'fk_id_cidade',
        'observacoes',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'fk_id_cidade', 'id_cidade');
    }

    public function salas()
    {
        return $this->hasMany(SigeConcursoSala::class, 'fk_id_local_prova', 'id_local_prova');
    }

    public function processos()
    {
        return $this->hasMany(SigeConcursoProcessoLocal::class, 'fk_id_local_prova', 'id_local_prova');
    }
}