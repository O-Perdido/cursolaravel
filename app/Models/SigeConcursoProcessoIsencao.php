<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoProcessoIsencao extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_processo_isencoes';

    protected $primaryKey = 'id_isencao';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_processo',
        'titulo',
        'descricao',
        'data_inicio',
        'data_fim',
        'exige_comprovacao',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
            'exige_comprovacao' => 'boolean',
        ];
    }

    public function processo()
    {
        return $this->belongsTo(SigeConcursoProcesso::class, 'fk_id_processo', 'id_processo');
    }
}