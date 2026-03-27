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
        'etapa_fluxo_atual',
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
        'permite_condicao_especial',
        'exige_documento_condicao_especial',
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
            'permite_condicao_especial' => 'boolean',
            'exige_documento_condicao_especial' => 'boolean',
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

    public function documentosExigidos()
    {
        return $this->hasMany(SigeConcursoProcessoDocumentoExigido::class, 'fk_id_processo', 'id_processo')
            ->orderBy('ordem_exibicao')
            ->orderBy('id_documento_exigido');
    }

    public function inscricoes()
    {
        return $this->hasMany(SigeConcursoInscricao::class, 'fk_id_processo', 'id_processo');
    }

    public static function etapasFluxoDefinicoes(): array
    {
        return [
            'cadastro' => [
                'titulo' => 'Cadastro do processo',
                'descricao' => 'Estrutura inicial, regras do edital e configuracoes do fluxo.',
            ],
            'inscricoes' => [
                'titulo' => 'Inscricoes',
                'descricao' => 'Periodo de inscricao, coleta de dados, documentos e opcoes do candidato.',
            ],
            'homologacao_inscricoes' => [
                'titulo' => 'Homologacao das inscricoes',
                'descricao' => 'Analise administrativa, deferimentos e indeferimentos.',
            ],
            'distribuicao_locais' => [
                'titulo' => 'Distribuicao por locais',
                'descricao' => 'Separacao automatica dos candidatos deferidos entre os locais de prova.',
            ],
            'distribuicao_salas' => [
                'titulo' => 'Distribuicao por salas',
                'descricao' => 'Organizacao dos candidatos em salas e ajustes manuais.',
            ],
            'local_prova_liberado' => [
                'titulo' => 'Local de prova liberado',
                'descricao' => 'Consulta do local e sala de prova disponibilizada ao candidato.',
            ],
            'etapas_finais' => [
                'titulo' => 'Etapas finais',
                'descricao' => 'Aplicacao, resultados, recursos e publicacoes finais.',
            ],
        ];
    }

    public function fluxoOperacional(): array
    {
        $etapas = static::etapasFluxoDefinicoes();
        $chaves = array_keys($etapas);
        $indiceAtual = array_search($this->etapa_fluxo_atual ?: 'cadastro', $chaves, true);

        if ($indiceAtual === false) {
            $indiceAtual = 0;
        }

        $fluxo = [];

        foreach ($chaves as $indice => $chave) {
            $etapa = $etapas[$chave];
            $situacao = 'pendente';

            if ($indice < $indiceAtual) {
                $situacao = 'concluida';
            } elseif ($indice === $indiceAtual) {
                $situacao = 'atual';
            }

            $fluxo[] = [
                'chave' => $chave,
                'titulo' => $etapa['titulo'],
                'descricao' => $etapa['descricao'],
                'situacao' => $situacao,
            ];
        }

        return $fluxo;
    }

    public static function formatarNumeroProcesso(int $id): string
    {
        return sprintf('SC-%s-%04d', date('Y'), $id);
    }
}