<?php

use App\Http\Controllers\AlteracaoTermoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Termo;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstagiarioController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\TermoController;
use App\Http\Controllers\RescisaoController;
use App\Http\Controllers\FolhaPagamentoController;
use App\Http\Controllers\FolhasTermosController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ZapSignWebhookController;
use App\Http\Controllers\ChamadoController;
use App\Http\Controllers\TipoChamadoController;
use App\Http\Controllers\SigeConcursoCargoController;
use App\Http\Controllers\SigeConcursoEmpresaController;
use App\Http\Controllers\SigeConcursoCandidatoController;
use App\Http\Controllers\SigeConcursoCandidatoPortalController;
use App\Http\Controllers\SigeConcursoInterCobrancaController;
use App\Http\Controllers\SigeConcursoLocalProvaController;
use App\Http\Controllers\SigeConcursoProcessoController;
use App\Http\Controllers\SigeConcursoPublicoController;
use App\Http\Controllers\VagaCandidaturaController;
use App\Http\Controllers\VagaPublicaController;

$manutencao = false; // Defina como true para ativar a manutenção

if ($manutencao) {
    Route::get('/{any?}', function () {
        return view('manutencao');
    })->where('any', '.*');
    return;
}


Route::middleware(['auth'])->group(function () {

    // Dashboards específicas por nível
    Route::get('/dashboard/admin', function () {
        $termos = Termo::with('estagiario')
            ->whereDoesntHave('rescisao')
            ->get();
        return view('welcome_admin_operador', compact('termos'));
    })->middleware(['nivel:admin,operador'])->name('welcome.admin');

    Route::prefix('sigeconcursos')->name('sigeconcursos.')->middleware(['nivel:admin,operador'])->group(function () {
        Route::view('/dashboard', 'sigeconcursos.dashboard')->name('dashboard');
        Route::get('/processos', [SigeConcursoProcessoController::class, 'index'])->name('processos.index');
        Route::get('/processos/create', [SigeConcursoProcessoController::class, 'create'])->name('processos.create');
        Route::post('/processos', [SigeConcursoProcessoController::class, 'store'])->name('processos.store');
        Route::get('/processos/{id}', [SigeConcursoProcessoController::class, 'show'])->name('processos.show');
        Route::get('/processos/{id}/edit', [SigeConcursoProcessoController::class, 'edit'])->name('processos.edit');
        Route::put('/processos/{id}', [SigeConcursoProcessoController::class, 'update'])->name('processos.update');
        Route::delete('/processos/{id}', [SigeConcursoProcessoController::class, 'destroy'])->name('processos.destroy');
        Route::get('/processos/{id}/inscricoes', [SigeConcursoProcessoController::class, 'inscricoes'])->name('processos.inscricoes');
        Route::get('/processos/{id}/inscricoes/exportar', [SigeConcursoProcessoController::class, 'exportarInscricoes'])->name('processos.inscricoes.exportar');
        Route::get('/processos/{id}/isencoes', [SigeConcursoProcessoController::class, 'isencoes'])->name('processos.isencoes');
        Route::get('/processos/{id}/distribuicao-locais', [SigeConcursoProcessoController::class, 'distribuicaoLocais'])->name('processos.distribuicao-locais');
        Route::post('/processos/{id}/distribuicao-locais', [SigeConcursoProcessoController::class, 'distribuirPorLocais'])->name('processos.distribuicao-locais.distribuir');
        Route::delete('/processos/{id}/distribuicao-locais', [SigeConcursoProcessoController::class, 'limparDistribuicaoLocais'])->name('processos.distribuicao-locais.limpar');
        Route::get('/processos/{id}/distribuicao-salas', [SigeConcursoProcessoController::class, 'distribuicaoSalas'])->name('processos.distribuicao-salas');
        Route::post('/processos/{id}/distribuicao-salas', [SigeConcursoProcessoController::class, 'distribuirPorSalas'])->name('processos.distribuicao-salas.distribuir');
        Route::get('/processos/{id}/distribuicao-salas/lista-presenca-pdf', [SigeConcursoProcessoController::class, 'listaPresencaSalasPdf'])->name('processos.distribuicao-salas.lista-presenca.pdf');
        Route::delete('/processos/{id}/distribuicao-salas', [SigeConcursoProcessoController::class, 'limparDistribuicaoSalas'])->name('processos.distribuicao-salas.limpar');
        Route::post('/processos/{id}/publicar-edital', [SigeConcursoProcessoController::class, 'publicarEdital'])->name('processos.publicar-edital');
        Route::post('/processos/{id}/iniciar-inscricoes', [SigeConcursoProcessoController::class, 'iniciarInscricoes'])->name('processos.iniciar-inscricoes');
        Route::post('/processos/{id}/homologar-inscricoes', [SigeConcursoProcessoController::class, 'homologarInscricoesComArquivo'])->name('processos.homologar-inscricoes');
        Route::post('/processos/{id}/etapas-finais/arquivo', [SigeConcursoProcessoController::class, 'adicionarArquivoEtapasFinais'])->name('processos.etapas-finais.arquivo');
        Route::post('/processos/{id}/encerrar', [SigeConcursoProcessoController::class, 'encerrarProcesso'])->name('processos.encerrar');
        Route::post('/processos/{id}/publicar-local-prova', [SigeConcursoProcessoController::class, 'publicarLocalProva'])->name('processos.local-prova.publicar');
        Route::post('/processos/{id}/inscricoes/atualizar-status', [SigeConcursoProcessoController::class, 'atualizarStatusInscricao'])->name('processos.inscricoes.atualizar-status');
        Route::post('/processos/{id}/inscricoes/atualizar-status-lote', [SigeConcursoProcessoController::class, 'atualizarStatusInscricaoLote'])->name('processos.inscricoes.atualizar-status-lote');
        Route::post('/processos/{id}/inscricoes/atualizar-isencao', [SigeConcursoProcessoController::class, 'atualizarStatusIsencao'])->name('processos.inscricoes.atualizar-isencao');
        Route::post('/processos/{id}/inscricoes/atualizar-isencao-lote', [SigeConcursoProcessoController::class, 'atualizarStatusIsencaoLote'])->name('processos.inscricoes.atualizar-isencao-lote');
        Route::delete('/processos/{id}/inscricoes/{inscricao}', [SigeConcursoProcessoController::class, 'destroyInscricao'])->name('processos.inscricoes.destroy');
        Route::delete('/processos/arquivos/{id}', [SigeConcursoProcessoController::class, 'removerArquivo'])->name('processos.arquivos.destroy');
        Route::delete('/processos/documentos-exigidos/{id}', [SigeConcursoProcessoController::class, 'removerDocumentoExigido'])->name('processos.documentos-exigidos.destroy');
        Route::get('/cobrancas-inter', [SigeConcursoInterCobrancaController::class, 'index'])->name('cobrancas.index');
        Route::post('/cobrancas-inter/{id}/sincronizar', [SigeConcursoInterCobrancaController::class, 'sincronizar'])->name('cobrancas.sincronizar');
        Route::post('/cobrancas-inter/reprocessar-lote', [SigeConcursoInterCobrancaController::class, 'reprocessarLote'])->name('cobrancas.reprocessar-lote');
        Route::get('/cargos', [SigeConcursoCargoController::class, 'index'])->name('cargos.index');
        Route::get('/cargos/create', [SigeConcursoCargoController::class, 'create'])->name('cargos.create');
        Route::post('/cargos', [SigeConcursoCargoController::class, 'store'])->name('cargos.store');
        Route::get('/cargos/{id}/edit', [SigeConcursoCargoController::class, 'edit'])->name('cargos.edit');
        Route::put('/cargos/{id}', [SigeConcursoCargoController::class, 'update'])->name('cargos.update');
        Route::delete('/cargos/{id}', [SigeConcursoCargoController::class, 'destroy'])->name('cargos.destroy');
        Route::get('/locais-prova', [SigeConcursoLocalProvaController::class, 'index'])->name('locais-prova.index');
        Route::get('/locais-prova/create', [SigeConcursoLocalProvaController::class, 'create'])->name('locais-prova.create');
        Route::post('/locais-prova', [SigeConcursoLocalProvaController::class, 'store'])->name('locais-prova.store');
        Route::get('/locais-prova/{id}', [SigeConcursoLocalProvaController::class, 'show'])->name('locais-prova.show');
        Route::get('/locais-prova/{id}/edit', [SigeConcursoLocalProvaController::class, 'edit'])->name('locais-prova.edit');
        Route::put('/locais-prova/{id}', [SigeConcursoLocalProvaController::class, 'update'])->name('locais-prova.update');
        Route::delete('/locais-prova/{id}', [SigeConcursoLocalProvaController::class, 'destroy'])->name('locais-prova.destroy');
        Route::get('/orgaos', [SigeConcursoEmpresaController::class, 'index'])->name('orgaos.index');
        Route::get('/orgaos/create', [SigeConcursoEmpresaController::class, 'create'])->name('orgaos.create');
        Route::post('/orgaos', [SigeConcursoEmpresaController::class, 'store'])->name('orgaos.store');
        Route::get('/orgaos/{id}', [SigeConcursoEmpresaController::class, 'show'])->name('orgaos.show');
        Route::get('/orgaos/{id}/edit', [SigeConcursoEmpresaController::class, 'edit'])->name('orgaos.edit');
        Route::put('/orgaos/{id}', [SigeConcursoEmpresaController::class, 'update'])->name('orgaos.update');
        Route::delete('/orgaos/{id}', [SigeConcursoEmpresaController::class, 'destroy'])->name('orgaos.destroy');
        Route::get('/candidatos', [SigeConcursoCandidatoController::class, 'index'])->name('candidatos.index');
        Route::get('/candidatos/{id}', [SigeConcursoCandidatoController::class, 'show'])->name('candidatos.show');
        Route::delete('/candidatos/{id}', [SigeConcursoCandidatoController::class, 'destroy'])->name('candidatos.destroy');
    });

    Route::get('/dashboard/empresa', function () {
        return view('welcome_empresa');
    })->middleware(['nivel:empresa'])->name('welcome.empresa');

    Route::get('/dashboard/estagiario', function () {
        return view('welcome_estagiario');
    })->middleware(['nivel:estagiario','estagiario_verified'])->name('welcome.estagiario');

    Route::prefix('sigeconcursos/candidato')->name('sigeconcursos.candidato.')->middleware(['nivel:candidato', 'candidato_verified'])->group(function () {
        Route::get('/dashboard', [SigeConcursoCandidatoPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/meus-dados', [SigeConcursoCandidatoPortalController::class, 'perfil'])->name('perfil');
        Route::get('/meus-dados/editar', [SigeConcursoCandidatoPortalController::class, 'editarPerfil'])->name('perfil.editar');
        Route::put('/meus-dados', [SigeConcursoCandidatoPortalController::class, 'atualizarPerfil'])->name('perfil.atualizar');
        Route::get('/processos', [SigeConcursoCandidatoPortalController::class, 'processos'])->name('processos.index');
        Route::get('/processos/{id}', [SigeConcursoCandidatoPortalController::class, 'processoDetalhes'])->name('processos.show');
        Route::post('/processos/{id}/inscrever', [SigeConcursoCandidatoPortalController::class, 'inscreverProcesso'])->name('processos.inscrever');
        Route::get('/minhas-inscricoes', [SigeConcursoCandidatoPortalController::class, 'minhasInscricoes'])->name('minhas-inscricoes');
        Route::get('/minhas-isencoes', [SigeConcursoCandidatoPortalController::class, 'minhasIsencoes'])->name('minhas-isencoes');
        Route::get('/minhas-inscricoes/{id}/local-prova', [SigeConcursoCandidatoPortalController::class, 'meuLocalProva'])->name('local-prova');
        Route::get('/minhas-inscricoes/{id}/comprovante-pdf', [SigeConcursoCandidatoPortalController::class, 'comprovanteInscricaoPdf'])->name('comprovante-inscricao.pdf');
        Route::get('/minhas-inscricoes/{id}/local-prova-pdf', [SigeConcursoCandidatoPortalController::class, 'comprovanteLocalProvaPdf'])->name('comprovante-local-prova.pdf');
        Route::post('/minhas-inscricoes/{id}/boleto/gerar', [SigeConcursoCandidatoPortalController::class, 'gerarBoletoInscricao'])->name('boleto.gerar');
        Route::post('/minhas-inscricoes/{id}/boleto/sincronizar', [SigeConcursoCandidatoPortalController::class, 'sincronizarBoletoInscricao'])->name('boleto.sincronizar');
        Route::get('/minhas-inscricoes/{id}/boleto/pdf', [SigeConcursoCandidatoPortalController::class, 'baixarBoletoInscricaoPdf'])->name('boleto.pdf');
    });

    // Rotas para o estagiário gerenciar seu próprio perfil
    Route::middleware(['nivel:estagiario', 'estagiario_verified'])->group(function () {
        Route::get('/meu-perfil', [EstagiarioController::class, 'perfil'])->name('estagiario.perfil');
        Route::get('/meu-perfil/editar', [EstagiarioController::class, 'editarPerfil'])->name('estagiario.perfil.editar');
        Route::put('/meu-perfil/atualizar', [EstagiarioController::class, 'atualizarPerfil'])->name('estagiario.perfil.atualizar');
        Route::post('/meu-perfil/documento', [EstagiarioController::class, 'atualizarDocumento'])->name('estagiario.documento.atualizar');
        Route::get('/meu-perfil/documento/{campo}/download', [EstagiarioController::class, 'downloadMeuDocumento'])->name('estagiario.documento.download');
        Route::get('/meus-contratos', [EstagiarioController::class, 'contratos'])->name('estagiario.contratos');
        Route::post('/meus-contratos/avaliacoes/gerar-manual', [\App\Http\Controllers\AvaliacaoController::class, 'gerarManual'])->name('estagiario.avaliacoes.gerar-manual');
        Route::post('/meus-contratos/avaliacoes/{avaliacao}/link-compartilhamento', [\App\Http\Controllers\AvaliacaoController::class, 'gerarLinkCompartilhamento'])->name('estagiario.avaliacoes.gerar-link');
        Route::post('/meus-contratos/avaliacoes/{avaliacao}/regenerar-link', [\App\Http\Controllers\AvaliacaoController::class, 'regenerarLinkCompartilhamento'])->name('estagiario.avaliacoes.regenerar-link');
        Route::get('/meus-contratos/avaliacoes/{avaliacao}/pdf', [\App\Http\Controllers\AvaliacaoController::class, 'pdf'])->name('estagiario.avaliacoes.pdf');
        Route::get('/meus-contratos/{id}', [EstagiarioController::class, 'verTermo'])->name('estagiario.termo.detalhes');
        Route::get('/meus-contratos/{id}/recibo', [EstagiarioController::class, 'gerarMeuRecibo'])->name('estagiario.gerar.recibo');
        Route::post('/vagas-publicas/{id}/candidatar', [VagaCandidaturaController::class, 'candidatar'])->name('vagas.publicas.candidatar');
        Route::get('/minhas-candidaturas-vagas', [VagaCandidaturaController::class, 'minhasCandidaturas'])->name('vagas.publicas.minhas-candidaturas');
    });

    // Rotas de vagas (acessíveis por admin, operador e empresa)
    Route::middleware(['nivel:admin,operador,empresa'])->group(function () {
        Route::get('/vagas', [App\Http\Controllers\VagaController::class, 'index'])->name('vagas.index');
        Route::get('/vagas/create', [App\Http\Controllers\VagaController::class, 'create'])->name('vagas.create');
        Route::post('/vagas', [App\Http\Controllers\VagaController::class, 'store'])->name('vagas.store');
        Route::get('/vagas/{id}/edit', [App\Http\Controllers\VagaController::class, 'edit'])->name('vagas.edit');
        Route::put('/vagas/{id}', [App\Http\Controllers\VagaController::class, 'update'])->name('vagas.update');
        Route::delete('/vagas/{id}', [App\Http\Controllers\VagaController::class, 'destroy'])->name('vagas.destroy');

        // AJAX: Locais por empresa
        Route::get('/api/locais-por-empresa', [App\Http\Controllers\VagaController::class, 'getLocaisPorEmpresa'])->name('api.locais.por-empresa');
        // AJAX: Supervisores por empresa
        Route::get('/api/supervisores-por-empresa', [App\Http\Controllers\VagaController::class, 'getSupervisoresPorEmpresa'])->name('api.supervisores.por-empresa');
        // AJAX: Informações da vaga (incluindo dados do estagiário)
        Route::get('/api/vagas/{id}/info', [App\Http\Controllers\VagaController::class, 'getVagaInfo'])->name('api.vagas.info');
        Route::get('/vagas/{id}/candidaturas', [VagaCandidaturaController::class, 'indexInterno'])->name('vagas.candidaturas.index');
        Route::post('/vagas/{id}/candidaturas/status', [VagaCandidaturaController::class, 'atualizarStatus'])->name('vagas.candidaturas.status');
        Route::post('/vagas/{id}/candidaturas/{candidatura}/definir', [VagaCandidaturaController::class, 'definir'])->name('vagas.candidaturas.definir');
        Route::get('/vaga-candidaturas/{id}/curriculo', [VagaCandidaturaController::class, 'downloadCurriculo'])->name('vagas.candidaturas.curriculo');
    });

    // Rotas de processos seletivos (admin/operador/empresa - gerenciamento)
    Route::middleware(['nivel:admin,operador,empresa'])->group(function () {
        Route::get('/processos-seletivos', [App\Http\Controllers\ProcessoSeletivoController::class, 'index'])->name('processos-seletivos.index');
        Route::get('/processos-seletivos/create', [App\Http\Controllers\ProcessoSeletivoController::class, 'create'])->name('processos-seletivos.create');
        Route::post('/processos-seletivos', [App\Http\Controllers\ProcessoSeletivoController::class, 'store'])->name('processos-seletivos.store');
        Route::get('/processos-seletivos/{id}/edit', [App\Http\Controllers\ProcessoSeletivoController::class, 'edit'])->name('processos-seletivos.edit');
        Route::put('/processos-seletivos/{id}', [App\Http\Controllers\ProcessoSeletivoController::class, 'update'])->name('processos-seletivos.update');
        Route::delete('/processos-seletivos/{id}', [App\Http\Controllers\ProcessoSeletivoController::class, 'destroy'])->name('processos-seletivos.destroy');
        Route::delete('/processos-seletivos/arquivos/{id}', [App\Http\Controllers\ProcessoSeletivoController::class, 'removerArquivo'])->name('processos-seletivos.arquivos.destroy');
        Route::delete('/processos-seletivos/resultados/{id}', [App\Http\Controllers\ProcessoSeletivoController::class, 'removerResultado'])->name('processos-seletivos.resultados.destroy');
        Route::get('/processos-seletivos/{id}/inscricoes', [App\Http\Controllers\ProcessoSeletivoController::class, 'listarInscricoes'])->name('processos-seletivos.inscricoes');
        Route::post('/processos-seletivos/{id}/inscricoes/atualizar-status', [App\Http\Controllers\ProcessoSeletivoController::class, 'atualizarStatusInscricao'])->name('processos-seletivos.inscricoes.atualizar-status');
        Route::post('/processos-seletivos/{id}/inscricoes/exportar', [App\Http\Controllers\ProcessoSeletivoController::class, 'exportarInscricoes'])->name('processos-seletivos.exportar-inscricoes');
        Route::get('/processos-seletivos/{id}/resultados', [App\Http\Controllers\ProcessoSeletivoController::class, 'resultados'])->name('processos-seletivos.resultados');
        Route::post('/processos-seletivos/{id}/resultados', [App\Http\Controllers\ProcessoSeletivoController::class, 'publicarResultado'])->name('processos-seletivos.publicar-resultado');
    });

    Route::middleware(['nivel:admin,operador,empresa,estagiario'])->group(function () {
        Route::get('/processos-seletivos/arquivos/{id}/download', [App\Http\Controllers\ProcessoSeletivoPublicoController::class, 'downloadArquivo'])->name('processos-seletivos.arquivos.download');
    });

    // Rotas de processos seletivos (estagiário)
    Route::middleware(['nivel:estagiario', 'estagiario_verified'])->group(function () {
        Route::get('/processos-seletivos-abertos', [App\Http\Controllers\ProcessoSeletivoPublicoController::class, 'listarAbertos'])->name('processos-seletivos.abertos');
        Route::get('/processos-seletivos/{id}/detalhes', [App\Http\Controllers\ProcessoSeletivoPublicoController::class, 'detalhes'])->name('processos-seletivos.detalhes');
        Route::post('/processos-seletivos/{id}/inscrever', [App\Http\Controllers\ProcessoSeletivoPublicoController::class, 'inscrever'])->name('processos-seletivos.inscrever');
        Route::get('/minhas-inscricoes', [App\Http\Controllers\ProcessoSeletivoPublicoController::class, 'minhasInscricoes'])->name('processos-seletivos.minhas-inscricoes');
    });

    Route::middleware(['admin_ou_operador'])->group(function () {

        // Exibir o formulário de cadastro
        Route::get('/supervisores/create', [SupervisorController::class, 'create'])->name('supervisor.create');

        // Processar o formulário de cadastro
        Route::post('/supervisores', [SupervisorController::class, 'store'])->name('supervisor.store');

        // Pagina inicial dos supervisores
        Route::get('/supervisores/', [SupervisorController::class, 'index'])->name('supervisores.index');

        // Rota para mostrar o formulário de edição de um estagiário
        Route::get('/supervisor/{id}/edit', [SupervisorController::class, 'edit'])->name('supervisores.edit');

        // Rota para editar o estagiario
        Route::put('supervisor/{id}', [SupervisorController::class, 'update'])->name('supervisores.update');

        // Rota para deletar estagiário
        Route::delete('/supervisor/{id}', [SupervisorController::class, 'destroy'])->name('supervisores.destroy');



        //Route::resource('empresas', EmpresaController::class);

        // Separando as rotas do resource para EmpresaController
        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::get('/empresas/create', [EmpresaController::class, 'create'])->name('empresas.create');
        Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
        Route::get('/empresas/{id}', [EmpresaController::class, 'show'])->name('empresas.show');
        Route::get('/empresas/{id}/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
        Route::put('/empresas/{id}', [EmpresaController::class, 'update'])->name('empresas.update');
        Route::delete('/empresas/{id}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');




        Route::get('/estagiarios/download/{id}/{campo}', [EstagiarioController::class, 'download'])->name('estagiarios.download');



        // Exibir o formulário de cadastro
        Route::get('/estagiario/create', [EstagiarioController::class, 'create'])->name('estagiarios.create');

        // Processar o formulário de cadastro
        Route::post('/estagiario', [EstagiarioController::class, 'store'])->name('estagiario.store');

        // Pagina inicial dos estagiarios
        Route::get('/estagiario/', [EstagiarioController::class, 'index'])->name('estagiarios.index');

        // Rota para mostrar os detalhes de um estagiário
        Route::get('/estagiario/{id}', [EstagiarioController::class, 'show'])->name('estagiario.show');

        // Rota para mostrar o formulário de edição de um estagiário
        Route::get('/estagiario/{id}/edit', [EstagiarioController::class, 'edit'])->name('estagiarios.edit');

        // Rota para editar o estagiario
        Route::put('estagiario/{id}', [EstagiarioController::class, 'update'])->name('estagiario.update');

        // Rota para deletar estagiário
        Route::delete('/estagiario/{id}', [EstagiarioController::class, 'destroy'])->name('estagiario.destroy');

        //ROTAS para escolas

        // Roda para pagina inicial das escolas
        Route::get('/escolas/', [EscolaController::class, 'index'])->name('escolas.index');

        // Rota para pagina de formulario de cadastro de escolas
        Route::get('/escolas/create', [EscolaController::class, 'create'])->name('escolas.create');

        // Rota para cadastrar escolas
        Route::post('/escolas', [EscolaController::class, 'store'])->name('escolas.store');

        // Rota para mostrar os detalhes de uma escola
        Route::get('/escolas/{id}', [EscolaController::class, 'show'])->name('escolas.show');

        // Rota para excluir escola
        Route::delete('/escola/{id}', [EscolaController::class, 'destroy'])->name('escolas.destroy');

        // Rota para o formulario de edição de escola
        Route::get('/escolas/{id}/edit', [EscolaController::class, 'edit'])->name('escolas.edit');

        // Rota para editar escola
        Route::put('/escolas/{id}', [EscolaController::class, 'update'])->name('escolas.update');


        //ROTAS para termos

        // Rota para mostrar o formulário de criação
        Route::get('/termos/create/{id_estagiario?}', [TermoController::class, 'create'])->name('termos.create');

        // Rota para buscar vagas por empresa (AJAX)
        Route::get('/api/vagas-por-empresa', [TermoController::class, 'buscarVagasPorEmpresa'])->name('api.vagas.por-empresa');

        // Rota para salvar um novo termo
        Route::post('/termos', [TermoController::class, 'store'])->name('termos.store');


        // Rota para mostrar o formulário de edição
        Route::get('/termos/{id}/edit', [TermoController::class, 'edit'])
            ->middleware(['nivel:admin'])
            ->name('termos.edit');

        // Rota para atualizar um termo
        Route::put('/termos/{id}', [TermoController::class, 'update'])
            ->middleware(['nivel:admin'])
            ->name('termos.update');

        // Rota para reverter rescisao do termo
        Route::post('/termos/{id}/reverter-rescisao', [TermoController::class, 'reverterRescisao'])
            ->middleware(['nivel:admin'])
            ->name('termos.reverterRescisao');

        // Rota para excluir um termo específico
        Route::delete('/termos/{id}', [TermoController::class, 'destroy'])->name('termos.destroy');


        //ROTAS para rescisoes

        // Rota para salvar um novo termo
        Route::post('/rescisoes/{id_termo}', [RescisaoController::class, 'store'])->name('rescisoes.store');

        //Rota para gerar o PDF da rescisao
        Route::get('/rescisao/{id}', [RescisaoController::class, 'gerarPdf'])->name('rescisoes.gerarPdf');

        // Rota para enviar rescisão para ZapSign
        Route::post('/rescisao/{id}/enviar-zapsign', [RescisaoController::class, 'enviarParaZapSign'])->name('rescisoes.enviarZapSign');

        // Rota para verificar status da rescisão no ZapSign
        Route::get('/rescisao/{id}/status-zapsign', [RescisaoController::class, 'verificarStatusZapSign'])->name('rescisao.statusZapSign');
        Route::delete('/rescisao/{id}/zapsign', [RescisaoController::class, 'excluirDocumentoZapSign'])
            ->middleware(['admin_ou_operador'])
            ->name('rescisao.zapsign.excluir');


        //ROTAS para alterações de termos


        // Rota para dar o formulário de criação
        Route::get('/termos/{id}/alteracoes/create', [AlteracaoTermoController::class, 'create'])->name('alteracao.create');

        // Rota para salvar uma novo alteracao de termo
        Route::post('/termos/{id}/alteracoes', [AlteracaoTermoController::class, 'store'])->name('alteracao.store');

        // Rota para exibir os detalhes de uma alteracao de termo específico
        Route::get('/termos/{id}/alteracoes/{id_alteracao}/show', [AlteracaoTermoController::class, 'show'])->name('alteracao.show');

        // Rota para excluir uma alteracao de termo específico
        Route::delete('/termos/{id}/alteracoes/{id_alteracao}', [AlteracaoTermoController::class, 'destroy'])->name('alteracao.destroy');

        // Rota para enviar alteração para ZapSign
        Route::post('/termos/{id}/alteracoes/{id_alteracao}/enviar-zapsign', [AlteracaoTermoController::class, 'enviarParaZapSign'])->name('alteracao.enviarZapSign');

        // Rota para verificar status da alteração no ZapSign
        Route::get('/termos/{id}/alteracoes/{id_alteracao}/status-zapsign', [AlteracaoTermoController::class, 'verificarStatusZapSign'])->name('alteracao.statusZapSign');
        Route::delete('/termos/{id}/alteracoes/{id_alteracao}/zapsign', [AlteracaoTermoController::class, 'excluirDocumentoZapSign'])
            ->middleware(['admin_ou_operador'])
            ->name('alteracao.zapsign.excluir');


        // Rotas para folhas de pagamento        

        Route::post('/folhas-pagamento/{id_folha_pagamento}', [FolhaPagamentoController::class, 'storeall'])->name('folhas.storeall');
        
        // Rotas AJAX para salvar em lotes
        Route::post('/folhas-pagamento/{id_folha_pagamento}/batch', [FolhaPagamentoController::class, 'storeallBatch'])->name('folhas.storeallBatch');
        Route::post('/folhas-pagamento/{id_folha_pagamento}/finalize', [FolhaPagamentoController::class, 'finalizeFolha'])->name('folhas.finalize');

        // Rota para salvar uma nova folha de pagamento
        Route::post('/folhas-pagamento', [FolhaPagamentoController::class, 'store'])->name('folhas.store');

        // Rota para gerar arquivo de remessa CNAB240 da folha de pagamento
        Route::get('/folha-pagamento/remessa/{id_folha_pagamento}', [FolhaPagamentoController::class, 'gerarRemessa'])->name('folha_pagamento.gerarRemessa');

        // Rota para preparar remessa (dividir em lotes)
        Route::get('/folha-pagamento/preparar-remessa/{id_folha_pagamento}', [FolhaPagamentoController::class, 'prepararRemessa'])->name('folha_pagamento.prepararRemessa');

        // Rota para gerar remessa de um lote específico
        Route::post('/folha-pagamento/remessa-lote/{id_folha_pagamento}', [FolhaPagamentoController::class, 'gerarRemessaLote'])->name('folha_pagamento.gerarRemessaLote');

        // Rota para exibir o formulário de criação de uma nova folha de pagamento
        Route::get('/folhas-pagamento/create/{id_folha_pagamento}', [FolhaPagamentoController::class, 'create'])->name('folhas.create');

        // Rota para exibir o formulário de edição de uma folha de pagamento
        Route::get('/folhas-pagamento/{id_folha_pagamento}/edit', [FolhaPagamentoController::class, 'edit'])->name('folhas.edit');

        // Rota para atualizar uma folha de pagamento
        Route::put('/folhas-pagamento/{id_folha_pagamento}', [FolhaPagamentoController::class, 'update'])->name('folhas.update');

        //Rota para excluir uma folha de pagamento
        Route::delete('/folhas-pagamento/{id_folha_pagamento}', [FolhaPagamentoController::class, 'destroy'])->name('folhas.destroy');

        // Rota para gerar o excel da folha pagamento
        Route::get('/folha-pagamento/export/{id_folha_pagamento}', [FolhaPagamentoController::class, 'export'])->name('folha_pagamento.export');

        // ROTAS para locais (endpoints JSON simples por enquanto)
        Route::get('/locais', [LocalController::class, 'index'])->name('locais.index');
        Route::post('/locais', [LocalController::class, 'store'])->name('locais.store');
        Route::put('/locais/{id}', [LocalController::class, 'update'])->name('locais.update');
        Route::delete('/locais/{id}', [LocalController::class, 'destroy'])->name('locais.destroy');
    });

    // Rotas de supervisores para usuários EMPRESA (escopo próprio)
    Route::middleware(['nivel:empresa'])->group(function () {
        Route::get('/meus-supervisores', [SupervisorController::class, 'index'])->name('empresa.supervisores.index');
        Route::get('/meus-supervisores/create', [SupervisorController::class, 'create'])->name('empresa.supervisores.create');
        Route::post('/meus-supervisores', [SupervisorController::class, 'store'])->name('empresa.supervisores.store');
        Route::get('/meus-supervisores/{id}/edit', [SupervisorController::class, 'edit'])->name('empresa.supervisores.edit');
        Route::put('/meus-supervisores/{id}', [SupervisorController::class, 'update'])->name('empresa.supervisores.update');
        Route::delete('/meus-supervisores/{id}', [SupervisorController::class, 'destroy'])->name('empresa.supervisores.destroy');
    });

    // Rotas para folhas de pagamento

    // Endpoints de locais para usuário empresa (visualizar/editar apenas)
    Route::get('/meus-locais', [LocalController::class, 'meusLocais'])->name('meus-locais.index');
    Route::post('/meus-locais', [LocalController::class, 'criarMeuLocal'])->name('meus-locais.store');
    Route::put('/meus-locais/{id}', [LocalController::class, 'atualizarMeuLocal'])->name('meus-locais.update');

    // Rota para gerar o pdf da folha de pagamento
    Route::get('/folha-pagamento/{id_folha_pagamento}', [FolhaPagamentoController::class, 'gerarPdf'])->name('folha_pagamento.gerarPdf');

    // Rota para gerar o pdf do recibo de um estagiário de uma folha de pagamento especifica
    Route::get('/folha-pagamento/{id_folha_pagamento}/estagiario/{id_conteudo}', [FolhaPagamentoController::class, 'gerarRecibo'])->name('folha_pagamento.gerarRecibo');

    // Rota para listar todas as folhas de pagamento
    Route::get('/folhas-pagamento', [FolhaPagamentoController::class, 'index'])->name('folhas.index');

    // Rota para mostrar os detalhes de uma folha de pagamento específica
    Route::get('folhas-termo/{id}', [FolhaPagamentoController::class, 'show'])->name('folhas.show');



    Route::get('/termos', [TermoController::class, 'index'])->name('termos.index');

    Route::get('/termos/export/', [TermoController::class, 'export'])->name('termos.export');


    Route::get('/gerarPdfRelatorioTermo/{id_empresa?}', [TermoController::class, 'gerarPdfRelatorioTermo'])->name('termos.gerarPdfRelatorioTermo');


    Route::get('/termos/{id}/alteracoes/{id_alteracao}', [AlteracaoTermoController::class, 'gerarPdf'])->name('alteracao.gerarPdf');

    // Rota para exibir a lista de alteracoes
    Route::get('/termos/{id}/alteracoes', [AlteracaoTermoController::class, 'index'])->name('alteracoes.index');

    // Rota para exibir os detalhes de um termo específico
    Route::get('/termos/{id}/show', [TermoController::class, 'show'])->name('termos.show');

    //Rota para gerar o PDF do termo
    Route::get('/termos/{id}', [TermoController::class, 'gerarPdf'])->name('termos.gerarPdf');

    // Recesso: gerar PDF e abater saldo
    Route::post('/termos/{id}/recesso', [TermoController::class, 'gerarPdfRecesso'])->name('termos.recesso.gerar');

    // Recesso: excluir concessão (devolver saldo)
    Route::delete('/recesso/{id_concessao}', [TermoController::class, 'excluirConcessaoRecesso'])
        ->middleware(['admin_ou_operador'])
        ->name('termos.recesso.excluir');

    // Recesso: imprimir/visualizar PDF de concessão já criada
    Route::get('/recesso/{id_concessao}/pdf', [TermoController::class, 'imprimirPdfRecesso'])
        ->name('termos.recesso.pdf');

    //Rota para baixar o PDF do termo
    Route::get('/termos/{id}/download', [TermoController::class, 'downloadPdf'])->name('termos.downloadPdf');

    // Rotas para integração com ZapSign
    Route::post('/termos/{id}/enviar-zapsign', [TermoController::class, 'enviarParaZapSign'])->name('termos.enviarZapSign');
    Route::get('/termos/{id}/status-zapsign', [TermoController::class, 'verificarStatusZapSign'])->name('termos.statusZapSign');
    Route::delete('/termos/{id}/zapsign', [TermoController::class, 'excluirDocumentoZapSign'])
        ->middleware(['admin_ou_operador'])
        ->name('termos.zapsign.excluir');

    // Rotas para Representantes
    Route::post('/representantes', [App\Http\Controllers\RepresentanteController::class, 'store'])->name('representantes.store');
    Route::put('/representantes/{id}', [App\Http\Controllers\RepresentanteController::class, 'update'])->name('representantes.update');
    Route::delete('/representantes/{id}', [App\Http\Controllers\RepresentanteController::class, 'destroy'])->name('representantes.destroy');
    Route::get('/representantes/by-entity', [App\Http\Controllers\RepresentanteController::class, 'getByEntity'])->name('representantes.byEntity');






    Route::middleware(['admin_ou_operador'])->group(function () {
        // Rotas para autenticação

        // Rota para exibir a lista de usuários
        Route::get('/usuarios/', [UserController::class, 'index'])->name('usuarios.index');

        // Pesquisa rápida de usuários (modal)
        Route::get('/usuarios/pesquisa', [UserController::class, 'search'])->name('usuarios.search');
        Route::get('/usuarios/{id}/detalhes', [UserController::class, 'details'])->name('usuarios.details');
        Route::put('/usuarios/{id}/email', [UserController::class, 'updateEmail'])->name('usuarios.updateEmail');

        // Rota para exibir o formulário de cadastro de um novo usuário
        Route::get('/usuarios/cadastrar', [UserController::class, 'create'])->name('usuarios.register');

        // Rota para salvar um novo usuário
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');

        // Rota para deletar um usuário
        Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');

        // Rotas para configurações do sistema
        Route::get('/configuracoes', [App\Http\Controllers\ConfiguracaoController::class, 'index'])->name('configuracoes.index');
        Route::post('/configuracoes', [App\Http\Controllers\ConfiguracaoController::class, 'update'])->name('configuracoes.update');
        
        // Rotas para configurações individuais de empresas (unidades concedentes)
        Route::get('/configuracoes/empresas', [App\Http\Controllers\ConfiguracaoController::class, 'empresas'])->name('configuracoes.empresas');
        Route::get('/configuracoes/empresas/{id}/editar', [App\Http\Controllers\ConfiguracaoController::class, 'editarEmpresa'])->name('configuracoes.editar-empresa');
        Route::post('/configuracoes/empresas/{id}/atualizar', [App\Http\Controllers\ConfiguracaoController::class, 'atualizarEmpresa'])->name('configuracoes.atualizar-empresa');

        // Rotas para gerenciamento de tipos de chamados (admin)
        Route::get('/admin/tipos-chamados', [TipoChamadoController::class, 'index'])->name('admin.tipos-chamados.index');
        Route::get('/admin/tipos-chamados/create', [TipoChamadoController::class, 'create'])->name('admin.tipos-chamados.create');
        Route::post('/admin/tipos-chamados', [TipoChamadoController::class, 'store'])->name('admin.tipos-chamados.store');
        Route::get('/admin/tipos-chamados/{id}/edit', [TipoChamadoController::class, 'edit'])->name('admin.tipos-chamados.edit');
        Route::put('/admin/tipos-chamados/{id}', [TipoChamadoController::class, 'update'])->name('admin.tipos-chamados.update');
        Route::delete('/admin/tipos-chamados/{id}', [TipoChamadoController::class, 'destroy'])->name('admin.tipos-chamados.destroy');
    });

    // Rotas de Chamados (acessíveis por empresa, admin e operador)
    Route::middleware(['nivel:empresa,admin,operador'])->group(function () {
        Route::get('/chamados', [ChamadoController::class, 'index'])->name('chamados.index');
        Route::get('/chamados/create', [ChamadoController::class, 'create'])->name('chamados.create');
        Route::post('/chamados', [ChamadoController::class, 'store'])->name('chamados.store');
        Route::get('/chamados/{id}', [ChamadoController::class, 'show'])->name('chamados.show');
        Route::post('/chamados/{id}/mensagens', [ChamadoController::class, 'enviarMensagem'])->name('chamados.enviar-mensagem');
        
        // Empresas podem cancelar seus próprios chamados
        Route::put('/chamados/{id}/cancelar', [ChamadoController::class, 'cancelar'])->name('chamados.cancelar');
        
        // API para buscar termos (usado no Select2)
        Route::get('/api/chamados/buscar-termos', [ChamadoController::class, 'buscarTermos'])->name('api.chamados.buscar-termos');
        
        // API para listar tipos de chamados ativos
        Route::get('/api/tipos-chamados/ativos', [TipoChamadoController::class, 'tiposAtivos'])->name('api.tipos-chamados.ativos');

        // API para listagem de termos com filtros (modal)
        Route::get('/api/chamados/termos-lista', [ChamadoController::class, 'listarTermosModal'])->name('api.chamados.termos-lista');

        // Visualização e download de anexos
        Route::get('/chamados/{id}/anexo/{index}/ver', [ChamadoController::class, 'visualizarAnexo'])->name('chamados.anexo.ver');
        Route::get('/chamados/{id}/anexo/{index}/download', [ChamadoController::class, 'downloadAnexo'])->name('chamados.anexo.download');
    });

    // Rotas de Avaliações (apenas admin e operador)
    Route::middleware(['nivel:admin,operador'])->group(function () {
        Route::get('/avaliacoes', [\App\Http\Controllers\AvaliacaoController::class, 'index'])->name('avaliacoes.index');
        Route::get('/avaliacoes/{avaliacao}', [\App\Http\Controllers\AvaliacaoController::class, 'show'])->name('avaliacoes.show');
        Route::post('/avaliacoes/{avaliacao}/link-compartilhamento', [\App\Http\Controllers\AvaliacaoController::class, 'gerarLinkCompartilhamento'])->name('avaliacoes.gerar-link');
        Route::post('/avaliacoes/{avaliacao}/regenerar-link', [\App\Http\Controllers\AvaliacaoController::class, 'regenerarLinkCompartilhamento'])->name('avaliacoes.regenerar-link');
        Route::get('/avaliacoes/termo/{termo}', [\App\Http\Controllers\AvaliacaoController::class, 'porTermo'])->name('avaliacoes.por-termo');
        Route::post('/avaliacoes/gerar-manual', [\App\Http\Controllers\AvaliacaoController::class, 'gerarManual'])->name('avaliacoes.gerar-manual');
        Route::post('/avaliacoes/{avaliacao}/limpar', [\App\Http\Controllers\AvaliacaoController::class, 'limpar'])->name('avaliacoes.limpar');
        Route::delete('/avaliacoes/{avaliacao}', [\App\Http\Controllers\AvaliacaoController::class, 'destroy'])->name('avaliacoes.destroy');
        Route::get('/avaliacoes/contador/pendentes', [\App\Http\Controllers\AvaliacaoController::class, 'contadorPendentes'])->name('avaliacoes.contador');
        Route::get('/avaliacoes/{avaliacao}/pdf', [\App\Http\Controllers\AvaliacaoController::class, 'pdf'])->name('avaliacoes.pdf');
    });

    // Painel de gerenciamento de chamados (apenas admin e operador)
    Route::middleware(['nivel:admin,operador'])->group(function () {
        Route::get('/painel/chamados', [ChamadoController::class, 'painel'])->name('chamados.painel');
        Route::put('/painel/chamados/{id}/status', [ChamadoController::class, 'atualizarStatus'])->name('chamados.atualizar-status');
        Route::put('/painel/chamados/{id}/responsavel', [ChamadoController::class, 'atribuirResponsavel'])->name('chamados.atribuir-responsavel');
        Route::post('/painel/chamados/{id}/observacao', [ChamadoController::class, 'adicionarObservacao'])->name('chamados.adicionar-observacao');
        Route::delete('/chamados/{id}', [ChamadoController::class, 'destroy'])->name('chamados.destroy');
    });


    Route::get('/welcome/', function () {
        $nivel = Auth::user()->nivel ?? '';
        return redirect()->route(match ($nivel) {
            'admin', 'operador' => 'welcome.admin',
            'empresa' => 'welcome.empresa',
            'estagiario' => 'welcome.estagiario',
            'candidato' => 'sigeconcursos.candidato.dashboard',
            default => 'login',
        });
    })->name('welcome');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ========== ROTAS PÚBLICAS (sem autenticação) ==========

// Landing page - página inicial (pública ou redireciona para dashboard se autenticado)
Route::get('/', function () {
    if (Auth::check()) {
        $nivel = Auth::user()->nivel ?? '';
        return redirect()->route(match ($nivel) {
            'admin', 'operador' => 'welcome.admin',
            'empresa' => 'welcome.empresa',
            'estagiario' => 'welcome.estagiario',
            'candidato' => 'sigeconcursos.candidato.dashboard',
            default => 'login',
        });
    }
    return app(App\Http\Controllers\ProcessoSeletivoPublicoController::class)->landing();
})->name('landing');

// Rotas de processos seletivos públicas
Route::get('/processos-publicos', [App\Http\Controllers\ProcessoSeletivoPublicoController::class, 'listarPublicos'])->name('processos-seletivos.publicos');
Route::get('/processos-seletivos/{id}/detalhes-publico', [App\Http\Controllers\ProcessoSeletivoPublicoController::class, 'detalhesPublico'])->name('processos-seletivos.detalhes.publico');
Route::get('/processos-seletivos/arquivos/{id}/download-publico', [App\Http\Controllers\ProcessoSeletivoPublicoController::class, 'downloadArquivoPublico'])->name('processos-seletivos.arquivos.download-publico');
Route::get('/vagas-publicas', [VagaPublicaController::class, 'index'])->name('vagas.publicas.index');
Route::get('/vagas-publicas/{id}', [VagaPublicaController::class, 'show'])->name('vagas.publicas.show');

// Rotas publicas do SIGE Concursos
Route::prefix('sigeconcursos')->name('sigeconcursos.publico.')->group(function () {
    Route::get('/processos-publicos', [SigeConcursoPublicoController::class, 'index'])->name('processos.index');
    Route::get('/processos/{id}/detalhes-publico', [SigeConcursoPublicoController::class, 'show'])->name('processos.show');
});

Route::post('/sigeconcursos/inter/webhook', [SigeConcursoInterCobrancaController::class, 'webhook'])->name('sigeconcursos.inter.webhook');

// Rotas públicas de avaliação (sem autenticação)
Route::get('/avaliacoes/responder/{token}', [\App\Http\Controllers\AvaliacaoController::class, 'responder'])->name('avaliacoes.responder');
Route::post('/avaliacoes/salvar-respostas/{token}', [\App\Http\Controllers\AvaliacaoController::class, 'salvarRespostas'])->name('avaliacoes.salvar-respostas');
Route::get('/avaliacoes/sucesso', function () {
    return view('avaliacoes.sucesso');
})->name('avaliacoes.sucesso');




// Rotas de redefinição de senha (públicas/guest)
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetUpdate'])->name('password.update');

    Route::prefix('sigeconcursos/candidato')->name('sigeconcursos.candidato.')->group(function () {
        Route::get('/cadastro', [SigeConcursoCandidatoPortalController::class, 'showCadastro'])->name('cadastro');
        Route::post('/cadastro', [SigeConcursoCandidatoPortalController::class, 'storeCadastro'])->name('store');
        Route::get('/login', [SigeConcursoCandidatoPortalController::class, 'showLogin'])->name('login');
        Route::post('/login', [SigeConcursoCandidatoPortalController::class, 'login'])->name('auth');
        Route::post('/buscar-cpf', [SigeConcursoCandidatoPortalController::class, 'buscarPorCpf'])->name('buscar-cpf');
    });
});

Route::get('estados/{id}/cidades', [EmpresaController::class, 'getCidadesByEstado']);

// Exibir o formulário de cadastro
Route::get('/novo-estagiario', [EstagiarioController::class, 'novoEstagiarioCreate'])->name('novo-estagiario-create');

// Processar o formulário de cadastro
Route::post('/novo-estagiario', [EstagiarioController::class, 'novoEstagiarioStore'])->name('novo-estagiario-store');

// NOVO: fluxo AJAX de cadastro de estagiário e criação de usuário (sem recarregar)
Route::get('/novo-estagiario-ajax', [EstagiarioController::class, 'novoEstagiarioAjaxCreate'])->name('novo-estagiario-ajax-create');
Route::post('/novo-estagiario-ajax', [EstagiarioController::class, 'novoEstagiarioAjaxStore'])->name('novo-estagiario-ajax-store');
Route::post('/estagiarios/buscar-cpf', [EstagiarioController::class, 'buscarEstagiarioPorCpf'])->name('estagiarios.buscar-por-cpf');
Route::post('/estagiarios/{id}/criar-usuario', [EstagiarioController::class, 'criarUsuarioEstagiario'])->name('estagiarios.criar-usuario');

// Verificação de e-mail por código (público)
Route::get('/verificar-email', [EmailVerificationController::class, 'show'])->name('verification.show');
Route::post('/verificar-email', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('/reenviar-codigo', [EmailVerificationController::class, 'resend'])->name('verification.resend');

Route::get('/login', [AuthController::class, 'index'])->name('login');

// Rota para salvar um novo termo
Route::post('/login', [AuthController::class, 'loginAttempt'])->name('auth');

// Webhook ZapSign (rota pública)
Route::post('/webhooks/zapsign', [ZapSignWebhookController::class, 'handle'])->name('webhook.zapsign');

// Central de Ajuda (rota pública)
Route::get('/ajuda', [App\Http\Controllers\AjudaController::class, 'index'])->name('ajuda');
