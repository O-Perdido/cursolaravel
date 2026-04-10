<?php

namespace App\Http\Controllers;

use App\Models\Vaga;
use App\Models\VagaCandidatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VagaPublicaController extends Controller
{
    public function index(Request $request)
    {
        $query = Vaga::query()
            ->with(['empresa.cidade.estado'])
            ->where('divulgada_publicamente', true)
            ->where('status', 'disponivel')
            ->whereNull('fk_id_termo')
            ->whereDate('data_termino', '>=', now());

        if ($request->filled('busca')) {
            $busca = trim((string) $request->input('busca'));

            $query->where(function ($subQuery) use ($busca) {
                $subQuery->where('titulo_vaga', 'like', '%' . $busca . '%')
                    ->orWhere('numero_vaga', 'like', '%' . $busca . '%')
                    ->orWhere('atividades', 'like', '%' . $busca . '%')
                    ->orWhere('horario', 'like', '%' . $busca . '%')
                    ->orWhereHas('empresa', function ($empresaQuery) use ($busca) {
                        $empresaQuery->where('nome_empresa', 'like', '%' . $busca . '%')
                            ->orWhere('bairro', 'like', '%' . $busca . '%')
                            ->orWhereHas('cidade', function ($cidadeQuery) use ($busca) {
                                $cidadeQuery->where('nm_cidade', 'like', '%' . $busca . '%')
                                    ->orWhereHas('estado', function ($estadoQuery) use ($busca) {
                                        $estadoQuery->where('uf_estado', 'like', '%' . $busca . '%')
                                            ->orWhere('nm_estado', 'like', '%' . $busca . '%');
                                    });
                            });
                    });
            });
        }

        $vagas = $query->orderByDesc('publicada_em')->orderByDesc('created_at')->paginate(12);

        return view('vagas.publicas.index', compact('vagas'));
    }

    public function show($id)
    {
        $vaga = Vaga::with(['empresa.cidade.estado', 'supervisor'])->findOrFail($id);

        abort_unless(
            $vaga->divulgada_publicamente && $vaga->status === 'disponivel' && !$vaga->fk_id_termo,
            404
        );

        $jaCandidatado = false;

        if (Auth::check() && Auth::user()->nivel === 'estagiario' && Auth::user()->fk_id_estagiario) {
            $jaCandidatado = VagaCandidatura::where('fk_id_vaga', $vaga->id_vaga)
                ->where('fk_id_estagiario', Auth::user()->fk_id_estagiario)
                ->exists();
        }

        return view('vagas.publicas.show', compact('vaga', 'jaCandidatado'));
    }
}