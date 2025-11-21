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
