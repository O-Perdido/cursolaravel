<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Chamado extends Model
{
    use HasFactory;

    protected $table = 'tb_chamados';
    protected $primaryKey = 'id_chamado';

    protected $fillable = [
        'protocolo',
        'fk_id_tipo_chamado',
        'fk_id_empresa',
        'fk_id_user_solicitante',
        'fk_id_termo',
        'data_rescisao',
        'motivo_rescisao',
        'descricao_alteracao',
        'titulo',
        'detalhes',
        'anexos',
        'status',
        'observacoes_internas',
        'fk_id_user_responsavel',
        'data_conclusao',
    ];

    protected $casts = [
        'data_rescisao' => 'date',
        'anexos' => 'array',
        'data_conclusao' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Gera protocolo automaticamente ao criar
        static::creating(function ($chamado) {
            if (!$chamado->protocolo) {
                $chamado->protocolo = self::gerarProtocolo();
            }
        });
    }

    /**
     * Gera protocolo único no formato: CHAM-YYYYMMDD-XXXXX
     */
    public static function gerarProtocolo(): string
    {
        $data = date('Ymd');
        $ultimo = self::where('protocolo', 'like', "CHAM-{$data}-%")
            ->orderBy('protocolo', 'desc')
            ->first();

        if ($ultimo) {
            $numero = intval(substr($ultimo->protocolo, -5)) + 1;
        } else {
            $numero = 1;
        }

        return sprintf('CHAM-%s-%05d', $data, $numero);
    }

    /**
     * Relacionamento com tipo de chamado
     */
    public function tipoChamado()
    {
        return $this->belongsTo(TipoChamado::class, 'fk_id_tipo_chamado', 'id_tipo_chamado');
    }

    /**
     * Relacionamento com empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa', 'id_empresa');
    }

    /**
     * Relacionamento com usuário solicitante
     */
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'fk_id_user_solicitante', 'id');
    }

    /**
     * Relacionamento com termo
     */
    public function termo()
    {
        return $this->belongsTo(Termo::class, 'fk_id_termo', 'id_termo');
    }

    /**
     * Relacionamento com usuário responsável
     */
    public function responsavel()
    {
        return $this->belongsTo(User::class, 'fk_id_user_responsavel', 'id');
    }

    /**
     * Scope para chamados de uma empresa
     */
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('fk_id_empresa', $empresaId);
    }

    /**
     * Scope para chamados por status
     */
    public function scopeComStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para chamados pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    /**
     * Badge de status com cores
     */
    public function getStatusBadge(): string
    {
        $badges = [
            'pendente' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendente</span>',
            'em_analise' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Em Análise</span>',
            'em_andamento' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Em Andamento</span>',
            'concluido' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Concluído</span>',
            'cancelado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelado</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    /**
     * Verifica se é chamado de rescisão
     */
    public function isRescisao(): bool
    {
        return $this->tipoChamado && $this->tipoChamado->isRescisao();
    }

    /**
     * Verifica se é chamado de alteração
     */
    public function isAlteracao(): bool
    {
        return $this->tipoChamado && $this->tipoChamado->isAlteracao();
    }
}
