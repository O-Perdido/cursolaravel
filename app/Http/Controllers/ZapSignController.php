<?php

namespace App\Http\Controllers;

use App\Services\ZapSignService;
use Illuminate\Http\Request;

/**
 * Controller de exemplo para ZapSign
 * 
 * Os métodos reais de envio estão implementados em:
 * - TermoController::enviarParaZapSign()
 * - RescisaoController::enviarParaZapSign()
 * - AlteracaoTermoController::enviarParaZapSign()
 */
class ZapSignController extends Controller
{
    protected $zapSignService;

    public function __construct(ZapSignService $zapSignService)
    {
        $this->zapSignService = $zapSignService;
    }

    // Controller mantido para compatibilidade
    // Métodos ativos estão nos controllers específicos de cada documento
}