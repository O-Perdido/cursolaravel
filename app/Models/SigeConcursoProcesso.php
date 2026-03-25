<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoProcesso extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_processos';

    protected $primaryKey = 'id_processo';

    public $timestamps = false;

    protected $fillable = [
        'numero_processo',
        'numero_edital',
        'titulo',
        'tipo_processo',
        'fk_id_empresa',
        'status',
        'resumo',
        'descricao',
        'requisitos_gerais',
        'observacoes',
        'data_publicacao',
        'data_inicio_inscricoes',
        'data_fim_inscricoes',
        'data_prova',
        'data_resultado_final',
        'fases',
        'exige_aceite_edital',
        'permite_escolha_local_prova',
        'possui_taxa_inscricao',
        'valor_taxa_padrao',
        'permite_ampla_concorrencia',
        'permite_pcd',
    ];

    protected function casts(): array
    {
        return [
            'data_publicacao' => 'datetime',
            'data_inicio_inscricoes' => 'datetime',
            'data_fim_inscricoes' => 'datetime',
            'data_prova' => 'datetime',
            'data_resultado_final' => 'datetime',
            'fases' => 'array',
            'exige_aceite_edital' => 'boolean',
            'permite_escolha_local_prova' => 'boolean',
            'possui_taxa_inscricao' => 'boolean',
            'permite_ampla_concorrencia' => 'boolean',
            'permite_pcd' => 'boolean',
            'valor_taxa_padrao' => 'decimal:2',
        ];
    }

    public function empresa()
    {
        return $this->belongsTo(SigeConcursoEmpresa::class, 'fk_id_empresa', 'id_empresa');
    }

    public function processoCargos()
    {
        return $this->hasMany(SigeConcursoProcessoCargo::class, 'fk_id_processo', 'id_processo');
    }

    public function processoLocais()
    {
        return $this->hasMany(SigeConcursoProcessoLocal::class, 'fk_id_processo', 'id_processo');
    }

    public function isencoes()
    {
        return $this->hasMany(SigeConcursoProcessoIsencao::class, 'fk_id_processo', 'id_processo');
    }

    public function arquivos()
    {
        return $this->hasMany(SigeConcursoProcessoArquivo::class, 'fk_id_processo', 'id_processo');
    }

    public static function formatarNumeroProcesso(int $id): string
    {
        return sprintf('SC-%s-%04d', date('Y'), $id);
    }
}