<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoProcessoCargo extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_processo_cargos';

    protected $primaryKey = 'id_processo_cargo';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_processo',
        'fk_id_cargo',
        'quantidade_vagas',
        'quantidade_cadastro_reserva',
        'valor_remuneracao',
        'valor_taxa_inscricao',
        'carga_horaria',
        'requisitos_especificos',
    ];

    protected function casts(): array
    {
        return [
            'quantidade_vagas' => 'integer',
            'quantidade_cadastro_reserva' => 'integer',
            'valor_remuneracao' => 'decimal:2',
            'valor_taxa_inscricao' => 'decimal:2',
        ];
    }

    public function processo()
    {
        return $this->belongsTo(SigeConcursoProcesso::class, 'fk_id_processo', 'id_processo');
    }

    public function cargo()
    {
        return $this->belongsTo(SigeConcursoCargo::class, 'fk_id_cargo', 'id_cargo');
    }
}