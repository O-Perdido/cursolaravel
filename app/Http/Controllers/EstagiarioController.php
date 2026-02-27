<?php

namespace App\Http\Controllers;
use App\Models\Estagiario;
use App\Models\User;
use App\Models\Estado;
use App\Models\Escola;
use App\Models\Cidade;
use App\Models\Termo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class EstagiarioController extends Controller
{

    public function validarCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf); // Remove caracteres não numéricos

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1+$/', $cpf)) {
            return false; // Verifica se o CPF tem 11 dígitos e não é uma sequência repetida
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    public function buscarEstagiarioPorCpf(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string',
        ]);

        $cpf = preg_replace('/\D/', '', $request->cpf);

        if (!$this->validarCPF($cpf)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'CPF informado é inválido.',
            ], 422);
        }

        $estagiarios = Estagiario::where('numero_cpf', $cpf)->get();

        if ($estagiarios->isEmpty()) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Nenhum cadastro encontrado para este CPF.',
            ]);
        }

        if ($estagiarios->count() > 1) {
            return response()->json([
                'status' => 'multiple',
                'message' => 'Existem múltiplos cadastros com este CPF.',
                'count' => $estagiarios->count(),
            ]);
        }

        $estagiario = $estagiarios->first();
        $user = User::where('fk_id_estagiario', $estagiario->id_estagiario)->first();

        if ($user) {
            return response()->json([
                'status' => 'has_user',
                'message' => 'Usuário já cadastrado para este estagiário.',
                'user_email' => $user->email,
            ]);
        }

        return response()->json([
            'status' => 'can_create_user',
            'message' => 'Cadastro encontrado e sem usuário vinculado.',
            'estagiario' => [
                'id' => $estagiario->id_estagiario,
                'nome' => $estagiario->nome_estagiario,
                'email' => $estagiario->email,
            ],
        ]);
    }

    public function download($id, $campo)
    {
        // Buscar o estagiário pelo ID
        $estagiario = Estagiario::find($id);

        if (!$estagiario) {
            return redirect()->back()->withErrors('Estagiário não encontrado.');
        }

        // Validar se o campo existe e contém um valor
        if (!isset($estagiario->$campo) || !$estagiario->$campo) {
            return redirect()->back()->withErrors('Arquivo não encontrado.');
        }

        // Caminho relativo do arquivo
        $filePath = $estagiario->$campo;

        // Verificar se o arquivo existe
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->withErrors('Arquivo não existe no servidor.');
        }

        // Obter o caminho físico do arquivo no disco 'public' e forçar o download
        $fullPath = Storage::disk('public')->path($filePath);
        return response()->download($fullPath);

        //uploads/fotos/FHuQ67V3sH68b9BkSERQo2Oj1RfD1Fb1ucOoR17X.jpg
    }


    public function create()
    {
        $estados = Estado::all();
        $escolas = Escola::orderBy('nome_escola', 'asc')->get();
        return view('estagiario.create', compact('estados', 'escolas'));
    }

    public function novoEstagiarioCreate()
    {
        $estados = Estado::all();
        $escolas = Escola::all();
        return view('novoEstagiario', compact('estados', 'escolas'));
    }

    public function store(Request $request)
    {
        // Sanitiza CPF antes de validar para garantir unicidade correta
        if ($request->filled('numero_cpf')) {
            $request->merge(['numero_cpf' => preg_replace('/\D/', '', $request->numero_cpf)]);
        }

        // Validar os dados recebidos

        $request->validate([
            'nome_estagiario' => 'required|string|max:255',
            'numero_cpf' => [
                'required',
                'unique:tb_estagiarios,numero_cpf',
                function ($attribute, $value, $fail) {
                    if (!$this->validarCPF($value)) {
                        $fail('O CPF informado é inválido.');
                    }
                }
            ],
            'data_nascimento' => 'required|date',
            'numero_telefone' => 'nullable|string',
            'numero_celular' => 'required|string',
            'email' => 'required|email',
            'numero_cep' => 'required|string',
            'endereco' => 'nullable|string',
            'numero_endereco' => 'required|string',
            'bairro' => 'required|string',
            'fk_id_cidade' => 'required|exists:tb_cidade,id_cidade',
            'instituicao_ensino' => 'required|string',
            'curso' => 'required|string',
            'nivel_curso' => 'required|string',
            'area_de_estagio' => 'required|string',
            'nome_mae' => 'required|string|max:255',
            'foto_documento' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'comprovante_residencia' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'comprovante_escolar' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'numero_pis' => 'nullable|string',
            'tipo_chave_pix' => 'nullable|in:CPF,EMAIL,TELEFONE,ALEATORIA',
            'chave_pix' => 'required|string',
        ], [
            'numero_cpf.unique' => 'Já existe um estagiário cadastrado com este CPF.',
        ]);

        // Processar arquivos (se existirem)

        if ($request->hasFile('foto_documento')) {
            $fotoDocumentoPath = $request->file('foto_documento')->store('uploads/fotos', 'public');
        }

        if ($request->hasFile('comprovante_residencia')) {
            $comprovanteResidenciaPath = $request->file('comprovante_residencia')->store('uploads/comprovantes', 'public');
        }

        if ($request->hasFile('comprovante_escolar')) {
            $comprovanteEscolarPath = $request->file('comprovante_escolar')->store('uploads/comprovantes', 'public');
        }

        // Criar o Estagiário no banco de dados
        Estagiario::create([
            'nome_estagiario' => mb_strtoupper($request->nome_estagiario),
            'numero_cpf' => $request->numero_cpf,
            'data_nascimento' => $request->data_nascimento,
            'numero_telefone' => $request->numero_telefone,
            'numero_celular' => $request->numero_celular,
            'email' => $request->email,
            'numero_cep' => $request->numero_cep,
            'endereco' => $request->endereco,
            'numero_endereco' => $request->numero_endereco,
            'complemento_endereco' => $request->complemento_endereco,
            'bairro' => $request->bairro,
            'fk_id_estado' => $request->fk_id_estado,
            'fk_id_cidade' => $request->fk_id_cidade,
            'instituicao_ensino' => $request->instituicao_ensino,
            'curso' => $request->curso,
            'nivel_curso' => $request->nivel_curso,
            'area_de_estagio' => $request->area_de_estagio,
            'nome_mae' => $request->nome_mae,
            'foto_documento' => $fotoDocumentoPath,
            'comprovante_residencia' => $comprovanteResidenciaPath,
            'comprovante_escolar' => $comprovanteEscolarPath,
            'tipo_chave_pix' => $request->tipo_chave_pix,
            'chave_pix' => $request->chave_pix,
        ]);

        // Retornar resposta após salvar
        return redirect()->route('estagiarios.index')->with('success', 'Estagiário cadastrado com sucesso');
    }

    public function novoEstagiarioStore(Request $request)
    {
        // Sanitiza CPF antes de validar
        if ($request->filled('numero_cpf')) {
            $request->merge(['numero_cpf' => preg_replace('/\D/', '', $request->numero_cpf)]);
        }

        // Validar os dados recebidos

        $request->validate([
            'nome_estagiario' => 'required|string|max:255',
            'numero_cpf' => [
                'required',
                'unique:tb_estagiarios,numero_cpf',
                function ($attribute, $value, $fail) {
                    if (!$this->validarCPF($value)) {
                        $fail('O CPF informado é inválido.');
                    }
                }
            ],
            'data_nascimento' => 'required|date',
            'numero_telefone' => 'nullable|string',
            'numero_celular' => 'required',
            'email' => 'required|email',
            'numero_cep' => 'required|string',
            'endereco' => 'nullable|string|max:255',
            'numero_endereco' => 'required|string|max:10',
            'bairro' => 'required|string|max:255',
            'fk_id_cidade' => 'required|exists:tb_cidade,id_cidade',
            'instituicao_ensino' => 'required|string|max:255',
            'curso' => 'required|string|max:255',
            'nivel_curso' => 'required|string|max:255',
            'area_de_estagio' => 'required|string|max:255',
            'nome_mae' => 'required|string|max:255',
            'foto_documento' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'comprovante_residencia' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'comprovante_escolar' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'numero_pis' => 'nullable|string',
            'tipo_chave_pix' => 'nullable|in:CPF,EMAIL,TELEFONE,ALEATORIA',
            'chave_pix' => 'required|string',
        ], [
            'numero_cpf.unique' => 'Já existe um estagiário cadastrado com este CPF.',
        ]);

        // Processar arquivos (se existirem)

        if ($request->hasFile('foto_documento')) {
            $fotoDocumentoPath = $request->file('foto_documento')->store('uploads/fotos', 'public');
        }

        if ($request->hasFile('comprovante_residencia')) {
            $comprovanteResidenciaPath = $request->file('comprovante_residencia')->store('uploads/comprovantes', 'public');
        }

        if ($request->hasFile('comprovante_escolar')) {
            $comprovanteEscolarPath = $request->file('comprovante_escolar')->store('uploads/comprovantes', 'public');
        }


        // Criar o Estagiário no banco de dados
        $estagiario = Estagiario::create([
            'nome_estagiario' => mb_strtoupper($request->nome_estagiario),
            'numero_cpf' => $request->numero_cpf,
            'data_nascimento' => $request->data_nascimento,
            'numero_telefone' => $request->numero_telefone,
            'numero_celular' => $request->numero_celular,
            'email' => $request->email,
            'numero_cep' => $request->numero_cep,
            'endereco' => $request->endereco,
            'numero_endereco' => $request->numero_endereco,
            'complemento_endereco' => $request->complemento_endereco,
            'bairro' => $request->bairro,
            'fk_id_estado' => $request->fk_id_estado,
            'fk_id_cidade' => $request->fk_id_cidade,
            'instituicao_ensino' => $request->instituicao_ensino,
            'curso' => $request->curso,
            'nivel_curso' => $request->nivel_curso,
            'area_de_estagio' => $request->area_de_estagio,
            'nome_mae' => $request->nome_mae,
            'foto_documento' => $fotoDocumentoPath ?? null,
            'comprovante_residencia' => $comprovanteResidenciaPath ?? null,
            'comprovante_escolar' => $comprovanteEscolarPath ?? null,
            'tipo_chave_pix' => $request->tipo_chave_pix,
            'chave_pix' => $request->chave_pix,
        ]);

        // Retornar resposta após salvar
        return redirect()->route('novo-estagiario-create')->with('success', 'Cadastro realizado com sucesso');
    }

    // NOVO: Exibir o formulário AJAX (mesmos campos), mas com submissão via fetch e passo 2 de criação de senha
    public function novoEstagiarioAjaxCreate()
    {
        $estados = Estado::all();
        $escolas = Escola::all();
        return view('estagiario.create_ajax', compact('estados', 'escolas'));
    }

    // NOVO: Store via AJAX – retorna JSON com dados do estagiário criado
    public function novoEstagiarioAjaxStore(Request $request)
    {
        // Sanitiza CPF antes de validar
        if ($request->filled('numero_cpf')) {
            $request->merge(['numero_cpf' => preg_replace('/\D/', '', $request->numero_cpf)]);
        }

        // Calcula a idade para validação condicional do PIS
        $dataNascimento = $request->input('data_nascimento');
        $idade = null;
        if ($dataNascimento) {
            $dataNasc = \Carbon\Carbon::createFromFormat('Y-m-d', $dataNascimento);
            $idade = $dataNasc->age;
        }

        $rules = [
            'nome_estagiario' => 'required|string|max:255',
            'numero_cpf' => [
                'required',
                'unique:tb_estagiarios,numero_cpf',
                function ($attribute, $value, $fail) {
                    if (!$this->validarCPF($value)) {
                        $fail('O CPF informado é inválido.');
                    }
                }
            ],
            'data_nascimento' => 'required|date',
            'numero_telefone' => 'nullable|string',
            'numero_celular' => 'required',
            // Além de email válido para o estagiário, garantir que o email do usuário seja único
            'email' => 'required|email|unique:users,email',
            'numero_cep' => 'required|string',
            'endereco' => 'nullable|string|max:255',
            'numero_endereco' => 'required|string|max:10',
            'bairro' => 'required|string|max:255',
            'fk_id_cidade' => 'required|exists:tb_cidade,id_cidade',
            'instituicao_ensino' => 'required|string|max:255',
            'curso' => 'required|string|max:255',
            'nivel_curso' => 'required|string|max:255',
            'area_de_estagio' => 'required|string|max:255',
            'nome_mae' => 'required|string|max:255',
            'foto_documento' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'comprovante_residencia' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'comprovante_escolar' => 'required|mimes:jpeg,png,jpg,pdf|max:20480',
            'numero_pis' => $idade !== null && $idade >= 17 ? 'required|string' : 'nullable|string',
            'tipo_chave_pix' => 'required|in:CPF,EMAIL,TELEFONE,ALEATORIA',
            'chave_pix' => 'required|string',
            // Senha do usuário vinculado
            'password' => 'required|min:8|confirmed',
        ];

        $request->validate($rules, [
            'numero_cpf.unique' => 'Já existe um estagiário cadastrado com este CPF.',
            'numero_pis.required' => 'O PIS é obrigatório para maiores de 17 anos.',
        ]);


        $result = DB::transaction(function () use ($request) {
            if ($request->hasFile('foto_documento')) {
                $fotoDocumentoPath = $request->file('foto_documento')->store('uploads/fotos', 'public');
            }

            if ($request->hasFile('comprovante_residencia')) {
                $comprovanteResidenciaPath = $request->file('comprovante_residencia')->store('uploads/comprovantes', 'public');
            }

            if ($request->hasFile('comprovante_escolar')) {
                $comprovanteEscolarPath = $request->file('comprovante_escolar')->store('uploads/comprovantes', 'public');
            }

            $estagiario = Estagiario::create([
                'nome_estagiario' => mb_strtoupper($request->nome_estagiario),
                'numero_cpf' => $request->numero_cpf,
                'data_nascimento' => $request->data_nascimento,
                'numero_telefone' => $request->numero_telefone,
                'numero_celular' => $request->numero_celular,
                'email' => $request->email,
                'numero_cep' => $request->numero_cep,
                'endereco' => $request->endereco,
                'numero_endereco' => $request->numero_endereco,
                'complemento_endereco' => $request->complemento_endereco,
                'bairro' => $request->bairro,
                'fk_id_estado' => $request->fk_id_estado,
                'fk_id_cidade' => $request->fk_id_cidade,
                'instituicao_ensino' => $request->instituicao_ensino,
                'curso' => $request->curso,
                'nivel_curso' => $request->nivel_curso,
                'area_de_estagio' => $request->area_de_estagio,
                'nome_mae' => $request->nome_mae,
                'foto_documento' => $fotoDocumentoPath ?? null,
                'comprovante_residencia' => $comprovanteResidenciaPath ?? null,
                'comprovante_escolar' => $comprovanteEscolarPath ?? null,
                'numero_pis' => $request->numero_pis,
                'tipo_chave_pix' => $request->tipo_chave_pix,
                'chave_pix' => $request->chave_pix,
            ]);

            // Cria o usuário vinculado
            $user = new User();
            $strongPassword = $user->validatePassword($request->password);
            $user->name = $estagiario->nome_estagiario;
            $user->email = $estagiario->email;
            $user->nivel = 'estagiario';
            $user->fk_id_estagiario = $estagiario->id_estagiario;
            $user->password = Hash::make($strongPassword);
            $user->senha = Crypt::encryptString($strongPassword);
            $user->save();

            return [$estagiario, $user];
        });

        [$estagiario, $user] = $result;

        // Inicia verificação de e-mail: gera código, envia e retorna URL de verificação
        $code = $user->startEmailVerification();
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\EmailVerificationCode($code, $user->name ?? ''));
        } catch (\Throwable $e) {
            // Não bloqueia o cadastro, mas informa que falhou o envio
        }

        return response()->json([
            'message' => 'Cadastro realizado com sucesso',
            'estagiario' => [
                'id' => $estagiario->id_estagiario,
                'nome' => $estagiario->nome_estagiario,
                'email' => $estagiario->email,
            ],
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'nivel' => $user->nivel,
            ],
            // Usa URL direta para evitar falhas em ambientes onde a rota nomeada possa não estar disponível
            'redirect' => url('/verificar-email?user=' . $user->id),
        ]);
    }

    // NOVO: cria o usuário vinculado ao estagiário e define a senha
    public function criarUsuarioEstagiario(Request $request, $id)
    {
        $estagiario = Estagiario::findOrFail($id);

        if (User::where('fk_id_estagiario', $estagiario->id_estagiario)->exists()) {
            return response()->json([
                'message' => 'Este estagiário já possui usuário cadastrado.',
                'status' => 'has_user',
            ], 409);
        }

        // Email do usuário deve ser o mesmo do estagiário
        $email = $request->input('email', $estagiario->email);

        $validated = $request->validate([
            'password' => 'required|min:8|confirmed', // requer password_confirmation
            'email' => 'required|email|unique:users,email',
        ]);

        $user = new User();
        // Regras de senha adicionais conforme método do modelo User
        $strongPassword = $user->validatePassword($validated['password']);

        $user->name = $estagiario->nome_estagiario;
        $user->email = $email;
        $user->nivel = 'estagiario';
        $user->fk_id_estagiario = $estagiario->id_estagiario;
        $user->password = Hash::make($strongPassword);
        $user->senha = Crypt::encryptString($strongPassword);
        $user->save();

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'nivel' => $user->nivel,
                'fk_id_estagiario' => $user->fk_id_estagiario,
            ],
        ]);
    }

    public function index(Request $request)
    {

        $query = Estagiario::query();

        // Filtros

        // Filtro por nome
        if ($request->filled('nome_estagiario')) {
            $query->where('nome_estagiario', 'like', '%' . $request->nome_estagiario . '%');
        }

        // Filtro por CPF
        if ($request->filled('cpf')) {
            // limpar o CPF para garantir que não haja caracteres especiais
            $request->merge(['cpf' => preg_replace('/\D/', '', $request->cpf)]);
            // Verifica se o CPF é válido
            $query->where('numero_cpf', 'like', '%' . $request->cpf . '%');
        }

        // Filtro por ordem de cadastro 
        if ($request->has('ordem_cadastro')) {
            $query->orderBy('id_estagiario', 'desc');
        } else {
            $query->orderBy('nome_estagiario', 'asc');
        }

        // Itens por página (25, 50, 100, 200, "all")
        $perPageParam = $request->input('per_page');
        $allowed = ['25', '50', '100', '200', 'all'];
        if (!in_array((string)($perPageParam ?? ''), $allowed, true)) {
            $perPageParam = '25';
        }

        if ($perPageParam === 'all') {
            // Paginar tudo em uma única página mantendo a API do paginator
            $total = (clone $query)->count();
            $perPage = max(1, (int)$total);
        } else {
            $perPage = (int)$perPageParam;
        }

        $estagiarios = $query->paginate($perPage)->appends($request->query());

        return view('estagiario.index', compact('estagiarios'));
    }

    public function show($id)
    {
        // Buscar o estagiário no banco de dados pelo id
        $estagiario = Estagiario::findOrFail($id);

        // Retornar a view de detalhes do estagiário
        return view('estagiario.show', compact('estagiario'));
    }

    public function edit($id)
    {
        // Buscar o estagiário no banco de dados pelo id com o relacionamento cidade
        $estagiario = Estagiario::with('cidade')->findOrFail($id);

        if (!$estagiario) {
            return redirect()->route('estagiarios.index')->with('error', 'Estagiário não encontrado');
        }

        $estados = Estado::all();
        $escolas = Escola::all();

        // Verifica se o estagiário tem cidade e carrega as cidades do estado correspondente
        if ($estagiario->cidade) {
            $cidades = Cidade::where('fk_id_estado', $estagiario->cidade->fk_id_estado)->get();
        } else {
            $cidades = collect(); // Retorna uma collection vazia
        }

        // Retornar a view de edição com os dados do estagiário
        return view('estagiario.edit', compact('estagiario', 'estados', 'cidades', 'escolas'));
    }

    public function destroy($id)
    {
        // Encontrar o estagiário pelo ID
        $estagiario = Estagiario::find($id);

        if ($estagiario->termo()->exists()) {
            return redirect()->route('estagiarios.index')
                ->with('error', 'Não é possível excluir este estagiário pois ele está vinculado a um termo!');
        }

        try {
            $comprovanteResidenciaPath = $estagiario->comprovante_residencia;
            $comprovanteEscolarPath = $estagiario->comprovante_escolar;
            $fotoDocumentoPath = $estagiario->foto_documento;

            Storage::disk('public')->delete($comprovanteResidenciaPath);
            Storage::disk('public')->delete($comprovanteEscolarPath);
            Storage::disk('public')->delete($fotoDocumentoPath);
            $estagiario->delete();

            return redirect()->route('estagiarios.index')
                ->with('success', 'Estagiário excluído com sucesso!');

        } catch (QueryException $e) {
            return redirect()->route('estagiarios.index')
                ->with('error', 'Erro inesperado ao tentar excluir estagiário!');
        }
    }

    public function update(Request $request, $id)
    {
        // Sanitiza CPF antes de validar
        if ($request->filled('numero_cpf')) {
            $request->merge(['numero_cpf' => preg_replace('/\D/', '', $request->numero_cpf)]);
        }
        // Encontrar o estagiário pelo ID
        $estagiario = Estagiario::find($id);

        // Verificar se o estagiário existe
        if (!$estagiario) {
            return redirect()->route('estagiarios.index')->with('error', 'Estagiário não encontrado.');
        }

        // Validar os dados de entrada

        $request->validate([
            'nome_estagiario' => 'required|string|max:255',
            'numero_cpf' => "required|string|unique:tb_estagiarios,numero_cpf,{$id},id_estagiario", // CPF deve ser único, exceto para o estagiário atual
            'email' => "required|email", // Email deve ser válido
            'numero_telefone' => 'nullable|string',
            'numero_celular' => 'nullable|string',
            'data_nascimento' => 'required|date',
            'numero_cep' => 'nullable|string',
            'tipo_logradouro' => 'nullable|string|max:255',
            'nome_logradouro' => 'nullable|string|max:255',
            'numero_endereco' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'curso' => 'nullable|string|max:255',
            'nivel_curso' => 'nullable|string|max:255',
            'area_de_estagio' => 'nullable|string|max:255',
            'nome_mae' => 'nullable|string|max:255',
            'foto_documento' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Arquivo de foto_documento
            'comprovante_residencia' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Arquivo de comprovante_residencia
            'comprovante_escolar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Arquivo de comprovante_escolar
            'numero_pis' => 'nullable|string|max:255',
            'tipo_chave_pix' => 'nullable|in:CPF,EMAIL,TELEFONE,ALEATORIA',
            'chave_pix' => 'nullable|string|max:255',
        ], [
            'numero_cpf.unique' => 'Já existe um estagiário cadastrado com este CPF.',
        ]);


        // Atualizar os campos do estagiário
        $estagiario->update($request->except(['foto_documento', 'comprovante_residencia', 'comprovante_escolar']));

        $comprovanteResidenciaPath = $estagiario->comprovante_residencia;
        $comprovanteEscolarPath = $estagiario->comprovante_escolar;
        $fotoDocumentoPath = $estagiario->foto_documento;

        // Verificar se há novos arquivos e processá-los
        if ($request->hasFile('foto_documento')) {

            // Verificar se o arquivo existe antes de tentar apagá-lo
            if ($estagiario->foto_documento) {
                Storage::disk('public')->delete($fotoDocumentoPath);
            }


            // Armazenar o novo arquivo
            $fotoDocumentoPath = $request->file('foto_documento')->store('uploads/fotos', 'public');
            $estagiario->foto_documento = $fotoDocumentoPath;
        }

        if ($request->hasFile('comprovante_residencia')) {

            // Verificar se o arquivo existe antes de tentar apagá-lo
            if ($estagiario->comprovante_residencia) {
                Storage::disk('public')->delete($comprovanteResidenciaPath);
            }

            // Armazenar o novo arquivo
            $comprovanteResidenciaPath = $request->file('comprovante_residencia')->store('uploads/comprovantes', 'public');
            $estagiario->comprovante_residencia = $comprovanteResidenciaPath;
        }

        if ($request->hasFile('comprovante_escolar')) {

            // Verificar se o arquivo existe antes de tentar apagá-lo
            if ($estagiario->comprovante_escolar) {
                Storage::disk('public')->delete($comprovanteEscolarPath);
            }

            // Armazenar o novo arquivo
            $comprovanteEscolarPath = $request->file('comprovante_escolar')->store('uploads/comprovantes', 'public');
            $estagiario->comprovante_escolar = $comprovanteEscolarPath;
        }

        // Salvar as alterações no banco de dados
        $estagiario->save();

        // Redirecionar o usuário de volta com uma mensagem de sucesso
        return redirect()->route('estagiarios.index')->with('success', 'Estagiário atualizado com sucesso!');
    }



    // MÉTODOS PARA A ÁREA DO ESTAGIÁRIO

    /**
     * Exibir perfil completo do estagiário logado
     */
    public function perfil()
    {
    $user = Auth::user();
        
        // Buscar o estagiário pela chave estrangeira do usuário
        $estagiario = Estagiario::find($user->fk_id_estagiario);
        
        if (!$estagiario) {
            return redirect()->route('welcome.estagiario')->with('error', 'Dados do estagiário não encontrados.');
        }
        
        // Recarregar do banco para garantir dados frescos (sem cache)
        $estagiario->refresh();
        
        return response(view('estagiario.perfil', compact('estagiario')))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Exibir formulário de edição de dados do estagiário logado
     */
    public function editarPerfil()
    {
    $user = Auth::user();
        $estagiario = Estagiario::find($user->fk_id_estagiario);
        
        if (!$estagiario) {
            return redirect()->route('welcome.estagiario')->with('error', 'Dados do estagiário não encontrados.');
        }
        
        $estados = Estado::all();
        
        return view('estagiario.editar', compact('estagiario', 'estados'));
    }

    /**
     * Atualizar dados do estagiário logado
     */
    public function atualizarPerfil(Request $request)
    {
    $user = Auth::user();
        $estagiario = Estagiario::find($user->fk_id_estagiario);
        
        if (!$estagiario) {
            return redirect()->route('welcome.estagiario')->with('error', 'Dados do estagiário não encontrados.');
        }
        
        // Validação (sem CPF e outros dados sensíveis)
        $request->validate([
            'nome_estagiario' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'numero_telefone' => 'nullable|string',
            'numero_celular' => 'required|string',
            'email' => 'required|email|unique:tb_estagiarios,email,' . $estagiario->id_estagiario . ',id_estagiario',
            'numero_cep' => 'required|string',
            'endereco' => 'required|string|max:255',
            'numero_endereco' => 'required|string|max:10',
            'complemento_endereco' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'fk_id_cidade' => 'required|exists:tb_cidade,id_cidade',
            'instituicao_ensino' => 'required|string|max:255',
            'curso' => 'required|string|max:255',
            'nivel_curso' => 'required|string|max:255',
            'area_de_estagio' => 'required|string|max:255',
            'nome_mae' => 'required|string|max:255',
            'numero_pis' => 'nullable|string',
            'tipo_chave_pix' => 'nullable|in:CPF,EMAIL,TELEFONE,ALEATORIA',
            'chave_pix' => 'nullable|string',
        ]);
        
        // Atualizar dados
        $estagiario->update([
            'nome_estagiario' => mb_strtoupper($request->nome_estagiario),
            'data_nascimento' => $request->data_nascimento,
            'numero_telefone' => $request->numero_telefone,
            'numero_celular' => $request->numero_celular,
            'email' => $request->email,
            'numero_cep' => $request->numero_cep,
            'endereco' => $request->endereco,
            'numero_endereco' => $request->numero_endereco,
            'complemento_endereco' => $request->complemento_endereco,
            'bairro' => $request->bairro,
            'fk_id_cidade' => $request->fk_id_cidade,
            'instituicao_ensino' => $request->instituicao_ensino,
            'curso' => $request->curso,
            'nivel_curso' => $request->nivel_curso,
            'area_de_estagio' => $request->area_de_estagio,
            'nome_mae' => $request->nome_mae,
            'numero_pis' => $request->numero_pis,
            'tipo_chave_pix' => $request->tipo_chave_pix,
            'chave_pix' => $request->chave_pix,
        ]);
        
        // Atualizar email do usuário também, se mudou
        if ($user->email !== $request->email) {
            User::where('id', $user->id)->update(['email' => $request->email]);
        }
        
        return redirect()->route('estagiario.perfil')->with('success', 'Dados atualizados com sucesso!');
    }

    /**
     * Atualizar documento específico do estagiário logado
     */
    public function atualizarDocumento(Request $request)
    {
        $user = Auth::user();
        $estagiario = Estagiario::find($user->fk_id_estagiario);
        
        if (!$estagiario) {
            return redirect()->route('welcome.estagiario')->with('error', 'Dados do estagiário não encontrados.');
        }
        
        $campo = $request->campo_documento;
        
        // Validar campo
        if (!in_array($campo, ['foto_documento', 'comprovante_residencia', 'comprovante_escolar'])) {
            return redirect()->back()->with('error', 'Campo de documento inválido.');
        }
        
        // Validar arquivo
        $request->validate([
            'novo_documento' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        if (!$request->hasFile('novo_documento')) {
            return redirect()->back()->with('error', 'Nenhum arquivo foi recebido. Tente novamente.');
        }

        $arquivo = $request->file('novo_documento');

        if (!$arquivo->isValid()) {
            Log::warning('Upload de documento inválido para estagiário', [
                'id_estagiario' => $estagiario->id_estagiario,
                'campo' => $campo,
                'upload_error_code' => $arquivo->getError(),
                'upload_error_message' => $arquivo->getErrorMessage(),
                'ip' => $request->ip(),
            ]);

            return redirect()->back()->with('error', 'Falha no upload do arquivo. Verifique sua conexão e tente novamente.');
        }
        
        // Guardar path antigo ANTES de fazer upload do novo
        $caminhoAntigo = $estagiario->$campo;

        try {
            $pasta = ($campo === 'foto_documento') ? 'uploads/fotos' : 'uploads/comprovantes';
            $novoPath = $arquivo->store($pasta, 'public');

            DB::beginTransaction();
            $updateResult = $estagiario->update([$campo => $novoPath]);

            if (!$updateResult) {
                DB::rollBack();

                if (Storage::disk('public')->exists($novoPath)) {
                    Storage::disk('public')->delete($novoPath);
                }

                return redirect()->back()->with('error', 'Erro ao atualizar documento. Tente novamente.');
            }

            DB::commit();

            if ($caminhoAntigo && $caminhoAntigo !== $novoPath && Storage::disk('public')->exists($caminhoAntigo)) {
                Storage::disk('public')->delete($caminhoAntigo);
            }
        } catch (\Throwable $exception) {
            DB::rollBack();

            if (isset($novoPath) && $novoPath && Storage::disk('public')->exists($novoPath)) {
                Storage::disk('public')->delete($novoPath);
            }

            Log::error('Falha ao atualizar documento do estagiário', [
                'id_estagiario' => $estagiario->id_estagiario,
                'campo' => $campo,
                'caminho_antigo' => $caminhoAntigo,
                'arquivo_nome' => $arquivo->getClientOriginalName(),
                'arquivo_tamanho' => $arquivo->getSize(),
                'arquivo_mime' => $arquivo->getClientMimeType(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'erro' => $exception->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Não foi possível concluir o upload agora. Tente novamente em alguns instantes.');
        }
        
        $nomeDocumento = match($campo) {
            'foto_documento' => 'Documento de Identidade',
            'comprovante_residencia' => 'Comprovante de Residência',
            'comprovante_escolar' => 'Comprovante Escolar',
        };
        
        return redirect()->route('estagiario.perfil')->with('success', $nomeDocumento . ' atualizado com sucesso!');
    }

    /**
     * Listar contratos do estagiário logado
     */
    public function contratos()
    {
        $user = Auth::user();
        $estagiario = Estagiario::find($user->fk_id_estagiario);
        
        if (!$estagiario) {
            return redirect()->route('welcome.estagiario')->with('error', 'Dados do estagiário não encontrados.');
        }
        
        // Buscar termos do estagiário com relacionamentos
        $termos = Termo::where('fk_id_estagiario', $estagiario->id_estagiario)
                      ->with(['empresa', 'supervisor', 'rescisao', 'alteracaoTermo'])
                      ->orderBy('data_inicio_estagio', 'desc')
                      ->get();
        
        return view('estagiario.contratos', compact('termos', 'estagiario'));
    }

    /**
     * Download seguro de documento do próprio estagiário
     */
    public function downloadMeuDocumento($campo)
    {
        $user = Auth::user();
        $estagiario = Estagiario::find($user->fk_id_estagiario);

        if (!$estagiario) {
            return redirect()->back()->with('error', 'Estagiário não encontrado.');
        }

        if (!in_array($campo, ['foto_documento', 'comprovante_residencia', 'comprovante_escolar'])) {
            return redirect()->back()->with('error', 'Documento inválido.');
        }

        if (!$estagiario->$campo) {
            Log::warning('Documento não disponível para download', [
                'id_estagiario' => $estagiario->id_estagiario,
                'campo' => $campo,
            ]);
            return redirect()->back()->with('error', 'Arquivo não disponível.');
        }

        $filePath = $estagiario->$campo;
        
        Log::info('Tentando fazer download do documento', [
            'id_estagiario' => $estagiario->id_estagiario,
            'campo' => $campo,
            'caminho' => $filePath,
            'existe' => Storage::disk('public')->exists($filePath),
        ]);
        
        if (!Storage::disk('public')->exists($filePath)) {
            Log::error('Arquivo de documento não encontrado no storage', [
                'id_estagiario' => $estagiario->id_estagiario,
                'campo' => $campo,
                'caminho' => $filePath,
            ]);
            return redirect()->back()->with('error', 'Arquivo não encontrado no servidor.');
        }

        $fullPath = Storage::disk('public')->path($filePath);
        
        // Headers para garantir que não há cache
        return response()->download($fullPath)
            ->setCharset('utf-8');
    }

    /**
     * Ver detalhes do termo específico do estagiário logado
     */
    public function verTermo($id)
    {
        $user = Auth::user();
        $estagiario = Estagiario::find($user->fk_id_estagiario);
        
        if (!$estagiario) {
            return redirect()->route('welcome.estagiario')->with('error', 'Dados do estagiário não encontrados.');
        }
        
        // Buscar termo e validar que pertence ao estagiário
        $termo = Termo::where('id_termo', $id)
                     ->where('fk_id_estagiario', $estagiario->id_estagiario)
                     ->with(['empresa', 'escola', 'supervisor', 'rescisao', 'concessoesRecesso'])
                     ->first();
        
        if (!$termo) {
            return redirect()->route('estagiario.contratos')->with('error', 'Contrato não encontrado ou você não tem permissão para visualizá-lo.');
        }
        
        return view('estagiario.termo_detalhes', compact('termo', 'estagiario'));
    }

    /**
     * Gerar recibo de pagamento do estagiário para mês/ano específico
     */
    public function gerarMeuRecibo(Request $request, $id_termo)
    {
        $user = Auth::user();
        $estagiario = Estagiario::find($user->fk_id_estagiario);
        
        if (!$estagiario) {
            return redirect()->back()->with('error', 'Dados do estagiário não encontrados.');
        }
        
        // Validar termo pertence ao estagiário
        $termo = Termo::where('id_termo', $id_termo)
                     ->where('fk_id_estagiario', $estagiario->id_estagiario)
                     ->first();
        
        if (!$termo) {
            return redirect()->route('estagiario.contratos')->with('error', 'Contrato não encontrado ou você não tem permissão para visualizá-lo.');
        }
        
        // Validar dados do formulário
        $request->validate([
            'mes_referencia' => 'required|integer|min:1|max:12',
            'ano_referencia' => 'required|integer|min:2020|max:' . (date('Y') + 1),
        ]);
        
        $mesReferencia = $request->mes_referencia;
        $anoReferencia = $request->ano_referencia;
        $empresa = $termo->empresa;
        $local = $termo->local;
        
        // SOLUÇÃO: Buscar a folha pelo LOCAL do termo (não só pela empresa)
        // Pois podem existir múltiplas folhas da mesma empresa/mês/ano em locais diferentes
        $folha = \App\Models\FolhaPagamento::where('mes_referencia', $mesReferencia)
                                          ->where('ano_referencia', $anoReferencia)
                                          ->where('fk_id_empresa', $empresa->id_empresa)
                                          ->where('fk_id_local', $local->id_local ?? null)
                                          ->first();
        
        // Fallback: se não encontrar com local, tenta só com empresa
        if (!$folha) {
            $folha = \App\Models\FolhaPagamento::where('mes_referencia', $mesReferencia)
                                              ->where('ano_referencia', $anoReferencia)
                                              ->where('fk_id_empresa', $empresa->id_empresa)
                                              ->first();
        }

        if (!$folha) {
            $meses = [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
            ];
            $mesNome = $meses[$mesReferencia];
            
            return redirect()->back()->with('error', "Não existe folha de pagamento gerada para {$mesNome}/{$anoReferencia}.");
        }
        
        // Buscar o conteúdo da folha que contém este termo
        Log::info('Buscando recibo', [
            'folha_encontrada_id' => $folha->id_folha_pagamento,
            'folha_local_id' => $folha->fk_id_local,
            'termo_id' => $termo->id_termo,
            'termo_local_id' => $local->id_local ?? null,
            'mes' => $mesReferencia,
            'ano' => $anoReferencia
        ]);
        
        $conteudo = \App\Models\FolhasTermos::where('fk_id_folha', $folha->id_folha_pagamento)
                                            ->where('fk_id_termo', $termo->id_termo)
                                            ->with(['termo.estagiario', 'termo.empresa', 'folhaPagamento'])
                                            ->first();
        
        // Tentativa 2: Buscar usando relacionamento do eloquent (caso a FK esteja diferente)
        if (!$conteudo) {
            $conteudo = $folha->folhasTermos()
                             ->where('fk_id_termo', $termo->id_termo)
                             ->with(['termo.estagiario', 'termo.empresa', 'folhaPagamento'])
                             ->first();
            
            if ($conteudo) {
                Log::info('Conteúdo encontrado via relacionamento eloquent');
            }
        }
        
        // Tentativa 3: Buscar DIRETAMENTE qual folha contém este termo no mês/ano correto
        // (ignora filtro de local/empresa inicial e busca onde o termo realmente está)
        if (!$conteudo) {
            $conteudoDireto = \App\Models\FolhasTermos::where('fk_id_termo', $termo->id_termo)
                                                      ->with(['folhaPagamento', 'termo.estagiario', 'termo.empresa'])
                                                      ->get();
            
            // Filtrar pela folha que corresponde ao mês/ano solicitado
            $conteudoCorreto = $conteudoDireto->first(function ($item) use ($mesReferencia, $anoReferencia) {
                return $item->folhaPagamento 
                    && $item->folhaPagamento->mes_referencia == $mesReferencia 
                    && $item->folhaPagamento->ano_referencia == $anoReferencia;
            });
            
            if ($conteudoCorreto) {
                $conteudo = $conteudoCorreto;
                $folha = $conteudoCorreto->folhaPagamento; // Atualiza a folha para a correta
                
                Log::info('Conteúdo encontrado via busca direta (folha diferente da esperada)', [
                    'folha_correta_id' => $folha->id_folha_pagamento,
                    'folha_buscada_inicialmente' => $folha->id_folha_pagamento
                ]);
            }
        }
        
        // Debug adicional: verificar se o termo existe em QUALQUER folha
        if (!$conteudo) {
            $debugConteudo = \App\Models\FolhasTermos::where('fk_id_termo', $termo->id_termo)->get();
            $todasFolhas = \App\Models\FolhasTermos::select('fk_id_folha', 'fk_id_termo')->get();
            
            Log::warning('Termo não encontrado na folha especificada', [
                'termo_id' => $termo->id_termo,
                'folha_buscada' => $folha->id_folha_pagamento,
                'folhas_onde_termo_existe' => $debugConteudo->pluck('fk_id_folha')->toArray(),
                'total_registros_tb_folhas_termos' => $todasFolhas->count()
            ]);
        }

        if (!$conteudo) {
            $meses = [
                1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
            ];
            $mesNome = $meses[$mesReferencia];
            
            return redirect()->back()->with('error', "Seu contrato não está incluído na folha de pagamento de {$mesNome}/{$anoReferencia}. Se o problema persistir, entre em contato com o suporte.");
        }
        
        // Gerar o PDF do recibo usando a mesma view da rota administrativa
        $linklogo = public_path('images/logo_com_informacoes.png');
        
        $pdf = PDF::loadView('folhas_pagamento.gerarPdfRecibo', compact('folha', 'conteudo', 'linklogo'));
        $pdf->setPaper([0, 0, 595.28, 841.89], 'portrait');
        $pdf->getDOMPdf()->set_option('isPhpEnabled', true);
        
        return $pdf->download("recibo_{$folha->mes_referencia}_de_{$folha->ano_referencia}_estagiario_{$estagiario->nome_estagiario}.pdf");
    }

}
