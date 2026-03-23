<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationCode;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\SigeConcursoCandidato;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SigeConcursoCandidatoPortalController extends Controller
{
    public function showCadastro()
    {
        if (Auth::check()) {
            return redirect()->route($this->routeForNivel((string) (Auth::user()->nivel ?? '')));
        }

        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = $this->resolveCidadesFromOldInput();
        $orgaosExpedidores = $this->orgaoExpedidorOptions();
        $ufs = $this->ufOptions();

        return view('sigeconcursos.candidato.cadastro', compact('estados', 'cidades', 'orgaosExpedidores', 'ufs'));
    }

    public function storeCadastro(Request $request)
    {
        $data = $this->validateCandidato($request, true);

        [$candidato, $user] = DB::transaction(function () use ($data) {
            $candidato = SigeConcursoCandidato::create($data['candidato']);

            $user = new User();
            $strongPassword = $user->validatePassword($data['password']);
            $user->name = $candidato->nome_completo;
            $user->email = $candidato->email;
            $user->nivel = 'candidato';
            $user->fk_id_candidato = $candidato->id_candidato;
            $user->password = Hash::make($strongPassword);
            $user->senha = Crypt::encryptString($strongPassword);
            $user->save();

            return [$candidato, $user];
        });

        $code = $user->startEmailVerification();

        try {
            Mail::to($user->email)->send(new EmailVerificationCode($code, $user->name ?? ''));
        } catch (\Throwable $exception) {
            // O cadastro não deve falhar se houver indisponibilidade momentânea no envio do e-mail.
        }

        return redirect()->route('verification.show', ['user' => $user->id])
            ->with('status', 'Cadastro realizado com sucesso. Enviamos um código para validar seu e-mail.');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route($this->routeForNivel((string) (Auth::user()->nivel ?? '')));
        }

        return view('sigeconcursos.candidato.login');
    }

    public function buscarPorCpf(Request $request)
    {
        $request->validate([
            'cpf' => ['required', 'string'],
        ]);

        $cpf = preg_replace('/\D/', '', $request->cpf);

        if (!$this->validarCpf($cpf)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'CPF informado é inválido.',
            ], 422);
        }

        $candidato = SigeConcursoCandidato::with('user')
            ->where('numero_cpf', $cpf)
            ->first();

        if (!$candidato) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Nenhum candidato foi encontrado para este CPF.',
                'cadastro_url' => route('sigeconcursos.candidato.cadastro'),
            ]);
        }

        if (!$candidato->user) {
            return response()->json([
                'status' => 'without_user',
                'message' => 'O cadastro foi encontrado, mas não possui usuário vinculado.',
            ], 409);
        }

        return response()->json([
            'status' => 'login_ready',
            'message' => 'Cadastro localizado. Informe seu e-mail e senha para continuar.',
            'email' => $candidato->user->email,
            'nome' => $candidato->nome_completo,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'cpf' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $cpf = preg_replace('/\D/', '', $request->cpf);

        $candidato = SigeConcursoCandidato::with('user')
            ->where('numero_cpf', $cpf)
            ->first();

        if (!$candidato || !$candidato->user) {
            return back()->withInput($request->except('password'))
                ->with('error', 'Não foi possível localizar um acesso de candidato para o CPF informado.');
        }

        if (strcasecmp($candidato->user->email, $request->email) !== 0) {
            return back()->withInput($request->except('password'))
                ->with('error', 'O e-mail informado não corresponde ao cadastro localizado por CPF.');
        }

        // Mantido em e-mail + senha por decisão do projeto. Caso necessário no futuro,
        // este fluxo pode ser adaptado para autenticação direta por CPF + senha.
        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            return back()->withInput($request->except('password'))
                ->with('error', 'Login inválido.');
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        if (($user->nivel ?? null) !== 'candidato' || (int) $user->fk_id_candidato !== (int) $candidato->id_candidato) {
            Auth::logout();

            return back()->withInput($request->except('password'))
                ->with('error', 'O acesso informado não pertence a um candidato válido.');
        }

        if (empty($user->email_verified_at)) {
            $expiresAt = $user->email_verification_expires_at ?? null;
            $missingOrExpired = empty($user->email_verification_token) || ($expiresAt && now()->greaterThan($expiresAt));

            if ($missingOrExpired) {
                try {
                    $code = $user->startEmailVerification();
                    Mail::to($user->email)->send(new EmailVerificationCode($code, $user->name ?? ''));
                } catch (\Throwable $exception) {
                    // Não impedir o login por indisponibilidade do envio.
                }
            }

            return redirect()->route('verification.show', ['user' => $user->id]);
        }

        return redirect()->route('sigeconcursos.candidato.dashboard');
    }

    public function dashboard()
    {
        $candidato = $this->getCandidatoAutenticado();

        return view('sigeconcursos.candidato.dashboard', compact('candidato'));
    }

    public function perfil()
    {
        $candidato = $this->getCandidatoAutenticado();

        return view('sigeconcursos.candidato.perfil', compact('candidato'));
    }

    public function editarPerfil()
    {
        $candidato = $this->getCandidatoAutenticado();
        $estados = Estado::orderBy('nm_estado')->get();
        $cidades = $this->resolveCidadesFromOldInput($candidato?->cidade?->fk_id_estado);
        $orgaosExpedidores = $this->orgaoExpedidorOptions();
        $ufs = $this->ufOptions();

        return view('sigeconcursos.candidato.editar', compact('candidato', 'estados', 'cidades', 'orgaosExpedidores', 'ufs'));
    }

    public function atualizarPerfil(Request $request)
    {
        $candidato = $this->getCandidatoAutenticado();
        $data = $this->validateCandidato($request, false, $candidato);

        DB::transaction(function () use ($candidato, $data) {
            $candidato->update($data['candidato']);

            if ($candidato->user && $candidato->user->email !== $data['candidato']['email']) {
                $candidato->user->update([
                    'email' => $data['candidato']['email'],
                    'name' => $data['candidato']['nome_completo'],
                ]);
            } elseif ($candidato->user && $candidato->user->name !== $data['candidato']['nome_completo']) {
                $candidato->user->update([
                    'name' => $data['candidato']['nome_completo'],
                ]);
            }
        });

        return redirect()->route('sigeconcursos.candidato.perfil')
            ->with('success', 'Dados atualizados com sucesso!');
    }

    private function getCandidatoAutenticado(): SigeConcursoCandidato
    {
        $user = Auth::user();

        return SigeConcursoCandidato::with(['cidade.estado', 'user'])
            ->findOrFail($user->fk_id_candidato);
    }

    private function validateCandidato(Request $request, bool $creating = true, ?SigeConcursoCandidato $candidato = null): array
    {
        $request->merge([
            'numero_cpf' => $this->onlyDigits($request->input('numero_cpf')),
            'numero_cep' => $this->onlyDigits($request->input('numero_cep')),
            'numero_telefone' => $this->onlyDigits($request->input('numero_telefone')),
            'numero_celular' => $this->onlyDigits($request->input('numero_celular')),
            'uf_rg' => strtoupper((string) $request->input('uf_rg')),
            'nome_completo' => trim((string) $request->input('nome_completo')),
            'nome_mae' => trim((string) $request->input('nome_mae')),
            'nacionalidade' => trim((string) $request->input('nacionalidade')),
            'naturalidade_cidade' => trim((string) $request->input('naturalidade_cidade')),
            'naturalidade_estado' => trim((string) $request->input('naturalidade_estado')),
            'orgao_expedidor_rg' => trim((string) $request->input('orgao_expedidor_rg')),
        ]);

        $candidateId = $candidato?->id_candidato;
        $userId = $candidato?->user?->id;

        $rules = [
            'nome_completo' => ['required', 'string', 'max:255'],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['required', Rule::in(['Masculino', 'Feminino', 'Não declarar'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('sigeconcursos_tb_candidatos', 'email')->ignore($candidateId, 'id_candidato')],
            'numero_rg' => ['required', 'string', 'max:30'],
            'orgao_expedidor_rg' => ['required', Rule::in(array_keys($this->orgaoExpedidorOptions()))],
            'uf_rg' => ['required', Rule::in(array_keys($this->ufOptions()))],
            'nome_mae' => ['required', 'string', 'max:255'],
            'nacionalidade' => ['required', 'string', 'max:100'],
            'naturalidade_cidade' => ['required', 'string', 'max:150'],
            'naturalidade_estado' => ['required', 'string', 'max:150'],
            'canhoto' => ['required', Rule::in(['sim', 'nao'])],
            'numero_cep' => ['required', 'digits:8'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero_endereco' => ['required', 'string', 'max:20'],
            'complemento_endereco' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'fk_id_cidade' => ['required', 'exists:tb_cidade,id_cidade'],
            'numero_telefone' => ['nullable', 'digits_between:10,11'],
            'numero_celular' => ['required', 'digits_between:10,11'],
        ];

        if ($creating) {
            $rules['numero_cpf'] = ['required', 'digits:11', Rule::unique('sigeconcursos_tb_candidatos', 'numero_cpf')];
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules, [
            'numero_cpf.unique' => 'Já existe um candidato cadastrado com este CPF.',
            'email.unique' => 'Já existe um candidato cadastrado com este e-mail.',
        ]);

        if ($creating && !$this->validarCpf($validated['numero_cpf'])) {
            throw ValidationException::withMessages([
                'numero_cpf' => 'O CPF informado é inválido.',
            ]);
        }

        $this->ensureEmailAvailableForCandidate($validated['email'], $userId);

        $dadosCandidato = [
            'nome_completo' => mb_strtoupper($validated['nome_completo']),
            'data_nascimento' => $validated['data_nascimento'],
            'sexo' => $validated['sexo'],
            'email' => mb_strtolower($validated['email']),
            'numero_rg' => $validated['numero_rg'],
            'orgao_expedidor_rg' => mb_strtoupper($validated['orgao_expedidor_rg']),
            'uf_rg' => mb_strtoupper($validated['uf_rg']),
            'nome_mae' => mb_strtoupper($validated['nome_mae']),
            'nacionalidade' => mb_strtoupper($validated['nacionalidade']),
            'naturalidade_cidade' => mb_strtoupper($validated['naturalidade_cidade']),
            'naturalidade_estado' => mb_strtoupper($validated['naturalidade_estado']),
            'canhoto' => $validated['canhoto'],
            'numero_cep' => $validated['numero_cep'],
            'endereco' => mb_strtoupper($validated['endereco']),
            'numero_endereco' => $validated['numero_endereco'],
            'complemento_endereco' => $validated['complemento_endereco'] ? mb_strtoupper($validated['complemento_endereco']) : null,
            'bairro' => mb_strtoupper($validated['bairro']),
            'fk_id_cidade' => $validated['fk_id_cidade'],
            'numero_telefone' => $validated['numero_telefone'] ?? null,
            'numero_celular' => $validated['numero_celular'],
        ];

        if ($creating) {
            $dadosCandidato['numero_cpf'] = $validated['numero_cpf'];
        }

        return [
            'candidato' => $dadosCandidato,
            'password' => $validated['password'] ?? null,
        ];
    }

    private function onlyDigits(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value);

        return $digits === '' ? null : $digits;
    }

    private function validarCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($digit = 9; $digit < 11; $digit++) {
            $sum = 0;

            for ($index = 0; $index < $digit; $index++) {
                $sum += (int) $cpf[$index] * (($digit + 1) - $index);
            }

            $check = ((10 * $sum) % 11) % 10;

            if ((int) $cpf[$digit] !== $check) {
                return false;
            }
        }

        return true;
    }

    private function routeForNivel(string $nivel): string
    {
        return match ($nivel) {
            'admin', 'operador' => 'welcome.admin',
            'empresa' => 'welcome.empresa',
            'estagiario' => 'welcome.estagiario',
            'candidato' => 'sigeconcursos.candidato.dashboard',
            default => 'login',
        };
    }

    private function resolveCidadesFromOldInput(?int $fallbackEstadoId = null)
    {
        $estadoId = session()->getOldInput('fk_id_estado', $fallbackEstadoId);

        if (!$estadoId) {
            return collect();
        }

        return Cidade::where('fk_id_estado', $estadoId)
            ->orderBy('nm_cidade')
            ->get();
    }

    private function ensureEmailAvailableForCandidate(string $email, ?int $currentUserId = null): void
    {
        $normalizedEmail = mb_strtolower($email);

        $existingUser = User::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();

        if (!$existingUser || ($currentUserId && (int) $existingUser->id === (int) $currentUserId)) {
            return;
        }

        if (($existingUser->nivel ?? null) !== 'candidato') {
            session()->flash('candidate_email_platform_conflict', [
                'message' => 'Este e-mail já está cadastrado na plataforma de estágios do SIGE.',
                'url' => 'https://api.whatsapp.com/send?phone=5548991468761&text=Ol%C3%A1%21%20Preciso%20de%20ajuda%20com%20o%20cadastro%20de%20candidato%20no%20SIGE%20Concursos%20porque%20meu%20e-mail%20j%C3%A1%20est%C3%A1%20em%20uso%20na%20plataforma.',
            ]);

            throw ValidationException::withMessages([
                'email' => 'Este e-mail já está cadastrado na plataforma de estágios do SIGE. Informe um e-mail diferente. Caso precise manter este mesmo endereço, entre em contato com o suporte.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => 'Já existe um acesso de candidato cadastrado com este e-mail.',
        ]);
    }

    private function orgaoExpedidorOptions(): array
    {
        return [
            'SSP' => 'Secretaria de Segurança Pública.',
            'SESP' => 'Secretaria de Estado de Segurança Pública.',
            'PC' => 'Polícia Civil.',
            'IITB' => 'Instituto de Identificação Tavares Buril (Pernambuco).',
            'IIPR' => 'Instituto de Identificação do Paraná.',
            'IIRGD' => 'Instituto de Identificação Ricardo G. D. de A. (São Paulo).',
            'IFP' => 'Instituto Félix Pacheco (Rio de Janeiro).',
            'IC' => 'Instituto de Criminalística.',
            'DETRAN' => 'Departamento de Trânsito',
            'MD/PC' => 'Ministério da Defesa - Polícia Civil.',
            'PM/PC' => 'Polícia Militar - Polícia Civil.',
        ];
    }

    private function ufOptions(): array
    {
        return [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins',
        ];
    }
}