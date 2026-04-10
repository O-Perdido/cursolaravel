<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VagaCandidatura extends Model
{
    protected $table = 'tb_vaga_candidaturas';

    protected $primaryKey = 'id_candidatura';

    protected $fillable = [
        'fk_id_vaga',
        'fk_id_estagiario',
        'status_candidatura',
        'curriculo_arquivo',
        'observacoes_estagiario',
        'observacoes_internas',
        'analisado_em',
        'fk_id_usuario_analisou',
        'notificado_em',
    ];

    protected $casts = [
        'analisado_em' => 'datetime',
        'notificado_em' => 'datetime',
    ];

    public const STATUS_ENVIADA = 'enviada';
    public const STATUS_EM_ANALISE = 'em_analise';
    public const STATUS_ENTREVISTA = 'entrevista';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_NAO_SELECIONADO = 'nao_selecionado';
    public const STATUS_DESISTENTE = 'desistente';
    public const STATUS_DEFINIDO = 'definido';

    public static function statusDisponiveis(): array
    {
        return [
            self::STATUS_ENVIADA => 'Enviada',
            self::STATUS_EM_ANALISE => 'Em Análise',
            self::STATUS_ENTREVISTA => 'Entrevista',
            self::STATUS_APROVADO => 'Aprovado',
            self::STATUS_NAO_SELECIONADO => 'Não selecionado',
            self::STATUS_DESISTENTE => 'Desistente',
            self::STATUS_DEFINIDO => 'Definido para a vaga',
        ];
    }

    public function vaga()
    {
        return $this->belongsTo(Vaga::class, 'fk_id_vaga', 'id_vaga');
    }

    public function estagiario()
    {
        return $this->belongsTo(Estagiario::class, 'fk_id_estagiario', 'id_estagiario');
    }

    public function usuarioAnalisou()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_analisou');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusDisponiveis()[$this->status_candidatura] ?? ucfirst(str_replace('_', ' ', $this->status_candidatura));
    }
}