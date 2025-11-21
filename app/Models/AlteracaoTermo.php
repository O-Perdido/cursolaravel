<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlteracaoTermo extends Model
{
    // Especifica o nome da tabela associada ao model
    protected $table = 'tb_alteracao_termo';

    protected $primaryKey = 'id_alteracao';

    // Especifica os campos que podem ser atribuídos em massa (mass assignable)
    protected $fillable = [
        'id_alteracao',
        'fk_id_termo',
        'fk_id_supervisor',
        'desc_atividades_alteracao',
        'nome_orientador_alteracao',
        'cargo_orientador_alteracao',
        'data_fim_estagio_alteracao',
        'horario_alteracao',
        'valor_bolsa_alteracao',
        'auxilio_transporte_alteracao',
        'data_alteracao',
        'descricao',
        'old_fk_id_supervisor',
        'old_nome_orientador',
        'old_cargo_orientador',
        'old_data_fim_estagio',
        'old_horario',
        'old_valor_bolsa',
        'old_auxilio_transporte',
        'old_desc_atividades',
    ];

    public function termo()
    {
        return $this->belongsTo(Termo::class, 'fk_id_termo');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'fk_id_supervisor');
    }
}
