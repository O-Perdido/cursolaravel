<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id_avaliacao
 * @property int $fk_id_termo
 * @property int $fk_id_supervisor
 * @property string $tipo_avaliacao
 * @property string $status
 * @property string $token_compartilhamento
 * @property array $questoes_respostas
 * @property string|null $respondida_em
 * @property string|null $respondida_por
 */
class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'tb_avaliacoes';

    protected $primaryKey = 'id_avaliacao';

    protected $fillable = [
        'id_avaliacao',
        'fk_id_termo',
        'fk_id_supervisor',
        'tipo_avaliacao', // 'seis_meses' ou 'finalizacao'
        'status', // 'pendente', 'respondida', 'revisada'
        'token_compartilhamento',
        'respondida_em',
        'respondida_por', // email de quem respondeu
        'questoes_respostas', // JSON com estrutura: [{"questao": "...", "resposta": "...", "ordem": 1}, ...]
    ];

    protected $casts = [
        'questoes_respostas' => 'array',
        'respondida_em' => 'datetime',
    ];

    public function termo()
    {
        return $this->belongsTo(Termo::class, 'fk_id_termo', 'id_termo');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'fk_id_supervisor', 'id_supervisor');
    }

    /**
     * Verifica se a avaliação pode ser acessada via link de compartilhamento
     */
    public function podeSerAcessada(): bool
    {
        return $this->status === 'pendente' && !is_null($this->token_compartilhamento);
    }

    /**
     * Gera um token único para compartilhamento
     */
    public static function gerarTokenCompartilhamento(): string
    {
        return bin2hex(random_bytes(32));
    }
}
