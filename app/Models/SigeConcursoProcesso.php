<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
        'icone_processo',
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

    public static function statusDefinicoes(): array
    {
        return [
            'rascunho' => [
                'titulo' => 'Rascunho',
                'descricao' => 'Processo ainda em estruturacao interna.',
                'badge_class' => 'bg-secondary',
                'accent_class' => 'border-secondary-subtle',
                'color' => '#6c757d',
            ],
            'publicado' => [
                'titulo' => 'Publicado',
                'descricao' => 'Edital publicado e aguardando a janela operacional de inscricoes.',
                'badge_class' => 'bg-primary',
                'accent_class' => 'border-primary-subtle',
                'color' => '#0d6efd',
            ],
            'inscricoes_abertas' => [
                'titulo' => 'Inscricoes abertas',
                'descricao' => 'Candidatos podem acessar o edital e concluir a inscricao.',
                'badge_class' => 'bg-success',
                'accent_class' => 'border-success-subtle',
                'color' => '#198754',
            ],
            'inscricoes_encerradas' => [
                'titulo' => 'Inscricoes encerradas',
                'descricao' => 'Janela de inscricao encerrada; processo em preparacao para homologacao.',
                'badge_class' => 'bg-secondary',
                'accent_class' => 'border-secondary-subtle',
                'color' => '#6c757d',
            ],
            'em_andamento' => [
                'titulo' => 'Em andamento',
                'descricao' => 'Fluxo operacional avancando entre homologacao, distribuicao e publicacoes.',
                'badge_class' => 'bg-info text-dark',
                'accent_class' => 'border-info-subtle',
                'color' => '#0dcaf0',
            ],
            'finalizado' => [
                'titulo' => 'Finalizado',
                'descricao' => 'Ciclo operacional encerrado e resultado consolidado.',
                'badge_class' => 'bg-dark',
                'accent_class' => 'border-dark-subtle',
                'color' => '#212529',
            ],
            'suspenso' => [
                'titulo' => 'Suspenso',
                'descricao' => 'Fluxo temporariamente interrompido por decisao administrativa.',
                'badge_class' => 'bg-warning text-dark',
                'accent_class' => 'border-warning-subtle',
                'color' => '#ffc107',
            ],
        ];
    }

    public static function etapasFluxoDefinicoes(): array
    {
        return [
            'cadastro' => [
                'titulo' => 'Cadastro do processo',
                'descricao' => 'Estrutura inicial, regras do edital e configuracoes do fluxo.',
                'icone' => 'fa-pen-ruler',
                'route_name' => 'sigeconcursos.processos.edit',
                'cta' => 'Editar estrutura',
            ],
            'inscricoes' => [
                'titulo' => 'Inscricoes',
                'descricao' => 'Periodo de inscricao, coleta de dados, documentos e opcoes do candidato.',
                'icone' => 'fa-file-signature',
                'route_name' => 'sigeconcursos.processos.inscricoes',
                'cta' => 'Acompanhar inscricoes',
            ],
            'homologacao_inscricoes' => [
                'titulo' => 'Homologacao das inscricoes',
                'descricao' => 'Analise administrativa, deferimentos e indeferimentos.',
                'icone' => 'fa-clipboard-check',
                'route_name' => 'sigeconcursos.processos.inscricoes',
                'cta' => 'Homologar candidaturas',
            ],
            'distribuicao_locais' => [
                'titulo' => 'Distribuicao por locais',
                'descricao' => 'Separacao automatica dos candidatos deferidos entre os locais de prova.',
                'icone' => 'fa-map-location-dot',
                'route_name' => 'sigeconcursos.processos.distribuicao-locais',
                'cta' => 'Distribuir por locais',
            ],
            'distribuicao_salas' => [
                'titulo' => 'Distribuicao por salas',
                'descricao' => 'Organizacao dos candidatos em salas e ajustes manuais.',
                'icone' => 'fa-door-open',
                'route_name' => 'sigeconcursos.processos.distribuicao-salas',
                'cta' => 'Distribuir por salas',
            ],
            'local_prova_liberado' => [
                'titulo' => 'Local de prova liberado',
                'descricao' => 'Consulta do local e sala de prova disponibilizada ao candidato.',
                'icone' => 'fa-bullhorn',
                'route_name' => 'sigeconcursos.processos.distribuicao-salas',
                'cta' => 'Conferir publicacao',
            ],
            'etapas_finais' => [
                'titulo' => 'Etapas finais',
                'descricao' => 'Aplicacao, resultados, recursos e publicacoes finais.',
                'icone' => 'fa-flag-checkered',
                'route_name' => 'sigeconcursos.processos.show',
                'cta' => 'Fechar ciclo',
            ],
        ];
    }

    public function statusApresentacao(): string
    {
        if ($this->status === 'suspenso') {
            return 'suspenso';
        }

        if ($this->status === 'finalizado' || $this->resultadoPublicado()) {
            return 'finalizado';
        }

        if ($this->inscricoesAbertasAgora()) {
            return 'inscricoes_abertas';
        }

        if ($this->status === 'inscricoes_abertas' && $this->data_inicio_inscricoes && now()->lt($this->data_inicio_inscricoes)) {
            return 'publicado';
        }

        if ($this->status === 'inscricoes_abertas' && $this->data_fim_inscricoes && now()->gt($this->data_fim_inscricoes)) {
            return 'inscricoes_encerradas';
        }

        $etapaAtual = $this->etapaFluxoAtualCalculada();

        if (in_array($etapaAtual, ['homologacao_inscricoes', 'distribuicao_locais', 'distribuicao_salas', 'local_prova_liberado', 'etapas_finais'], true)) {
            return 'em_andamento';
        }

        if ($this->inscricoesEncerradas()) {
            return 'inscricoes_encerradas';
        }

        if ($this->status === 'publicado') {
            return 'publicado';
        }

        if ($this->status === 'rascunho' && $etapaAtual === 'cadastro') {
            return 'rascunho';
        }

        return $this->status ?: 'rascunho';
    }

    public function statusApresentacaoDefinicao(): array
    {
        $status = $this->statusApresentacao();

        return array_merge(static::statusDefinicoes()[$status] ?? static::statusDefinicoes()['rascunho'], [
            'chave' => $status,
        ]);
    }

    public function statusManualBloqueado(): bool
    {
        return in_array($this->status, ['suspenso', 'finalizado'], true);
    }

    public function etapaFluxoAtualCalculada(): string
    {
        $indicadores = $this->indicadoresOperacionais();

        if (
            $this->status === 'rascunho'
            && $indicadores['inscricoes_total'] === 0
            && $indicadores['distribuidos_local'] === 0
            && $indicadores['distribuidos_sala'] === 0
        ) {
            return 'cadastro';
        }

        if ($this->status === 'finalizado' || $this->resultadoPublicado()) {
            return 'etapas_finais';
        }

        if ($this->localProvaPublicado()) {
            return 'local_prova_liberado';
        }

        if ($indicadores['distribuidos_sala'] > 0) {
            return 'distribuicao_salas';
        }

        if ($indicadores['distribuidos_local'] > 0 || ($indicadores['inscricoes_deferidas'] > 0 && $indicadores['inscricoes_pendentes'] === 0)) {
            return 'distribuicao_locais';
        }

        if ($this->inscricoesEncerradas() || $indicadores['inscricoes_total'] > 0) {
            return 'homologacao_inscricoes';
        }

        if ($this->status !== 'rascunho') {
            return 'inscricoes';
        }

        return 'cadastro';
    }

    public function etapaFluxoAtualDefinicao(): array
    {
        $chave = $this->etapaFluxoAtualCalculada();

        return array_merge(static::etapasFluxoDefinicoes()[$chave] ?? static::etapasFluxoDefinicoes()['cadastro'], [
            'chave' => $chave,
        ]);
    }

    public function etapaManualBloqueada(): bool
    {
        return $this->statusManualBloqueado();
    }

    public function sincronizacaoFluxo(): array
    {
        return [
            'status' => $this->statusManualBloqueado() ? $this->status : $this->statusApresentacao(),
            'etapa_fluxo_atual' => $this->etapaManualBloqueada() ? $this->etapa_fluxo_atual : $this->etapaFluxoAtualCalculada(),
        ];
    }

    public function progressoOperacionalPercentual(): int
    {
        $ordem = array_keys(static::etapasFluxoDefinicoes());
        $indiceAtual = array_search($this->etapaFluxoAtualCalculada(), $ordem, true);

        if ($indiceAtual === false || count($ordem) <= 1) {
            return 0;
        }

        return (int) round(($indiceAtual / (count($ordem) - 1)) * 100);
    }

    public function indicadoresOperacionais(): array
    {
        $inscricoes = $this->relationLoaded('inscricoes')
            ? $this->inscricoes
            : $this->inscricoes()->get(['id_inscricao', 'status_inscricao', 'status_isencao']);

        $inscricoesTotal = $inscricoes->count();
        $inscricoesDeferidas = $inscricoes->where('status_inscricao', 'deferido')->count();
        $inscricoesIndeferidas = $inscricoes->where('status_inscricao', 'indeferido')->count();
        $inscricoesPendentes = $inscricoes->where('status_inscricao', 'inscrito')->count();
        $isencoesPendentes = $inscricoes->where('status_isencao', 'pendente')->count();

        $processoLocais = $this->relationLoaded('processoLocais')
            ? $this->processoLocais
            : $this->processoLocais()->with('localProva.salas')->get();

        $locaisConfigurados = $processoLocais->count();
        $salasAtivas = $processoLocais->sum(function ($processoLocal) {
            return $processoLocal->localProva?->salas?->where('ativo', true)->count() ?? 0;
        });

        $distribuidosLocal = SigeConcursoInscricaoLocal::whereHas('processoLocal', function ($query) {
            $query->where('fk_id_processo', $this->id_processo);
        })->count();

        $distribuidosSala = SigeConcursoInscricaoSala::whereHas('sala.localProva.processos', function ($query) {
            $query->where('fk_id_processo', $this->id_processo);
        })->count();

        return [
            'cargos_configurados' => $this->relationLoaded('processoCargos') ? $this->processoCargos->count() : $this->processoCargos()->count(),
            'locais_configurados' => $locaisConfigurados,
            'salas_ativas' => $salasAtivas,
            'documentos_exigidos' => $this->relationLoaded('documentosExigidos') ? $this->documentosExigidos->count() : $this->documentosExigidos()->count(),
            'inscricoes_total' => $inscricoesTotal,
            'inscricoes_deferidas' => $inscricoesDeferidas,
            'inscricoes_indeferidas' => $inscricoesIndeferidas,
            'inscricoes_pendentes' => $inscricoesPendentes,
            'isencoes_pendentes' => $isencoesPendentes,
            'distribuidos_local' => $distribuidosLocal,
            'distribuidos_sala' => $distribuidosSala,
        ];
    }

    public function fluxoOperacional(): array
    {
        $etapas = static::etapasFluxoDefinicoes();
        $chaves = array_keys($etapas);
        $etapaAtual = $this->etapaFluxoAtualCalculada();
        $indiceAtual = array_search($etapaAtual, $chaves, true);

        if ($indiceAtual === false) {
            $indiceAtual = 0;
        }

        $indicadores = $this->indicadoresOperacionais();
        $fluxo = [];

        foreach ($chaves as $indice => $chave) {
            $etapa = $etapas[$chave];
            $concluida = $this->etapaConcluida($chave, $indicadores);
            $situacao = 'planejada';

            if ($concluida || $indice < $indiceAtual) {
                $situacao = 'concluida';
            } elseif ($indice === $indiceAtual) {
                $situacao = 'atual';
            } elseif ($indice === ($indiceAtual + 1)) {
                $situacao = 'proxima';
            }

            $fluxo[] = [
                'chave' => $chave,
                'titulo' => $etapa['titulo'],
                'descricao' => $etapa['descricao'],
                'situacao' => $situacao,
                'icone' => $etapa['icone'] ?? 'fa-circle',
                'cta' => $etapa['cta'] ?? null,
                'route_name' => $etapa['route_name'] ?? null,
                'resumo' => $this->resumoEtapa($chave, $indicadores),
            ];
        }

        return $fluxo;
    }

    public function proximaAcaoOperacional(): ?array
    {
        foreach ($this->fluxoOperacional() as $etapa) {
            if (in_array($etapa['situacao'], ['atual', 'proxima'], true)) {
                return $etapa;
            }
        }

        return Arr::last($this->fluxoOperacional());
    }

    public function inscricoesAbertasAgora(): bool
    {
        if ($this->status === 'suspenso' || $this->status === 'finalizado' || $this->status === 'rascunho') {
            return false;
        }

        if ($this->data_inicio_inscricoes && now()->lt($this->data_inicio_inscricoes)) {
            return false;
        }

        if ($this->data_fim_inscricoes && now()->gt($this->data_fim_inscricoes)) {
            return false;
        }

        return in_array($this->status, ['publicado', 'inscricoes_abertas'], true);
    }

    public function inscricoesEncerradas(): bool
    {
        if ($this->status === 'finalizado' || $this->status === 'suspenso' || $this->status === 'rascunho') {
            return false;
        }

        if ($this->data_fim_inscricoes && now()->gt($this->data_fim_inscricoes)) {
            return true;
        }

        return $this->status === 'inscricoes_encerradas';
    }

    public function localProvaPublicado(): bool
    {
        return in_array($this->etapa_fluxo_atual, ['local_prova_liberado', 'etapas_finais'], true) || $this->status === 'finalizado';
    }

    public function resultadoPublicado(): bool
    {
        return $this->status === 'finalizado';
    }

    private function etapaConcluida(string $chave, array $indicadores): bool
    {
        return match ($chave) {
            'cadastro' => $this->status !== 'rascunho',
            'inscricoes' => $this->inscricoesEncerradas() || $this->inscricoesAbertasAgora() || $indicadores['inscricoes_total'] > 0,
            'homologacao_inscricoes' => $indicadores['inscricoes_total'] > 0
                && $indicadores['inscricoes_pendentes'] === 0,
            'distribuicao_locais' => $indicadores['inscricoes_deferidas'] > 0
                && $indicadores['distribuidos_local'] >= $indicadores['inscricoes_deferidas'],
            'distribuicao_salas' => $indicadores['distribuidos_local'] > 0
                && $indicadores['distribuidos_sala'] >= $indicadores['distribuidos_local'],
            'local_prova_liberado' => $this->localProvaPublicado(),
            'etapas_finais' => $this->resultadoPublicado(),
            default => false,
        };
    }

    private function resumoEtapa(string $chave, array $indicadores): string
    {
        return match ($chave) {
            'cadastro' => $indicadores['cargos_configurados'] . ' cargo(s), ' . $indicadores['locais_configurados'] . ' local(is) e ' . $indicadores['documentos_exigidos'] . ' documento(s) configurado(s).',
            'inscricoes' => $indicadores['inscricoes_total'] . ' inscricao(oes) registradas ate o momento.',
            'homologacao_inscricoes' => $indicadores['inscricoes_pendentes'] . ' inscricao(oes) pendente(s), ' . $indicadores['isencoes_pendentes'] . ' isencao(oes) aguardando analise.',
            'distribuicao_locais' => $indicadores['distribuidos_local'] . ' de ' . $indicadores['inscricoes_deferidas'] . ' deferidos distribuidos por local.',
            'distribuicao_salas' => $indicadores['distribuidos_sala'] . ' candidato(s) alocados em ' . $indicadores['salas_ativas'] . ' sala(s) ativa(s).',
            'local_prova_liberado' => $this->localProvaPublicado()
                ? 'Local e sala de prova ja liberados para consulta do candidato.'
                : 'Aguardando publicacao para os candidatos deferidos.',
            'etapas_finais' => $this->resultadoPublicado()
                ? 'Processo marcado como finalizado e pronto para historico.'
                : 'Pendencia de fechamento, resultados e publicacoes finais.',
            default => '',
        };
    }

    public static function formatarNumeroProcesso(int $id): string
    {
        return sprintf('SC-%s-%04d', date('Y'), $id);
    }
}