<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoInscricao extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_inscricoes';

    protected $primaryKey = 'id_inscricao';

    protected $fillable = [
        'fk_id_processo',
        'fk_id_candidato',
        'numero_inscricao',
        'modalidade_concorrencia',
        'solicitou_nome_social',
        'nome_social',
        'status_inscricao',
        'aceite_edital',
        'solicitou_condicao_especial',
        'descricao_condicao_especial',
        'caminho_documento_condicao_especial',
        'solicitou_isencao',
        'fk_id_isencao',
        'justificativa_isencao',
        'status_isencao',
        'parecer_isencao',
        'valor_taxa_aplicada',
        'status_pagamento',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'solicitou_nome_social' => 'boolean',
            'aceite_edital' => 'boolean',
            'solicitou_condicao_especial' => 'boolean',
            'solicitou_isencao' => 'boolean',
            'fk_id_isencao' => 'integer',
            'valor_taxa_aplicada' => 'decimal:2',
        ];
    }

    public function processo()
    {
        return $this->belongsTo(SigeConcursoProcesso::class, 'fk_id_processo', 'id_processo');
    }

    public function candidato()
    {
        return $this->belongsTo(SigeConcursoCandidato::class, 'fk_id_candidato', 'id_candidato');
    }

    public function documentos()
    {
        return $this->hasMany(SigeConcursoInscricaoDocumento::class, 'fk_id_inscricao', 'id_inscricao');
    }

    public function isencao()
    {
        return $this->belongsTo(SigeConcursoProcessoIsencao::class, 'fk_id_isencao', 'id_isencao');
    }

    public function documentosIsencao()
    {
        return $this->hasMany(SigeConcursoInscricaoIsencaoDocumento::class, 'fk_id_inscricao', 'id_inscricao');
    }

    public function localAtribuido()
    {
        return $this->hasOne(SigeConcursoInscricaoLocal::class, 'fk_id_inscricao', 'id_inscricao');
    }

    public function salaAtribuida()
    {
        return $this->hasOne(SigeConcursoInscricaoSala::class, 'fk_id_inscricao', 'id_inscricao');
    }

    public static function gerarNumeroInscricao(int $idProcesso): string
    {
        $ano = date('Y');
        $sequencial = (int) static::where('fk_id_processo', $idProcesso)
            ->whereNotNull('numero_inscricao')
            ->lockForUpdate()
            ->selectRaw("COALESCE(MAX(CAST(SUBSTRING_INDEX(numero_inscricao, '-', -1) AS UNSIGNED)), 0) as max_seq")
            ->value('max_seq');

        $proximo = $sequencial + 1;

        return sprintf('SCI-%s-%04d-%04d', $ano, $idProcesso, $proximo);
    }

    public function modalidadeLabel(): string
    {
        return $this->modalidade_concorrencia === 'pcd' ? 'PCD' : 'Ampla Concorrência';
    }
}