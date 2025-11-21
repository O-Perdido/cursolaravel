<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Termo;
use App\Models\Rescisao;
use Barryvdh\DomPDF\Facade\Pdf;

class RescisaoController extends Controller
{
    public function store(Request $request, $id_termo)
    {
        $validatedData = $request->validate([
            'fk_id_termo' => 'required',
            'data_rescisao' => 'required|date',
            'motivo' => 'required'
        ]);

        //Atualiza a data_fim_estagio do termo com a mesma data da rescisão
        Termo::where('id_termo', $id_termo)
            ->update(['data_fim_estagio' => $validatedData['data_rescisao']]);

        Rescisao::create($validatedData);
        return redirect('/termos/' . $id_termo . '/show')->with('success', 'Rescisão criada com sucesso!');
    }

    public function gerarPdf($id)
    {

        $rescisao = Rescisao::findOrFail($id);
        $linklogo = public_path('images/logo_pdf_padrao.png');



        //return view('termos.gerarPdf', compact('termo'));
        $pdf = Pdf::loadView('termos.gerarPdfRescisao', ['rescisao' => $rescisao, 'linklogo' => $linklogo])
            ->setPaper([0, 0, 595.28, 841.89], 'portrait');


        return $pdf->stream('TRE ' . $rescisao->termo->id_termo . '-' . \Carbon\Carbon::parse($rescisao->termo->data)->format('Y') . '-' . $rescisao->termo->estagiario->nome_estagiario . '.pdf');
        //return $pdf->download('TCE'.'.pdf');

    }

}