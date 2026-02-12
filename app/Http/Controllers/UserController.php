<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Estagiario;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query();

        // Filtros
        if ($nome = request('filtro_nome')) {
            $query->where('name', 'like', "%$nome%");
        }
        if ($email = request('filtro_email')) {
            $query->where('email', 'like', "%$email%");
        }
        if ($nivel = request('filtro_nivel')) {
            $query->where('nivel', $nivel);
        }

        // Ordenação por mais recentes
        if (request('ordem_cadastro')) {
            $query->orderByDesc('created_at');
        } else {
            $query->orderBy('name');
        }

        $perPage = request('per_page', 25);
        if ($perPage === 'all') {
            $usuarios = $query->get();
        } else {
            $usuarios = $query->paginate((int) $perPage)->withQueryString();
        }
        foreach ($usuarios as $user) {
            try {
                $user->senha = Crypt::decryptString($user->senha);
            } catch (\Exception $e) {
                $user->senha = 'Erro ao descriptografar';
            }
        }
        return view('usuarios.index', compact('usuarios'));
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'nivel' => 'required|in:estagiario,empresa',
            'termo' => 'required|string|min:2|max:200',
        ]);

        $nivel = $validated['nivel'];
        $termo = trim($validated['termo']);
        $termoNumeros = preg_replace('/\D/', '', $termo);

        if ($nivel === 'estagiario') {
            $query = User::query()
                ->where('users.nivel', 'estagiario')
                ->leftJoin('tb_estagiarios as estagiarios', 'users.fk_id_estagiario', '=', 'estagiarios.id_estagiario')
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.nivel',
                    'estagiarios.nome_estagiario',
                    'estagiarios.numero_cpf',
                    'estagiarios.email as email_estagiario',
                ]);

            $query->where(function ($subQuery) use ($termo, $termoNumeros) {
                $subQuery->where('users.email', 'like', "%{$termo}%")
                    ->orWhere('users.name', 'like', "%{$termo}%")
                    ->orWhere('estagiarios.nome_estagiario', 'like', "%{$termo}%")
                    ->orWhere('estagiarios.email', 'like', "%{$termo}%");

                if ($termoNumeros !== '') {
                    $subQuery->orWhere('estagiarios.numero_cpf', 'like', "%{$termoNumeros}%");
                }
            });
        } else {
            $query = User::query()
                ->where('users.nivel', 'empresa')
                ->leftJoin('tb_empresas as empresas', 'users.fk_id_empresa', '=', 'empresas.id_empresa')
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.nivel',
                    'empresas.nome_empresa',
                    'empresas.numero_cnpj',
                    'empresas.email as email_empresa',
                ]);

            $query->where(function ($subQuery) use ($termo, $termoNumeros) {
                $subQuery->where('users.email', 'like', "%{$termo}%")
                    ->orWhere('users.name', 'like', "%{$termo}%")
                    ->orWhere('empresas.nome_empresa', 'like', "%{$termo}%")
                    ->orWhere('empresas.email', 'like', "%{$termo}%");

                if ($termoNumeros !== '') {
                    $subQuery->orWhere('empresas.numero_cnpj', 'like', "%{$termoNumeros}%");
                }
            });
        }

        $usuarios = $query->limit(50)->get();

        $dados = $usuarios->map(function ($usuario) use ($nivel) {
            return [
                'id' => $usuario->id,
                'nome_usuario' => $usuario->name,
                'email_usuario' => $usuario->email,
                'nivel' => $usuario->nivel,
                'entidade_nome' => $nivel === 'estagiario' ? $usuario->nome_estagiario : $usuario->nome_empresa,
                'documento' => $nivel === 'estagiario' ? $usuario->numero_cpf : $usuario->numero_cnpj,
                'entidade_email' => $nivel === 'estagiario' ? $usuario->email_estagiario : $usuario->email_empresa,
            ];
        });

        return response()->json(['data' => $dados]);
    }

    public function details(string $id)
    {
        $user = User::findOrFail($id);

        $detalhes = [
            'id' => $user->id,
            'nome_usuario' => $user->name,
            'email_usuario' => $user->email,
            'nivel' => $user->nivel,
        ];

        if ($user->nivel === 'estagiario' && $user->fk_id_estagiario) {
            $estagiario = Estagiario::select([
                'id_estagiario',
                'nome_estagiario',
                'numero_cpf',
                'email',
            ])->find($user->fk_id_estagiario);

            if ($estagiario) {
                $detalhes['estagiario'] = [
                    'id' => $estagiario->id_estagiario,
                    'nome' => $estagiario->nome_estagiario,
                    'cpf' => $estagiario->numero_cpf,
                    'email' => $estagiario->email,
                ];
            }
        }

        if ($user->nivel === 'empresa' && $user->fk_id_empresa) {
            $empresa = Empresa::select([
                'id_empresa',
                'nome_empresa',
                'numero_cnpj',
                'email',
            ])->find($user->fk_id_empresa);

            if ($empresa) {
                $detalhes['empresa'] = [
                    'id' => $empresa->id_empresa,
                    'nome' => $empresa->nome_empresa,
                    'cnpj' => $empresa->numero_cnpj,
                    'email' => $empresa->email,
                ];
            }
        }

        return response()->json($detalhes);
    }

    public function updateEmail(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email|max:200|unique:users,email,' . $user->id,
        ]);

        $user->email = $validated['email'];
        $user->save();

        return response()->json(['message' => 'Email atualizado com sucesso.']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    $empresas = Empresa::all();
    // Buscar estagiários com os campos corretos do modelo
    $estagiarios = Estagiario::select(['id_estagiario as id', 'nome_estagiario as nome', 'email', 'numero_cpf as cpf'])->get();
    return view('usuarios.register', compact('empresas', 'estagiarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {

        $validated = $request->validate([
            'name' => 'required|min:3|max:200',
            'email' => 'required|min:5|max:200|email|unique:users',
            'password' => 'required|min:8|max:300',
            'nivel' => 'required',
            'fk_id_empresa' => 'nullable',
            'fk_id_estagiario' => 'nullable',
            'senha' => 'nullable',
        ]);

        $strongPassword = $user->validatePassword($validated['password']);

        try {
            $user = $user->fill($validated);
            $user->password = Hash::make($strongPassword);
            $user->senha = Crypt::encryptString($strongPassword);
            $user->save();
            return redirect('/usuarios')->with('success', 'Usuário criado com sucesso!');
        } catch (\Exception $ex) {
            // $ex->getMessage();
            return redirect('/usuarios')->with('error', 'Erro ao criar o usuário! ');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return redirect()->route('usuarios.index')->with('delete', 'Usuário não encontrado.');
        }

        if ($user->id == Auth::id()) {
            return redirect()->route('usuarios.index')->with('delete', 'Você não pode excluir a si mesmo.');
        }

        $user->delete();
        return redirect('/usuarios')->with('success', 'Usuário excluído com sucesso!');
    }
}
