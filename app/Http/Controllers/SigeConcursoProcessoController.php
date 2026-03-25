<?php

namespace App\Http\Controllers;

use App\Models\SigeConcursoCargo;
use App\Models\SigeConcursoEmpresa;
use App\Models\SigeConcursoLocalProva;
use App\Models\SigeConcursoProcesso;
use App\Models\SigeConcursoProcessoArquivo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SigeConcursoProcessoController extends Controller
{
    public function index(Request $request)
    {
        $query = SigeConcursoProcesso::with(['empresa', 'processoCargos.cargo', 'processoLocais.localProva']);

        if ($request->filled('titulo')) {
            $query->where('titulo', 'like', '%' . $request->titulo . '%');
        }

        if ($request->filled('numero_edital')) {
            $query->where('numero_edital', 'like', '%' . $request->numero_edital . '%');
        }

        if ($request->filled('fk_id_empresa')) {
            $query->where('fk_id_empresa', $request->fk_id_empresa);
        }

        if ($request->filled('tipo_processo')) {
            $query->where('tipo_processo', $request->tipo_processo);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_publicacao_inicio')) {
            $query->where('data_publicacao', '>=', $request->data_publicacao_inicio . ' 00:00:00');
        }

        if ($request->filled('data_publicacao_fim')) {
            $query->where('data_publicacao', '<=', $request->data_publicacao_fim . ' 23:59:59');
        }

        if ($request->boolean('ordem_cadastro')) {
            $query->orderByDesc('id_processo');
        } else {
            $query->orderByRaw('data_publicacao IS NULL, data_publicacao DESC')
                ->orderByDesc('id_processo');
        }

        $perPage = $this->resolvePerPage($request->input('per_page'), $query->count());
        $processos = $query->paginate($perPage)->appends($request->query());
        $orgaos = SigeConcursoEmpresa::orderBy('nome_razao_social')->get(['id_empresa', 'nome_razao_social']);

        return view('sigeconcursos.processos.index', compact('processos', 'orgaos'));
    }

    public function create()
    {
        $orgaos = SigeConcursoEmpresa::orderBy('nome_razao_social')->get();
        $cargos = SigeConcursoCargo::where('ativo', true)->orderBy('nome_cargo')->get();
        $locais = SigeConcursoLocalProva::with('cidade.estado')->where('ativo', true)->orderBy('nome_local')->get();

        return view('sigeconcursos.processos.create', compact('orgaos', 'cargos', 'locais'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $fases = $this->formatarFases($request->input('fases', []));
        $cargos = $this->formatarCargos($request->input('cargos', []));
        $locais = $this->formatarLocais($request->input('locais', []));
        $isencoes = $this->formatarIsencoes($request->input('isencoes', []));

        if (empty($cargos)) {
            throw ValidationException::withMessages([
                'cargos' => 'Adicione ao menos um cargo ao processo.',
            ]);
        }

        $processo = DB::transaction(function () use ($data, $fases, $cargos, $locais, $isencoes) {
            $processo = SigeConcursoProcesso::create(array_merge($data, [
                'fases' => !empty($fases) ? $fases : null,
                'numero_processo' => null,
            ]));

            $processo->update([
                'numero_processo' => SigeConcursoProcesso::formatarNumeroProcesso($processo->id_processo),
            ]);

            $processo->processoCargos()->createMany($cargos);
            $processo->processoLocais()->createMany($locais);
            $processo->isencoes()->createMany($isencoes);

            return $processo;
        });

        $this->salvarArquivos($request, $processo);

        return redirect()->route('sigeconcursos.processos.index')
            ->with('success', 'Processo cadastrado com sucesso!');
    }

    public function show($id)
    {
        $processo = SigeConcursoProcesso::with([
            'empresa.cidade.estado',
            'processoCargos.cargo',
            'processoLocais.localProva.cidade.estado',
            'processoLocais.localProva.salas',
            'isencoes',
            'arquivos',
        ])->findOrFail($id);

        return view('sigeconcursos.processos.show', compact('processo'));
    }

    public function edit($id)
    {
        $processo = SigeConcursoProcesso::with(['processoCargos.cargo', 'processoLocais.localProva', 'isencoes', 'arquivos'])
            ->findOrFail($id);
        $orgaos = SigeConcursoEmpresa::orderBy('nome_razao_social')->get();
        $cargos = SigeConcursoCargo::where('ativo', true)->orderBy('nome_cargo')->get();
        $locais = SigeConcursoLocalProva::with('cidade.estado')->where('ativo', true)->orderBy('nome_local')->get();

        return view('sigeconcursos.processos.edit', compact('processo', 'orgaos', 'cargos', 'locais'));
    }

    public function update(Request $request, $id)
    {
        $processo = SigeConcursoProcesso::findOrFail($id);
        $data = $this->validateData($request);
        $fases = $this->formatarFases($request->input('fases', []));
        $cargos = $this->formatarCargos($request->input('cargos', []));
        $locais = $this->formatarLocais($request->input('locais', []));
        $isencoes = $this->formatarIsencoes($request->input('isencoes', []));

        if (empty($cargos)) {
            throw ValidationException::withMessages([
                'cargos' => 'Adicione ao menos um cargo ao processo.',
            ]);
        }

        DB::transaction(function () use ($processo, $data, $fases, $cargos, $locais, $isencoes) {
            $processo->update(array_merge($data, [
                'fases' => !empty($fases) ? $fases : null,
            ]));

            $processo->processoCargos()->delete();
            $processo->processoLocais()->delete();
            $processo->isencoes()->delete();

            $processo->processoCargos()->createMany($cargos);
            $processo->processoLocais()->createMany($locais);
            $processo->isencoes()->createMany($isencoes);
        });

        $this->salvarArquivos($request, $processo);

        return redirect()->route('sigeconcursos.processos.index')
            ->with('success', 'Processo atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $processo = SigeConcursoProcesso::with('arquivos')->findOrFail($id);

        try {
            foreach ($processo->arquivos as $arquivo) {
                if (Storage::disk('public')->exists($arquivo->caminho_arquivo)) {
                    Storage::disk('public')->delete($arquivo->caminho_arquivo);
                }
            }

            $processo->delete();

            return redirect()->route('sigeconcursos.processos.index')
                ->with('success', 'Processo excluído com sucesso!');
        } catch (QueryException $exception) {
            return redirect()->route('sigeconcursos.processos.index')
                ->with('error', 'Não foi possível excluir o processo porque ele possui vínculos no sistema.');
        }
    }

    public function removerArquivo($id)
    {
        $arquivo = SigeConcursoProcessoArquivo::findOrFail($id);

        if (Storage::disk('public')->exists($arquivo->caminho_arquivo)) {
            Storage::disk('public')->delete($arquivo->caminho_arquivo);
        }

        $arquivo->delete();

        return back()->with('success', 'Arquivo removido com sucesso!');
    }

    private function validateData(Request $request): array
    {
        $request->merge([
            'valor_taxa_padrao' => $this->normalizeMoney($request->input('valor_taxa_padrao')),
        ]);

        $data = $request->validate([
            'numero_edital' => ['required', 'string', 'max:100'],
            'titulo' => ['required', 'string', 'max:255'],
            'tipo_processo' => ['required', 'in:concurso_publico,processo_seletivo'],
            'fk_id_empresa' => ['required', 'exists:sigeconcursos_tb_empresas,id_empresa'],
            'status' => ['required', 'in:rascunho,publicado,inscricoes_abertas,inscricoes_encerradas,em_andamento,finalizado,suspenso'],
            'resumo' => ['nullable', 'string'],
            'descricao' => ['nullable', 'string'],
            'requisitos_gerais' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
            'data_publicacao' => ['nullable', 'date'],
            'data_inicio_inscricoes' => ['nullable', 'date'],
            'data_fim_inscricoes' => ['nullable', 'date', 'after_or_equal:data_inicio_inscricoes'],
            'data_prova' => ['nullable', 'date'],
            'data_resultado_final' => ['nullable', 'date'],
            'valor_taxa_padrao' => ['nullable', 'numeric', 'min:0'],
            'fases' => ['nullable', 'array'],
            'fases.*.descricao' => ['nullable', 'string', 'max:255'],
            'fases.*.periodo' => ['nullable', 'string', 'max:255'],
            'cargos' => ['nullable', 'array'],
            'cargos.*.fk_id_cargo' => ['nullable', 'integer', 'exists:sigeconcursos_tb_cargos,id_cargo'],
            'cargos.*.quantidade_vagas' => ['nullable', 'integer', 'min:0'],
            'cargos.*.quantidade_cadastro_reserva' => ['nullable', 'integer', 'min:0'],
            'cargos.*.valor_remuneracao' => ['nullable'],
            'cargos.*.valor_taxa_inscricao' => ['nullable'],
            'cargos.*.carga_horaria' => ['nullable', 'string', 'max:100'],
            'cargos.*.requisitos_especificos' => ['nullable', 'string'],
            'locais' => ['nullable', 'array'],
            'locais.*.fk_id_local_prova' => ['nullable', 'integer', 'exists:sigeconcursos_tb_locais_prova,id_local_prova'],
            'locais.*.observacoes' => ['nullable', 'string'],
            'isencoes' => ['nullable', 'array'],
            'isencoes.*.titulo' => ['nullable', 'string', 'max:255'],
            'isencoes.*.descricao' => ['nullable', 'string'],
            'isencoes.*.data_inicio' => ['nullable', 'date'],
            'isencoes.*.data_fim' => ['nullable', 'date'],
            'arquivos' => ['nullable', 'array'],
            'arquivos.*' => ['nullable', 'file', 'max:5120'],
            'nome_exibicao' => ['nullable', 'array'],
            'nome_exibicao.*' => ['nullable', 'string', 'max:255'],
            'tipo_arquivo' => ['nullable', 'array'],
            'tipo_arquivo.*' => ['nullable', 'string', 'max:50'],
        ], [
            'fk_id_empresa.exists' => 'Selecione um órgão/empresa válido.',
        ]);

        $data['exige_aceite_edital'] = $request->boolean('exige_aceite_edital');
        $data['permite_escolha_local_prova'] = $request->boolean('permite_escolha_local_prova');
        $data['possui_taxa_inscricao'] = $request->boolean('possui_taxa_inscricao');
        $data['permite_ampla_concorrencia'] = $request->boolean('permite_ampla_concorrencia');
        $data['permite_pcd'] = $request->boolean('permite_pcd');

        foreach ($request->input('isencoes', []) as $index => $isencao) {
            $dataInicio = $isencao['data_inicio'] ?? null;
            $dataFim = $isencao['data_fim'] ?? null;

            if ($dataInicio && $dataFim && strtotime($dataFim) < strtotime($dataInicio)) {
                throw ValidationException::withMessages([
                    "isencoes.$index.data_fim" => 'A data final da isenção não pode ser menor que a data inicial.',
                ]);
            }
        }

        unset($data['fases'], $data['cargos'], $data['locais'], $data['isencoes'], $data['arquivos'], $data['nome_exibicao'], $data['tipo_arquivo']);

        return $data;
    }

    private function formatarFases(array $fases): array
    {
        return collect($fases)->map(function ($fase) {
            $descricao = trim($fase['descricao'] ?? '');
            $periodo = trim($fase['periodo'] ?? '');

            if ($descricao === '' && $periodo === '') {
                return null;
            }

            return [
                'descricao' => $descricao,
                'periodo' => $periodo,
            ];
        })->filter()->values()->all();
    }

    private function formatarCargos(array $cargos): array
    {
        return collect($cargos)->map(function ($cargo) {
            $cargoId = $cargo['fk_id_cargo'] ?? null;

            if (!$cargoId) {
                return null;
            }

            return [
                'fk_id_cargo' => (int) $cargoId,
                'quantidade_vagas' => $this->nullableInteger($cargo['quantidade_vagas'] ?? null),
                'quantidade_cadastro_reserva' => $this->nullableInteger($cargo['quantidade_cadastro_reserva'] ?? null),
                'valor_remuneracao' => $this->normalizeMoney($cargo['valor_remuneracao'] ?? null),
                'valor_taxa_inscricao' => $this->normalizeMoney($cargo['valor_taxa_inscricao'] ?? null),
                'carga_horaria' => trim($cargo['carga_horaria'] ?? '') ?: null,
                'requisitos_especificos' => trim($cargo['requisitos_especificos'] ?? '') ?: null,
            ];
        })->filter()->unique('fk_id_cargo')->values()->all();
    }

    private function formatarLocais(array $locais): array
    {
        return collect($locais)->map(function ($local) {
            $localId = $local['fk_id_local_prova'] ?? null;

            if (!$localId) {
                return null;
            }

            return [
                'fk_id_local_prova' => (int) $localId,
                'observacoes' => trim($local['observacoes'] ?? '') ?: null,
            ];
        })->filter()->unique('fk_id_local_prova')->values()->all();
    }

    private function formatarIsencoes(array $isencoes): array
    {
        return collect($isencoes)->map(function ($isencao) {
            $titulo = trim($isencao['titulo'] ?? '');
            $descricao = trim($isencao['descricao'] ?? '');

            if ($titulo === '' && $descricao === '') {
                return null;
            }

            return [
                'titulo' => $titulo !== '' ? $titulo : 'Isenção',
                'descricao' => $descricao !== '' ? $descricao : null,
                'data_inicio' => $isencao['data_inicio'] ?? null,
                'data_fim' => $isencao['data_fim'] ?? null,
                'exige_comprovacao' => filter_var($isencao['exige_comprovacao'] ?? false, FILTER_VALIDATE_BOOL),
            ];
        })->filter()->values()->all();
    }

    private function salvarArquivos(Request $request, SigeConcursoProcesso $processo): void
    {
        if (!$request->hasFile('arquivos')) {
            return;
        }

        foreach ($request->file('arquivos') as $index => $arquivo) {
            if (!$arquivo || !$arquivo->isValid()) {
                continue;
            }

            $caminho = $arquivo->store('sigeconcursos/processos/' . $processo->id_processo, 'public');

            $processo->arquivos()->create([
                'nome_exibicao' => $request->input("nome_exibicao.$index") ?: $arquivo->getClientOriginalName(),
                'tipo_arquivo' => $request->input("tipo_arquivo.$index") ?: 'outro',
                'caminho_arquivo' => $caminho,
                'ordem_exibicao' => $index + 1,
            ]);
        }
    }

    private function normalizeMoney($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (str_contains($value, ',')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace(',', '', $value);
        }

        return is_numeric($value) ? $value : null;
    }

    private function nullableInteger($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function resolvePerPage(?string $perPageParam, int $total): int
    {
        $allowed = ['25', '50', '100', '200', 'all'];

        if (!in_array((string) ($perPageParam ?? ''), $allowed, true)) {
            return 25;
        }

        if ($perPageParam === 'all') {
            return max(1, $total);
        }

        return (int) $perPageParam;
    }
}