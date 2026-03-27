<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoSala extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_salas';

    protected $primaryKey = 'id_sala';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_local_prova',
        'nome_sala',
        'bloco',
        'capacidade_maxima',
        'observacoes',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'capacidade_maxima' => 'integer',
        ];
    }

    public function localProva()
    {
        return $this->belongsTo(SigeConcursoLocalProva::class, 'fk_id_local_prova', 'id_local_prova');
    }

    public function inscricoesAtribuidas()
    {
        return $this->hasMany(SigeConcursoInscricaoSala::class, 'fk_id_sala', 'id_sala');
    }
}