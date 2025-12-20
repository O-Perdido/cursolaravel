<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoChamado extends Model
{
    use HasFactory;

    protected $table = 'tb_tipos_chamados';
    protected $primaryKey = 'id_tipo_chamado';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'sistema',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'sistema' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    /**
     * Relacionamento com chamados
     */
    public function chamados()
    {
        return $this->hasMany(Chamado::class, 'fk_id_tipo_chamado', 'id_tipo_chamado');
    }

    /**
     * Scope para tipos ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para ordenar
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    /**
     * Verifica se é um tipo do sistema (Rescisão ou Alteração)
     */
    public function isSistema(): bool
    {
        return $this->sistema === true;
    }

    /**
     * Verifica se é tipo de Rescisão
     */
    public function isRescisao(): bool
    {
        return $this->slug === 'rescisao';
    }

    /**
     * Verifica se é tipo de Alteração
     */
    public function isAlteracao(): bool
    {
        return $this->slug === 'alteracao';
    }
}
