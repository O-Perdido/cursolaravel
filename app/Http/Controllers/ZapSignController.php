<?php

namespace App\Http\Controllers;

use App\Services\ZapSignService;
use Illuminate\Http\Request;

class ZapSignController extends Controller
{
    protected $zapSignService;

    public function __construct(ZapSignService $zapSignService)
    {
        $this->zapSignService = $zapSignService;
    }

    public function createDocument(Request $request)
{
    $pdfUrl = 'URL_DO_SEU_PDF'; // Substitua pela URL do seu PDF gerado

    $data = [
        "name" => "Contrato de Prestação de Serviços", 
        "url_pdf" => $pdfUrl,
        "signers" => [
            [
                "name" => "Nome do Primeiro Signatário",
                "email" => "email1@example.com",
                "order_group" => 1, // Ordem de assinatura
                "send_automatic_email" => true,
            ],
            [
                "name" => "Nome do Segundo Signatário",
                "email" => "email2@example.com",
                "order_group" => 2, // Ordem de assinatura
                "send_automatic_email" => true,
            ],
        ],
        "lang" => "pt-br",
        "description" => "Descrição do documento",
        "require_signature" => true,
        "folder_path" => "/api/",
    ];

    $document = $this->zapSignService->createDocument($data);
    return response()->json($document);
}
}