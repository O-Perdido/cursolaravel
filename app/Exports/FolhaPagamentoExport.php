<?php

namespace App\Exports;

use App\Models\FolhaPagamento;
use App\Models\FolhasTermos;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class FolhaPagamentoExport implements FromView
{
    protected $id_folha_pagamento;

    public function __construct($id_folha_pagamento)
    {
        $this->id_folha_pagamento = $id_folha_pagamento;
    }


    public function view(): View
    {

        $folha = FolhaPagamento::findOrFail($this->id_folha_pagamento);
        $conteudoFolha = FolhasTermos::where('fk_id_folha', $folha->id_folha_pagamento)->get();
        $linklogo = public_path('images/logo_com_informacoes.png');

        return view('folhas_pagamento.gerarExcelFolha', [
            'folha' => $folha,
            'conteudoFolha' => $conteudoFolha,
            'linklogo' => $linklogo,
        ]);




    }
}

