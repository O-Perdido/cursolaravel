<?php

namespace App\Exports;

use App\Models\Termo;
use App\Models\Escola;
use App\Models\Empresa;
use App\Models\Ebcp;
use App\Models\Rescisao;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class TermosExport implements FromView
{

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {

        $query = Termo::query();

        if ($this->request->filled('estagiario')) {
            $query->whereHas('estagiario', function ($q) {
                $q->where('nome_estagiario', 'like', '%' . $this->request->estagiario . '%');
            });
        }

        if ($this->request->filled('empresa')) {
            $query->where('fk_id_empresa', $this->request->empresa);
        }

        if ($this->request->filled('local')) {
            $query->where('fk_id_local', $this->request->local);
        }

        if ($this->request->filled('escola')) {
            $query->where('fk_id_escola', $this->request->escola);
        }

        if ($this->request->filled('data_inicial')) {
            $query->whereDate('data', '>=', $this->request->data_inicial);
        }

        if ($this->request->filled('data_final')) {
            $query->whereDate('data', '<=', $this->request->data_final);
        }

        // Filtrar os termos que tem rescisão
        if ($this->request->has('status') && $this->request->status == 'rescindidos') {
            // Pega todas as rescisões, depois filtra os termos que tem rescisão
            $rescisaoIds = Rescisao::pluck('fk_id_termo')->toArray();
            $query->whereIn('id_termo', $rescisaoIds);
        }

        // Filtrar os termos que não tem rescisão
        if ($this->request->has('status') && $this->request->status == 'ativos') {
            // Pega todos as rescisões, depois filtra os termos que não tem rescisão
            $rescisaoIds = Rescisao::pluck('fk_id_termo')->toArray();
            $query->whereNotIn('id_termo', $rescisaoIds);
        }

        // Filtrar os termos que estão vencidos
        if ($this->request->has('status') && $this->request->status == 'vencidos') {
            // Pega todos os termos que estão vencidos, ou seja, a data_fim_estagio é menor que a data atual e não tem rescisão
            $query->where('data_fim_estagio', '<', now())
                ->whereDoesntHave('rescisao');
        }

        // return view('termos.gerarRelatorioTermo', [
        //     'termos' => Termo::all(),

        // ]);

        $linklogo = public_path('images/logo_com_informacoes.png');
        return view('termos.gerarRelatorioTermo', [
            'termos' => $query->get(),
            'linklogo' => $linklogo,
        ]);



    }
}
