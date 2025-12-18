<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $data_inicio_estagio
 * @property string|null $data_fim_estagio
 * @property int|null $saldo_recesso
 */
class Termo extends Model
{
    use HasFactory;

    // Especifica o nome da tabela associada ao model
    protected $table = 'tb_termos';

    protected $primaryKey = 'id_termo';

    // Especifica os campos que podem ser atribuídos em massa (mass assignable)
    protected $fillable = [
        'id_termo',
        'numero_termo',
        'ano_termo',
        'data',
        'hora',
        'fk_id_estagiario',
        'fk_id_empresa',
        'fk_id_local',
        'fk_id_escola',
        'fk_id_supervisor',
        'fk_id_supervisor_fixo',
        'desc_atividades',
        'desc_atividades_fixo',
        'nome_orientador',
        'nome_orientador_fixo',
        'cargo_orientador',
        'cargo_orientador_fixo',
        'data_inicio_estagio',
        'data_fim_estagio',
        'data_fim_estagio_fixo',
        'horario',
        'horario_fixo',
        'valor_bolsa',
        'valor_bolsa_fixo',
        'auxilio_transporte',
        'auxilio_transporte_fixo',
        'lotacao',
        // Recesso
        'saldo_recesso',
        // Campos ZapSign
        'zapsign_doc_token',
        'zapsign_status',
        'zapsign_enviado_em',
            'fk_id_vaga',
            'vinculo',
    ];
    public function vaga()
    {
        return $this->belongsTo(Vaga::class, 'fk_id_vaga', 'id_vaga');
    }

    public function estagiario()
    {
        return $this->belongsTo(Estagiario::class, 'fk_id_estagiario');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa');
    }
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'fk_id_escola');
    }
    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'fk_id_supervisor');
    }

    public function supervisorFixo()
    {
        return $this->belongsTo(Supervisor::class, 'fk_id_supervisor_fixo');
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'fk_id_local', 'id_local');
    }

    public function rescisao()
    {
        return $this->hasOne(Rescisao::class, 'fk_id_termo', 'id_termo');
    }

    public function alteracaoTermo()
    {
        return $this->hasMany(AlteracaoTermo::class, 'fk_id_termo');
    }

    public function folhaTermo()
    {
        return $this->hasMany(FolhasTermos::class, 'fk_id_termo');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'fk_id_cidade');
    }

    public function concessoesRecesso()
    {
        return $this->hasMany(ConcessaoRecesso::class, 'fk_id_termo', 'id_termo');
    }
}
