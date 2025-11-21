# SIGE – Instruções para Agentes (Copilot)

Essenciais para trabalhar produtivamente neste Laravel 11 (PHP 8.2) rodando em Laragon. Foque em padrões do projeto – não aplique convenções Laravel default onde foram customizadas.

## Big Picture
Contratos de estágio (Model `Termo`) geram PDFs e fluxo de assinatura ZapSign. Folhas de pagamento (`FolhaPagamento`) agregam linhas por estagiário (`FolhasTermos`) e exigem salvamento em LOTES. Recesso controla saldo de dias dentro do próprio termo. PWA garante uso offline básico.

## Autorização & Níveis
Campo `users.nivel` (admin | operador | empresa | estagiario) + middlewares: sempre combine `auth` com `nivel:` ou aliases (`admin_ou_operador`, `estagiario_verified`). Não adicionar rotas protegidas sem esse padrão.

## Convenções de Banco
Tabelas prefix `tb_`; PK `id_[singular]`; FK `fk_id_[singular]`. Cada Model define explicitamente `protected $table` e `protected $primaryKey`. Ao criar novas relações, siga nomenclatura para migrações e chaves.

## Models / Campos Críticos
`Termo`: datas início/fim, `saldo_recesso`, `zapsign_doc_token`, `zapsign_status`.
`FolhaPagamento`: `mes_referencia`, `tipo_calculo_auxilio_transporte`, `tipo_calculo_recesso`, `dias_uteis`.

## Fluxo ZapSign
Gerar PDF (DomPDF) -> converter para base64 -> `ZapSignService->criarDocumentoBase64()` -> persistir token/status -> webhook atualiza. Nunca expor token; usar variáveis `.env` (verificar que `config/zapsign.php` não mantenha valor real). Preferir base64 (mais confiável que URL local).

## Salvamento em Lotes (Folha)
Limite `max_input_vars`: front envia blocos de ~50 registros para `/folhas-pagamento/{id}/batch` até cobrir todos; finalizar com `/finalize`. No backend: `storeallBatch()` agrega parcial e `finalizeFolha()` calcula totais. Qualquer feature nova que poste muitas linhas deve reutilizar esse padrão.

## Recesso
Endpoint `POST /termos/{id}/recesso`: valida saldo, cria `ConcessaoRecesso`, abate `saldo_recesso`, gera PDF. Exclusão devolve saldo. Ajuste cálculos na folha via `tipo_calculo_recesso`.

## PWA
Arquivos chave: `public/manifest.json`, `public/service-worker.js`, `public/offline.html`. Cache strategy: HTML Network-First; assets Cache-First; formulários/API Network-Only. Alterar `CACHE_NAME` em deploy para forçar refresh.

## Dev Workflow
Inicialização completa: `composer dev` (concurrently: serve + queue:listen + pail + vite). Alternativa manual: `php artisan serve`, `php artisan queue:work`, `npm run dev`. Sempre executar `php artisan storage:link` após novo ambiente.

## Testes / Qualidade
Teste com Pest: `./vendor/bin/pest` (raramente usado – ao adicionar lógica financeira ou lote, inclua ao menos 1 teste happy path + 1 edge). Pint disponível para formatação (`vendor/bin/pint`).

## Exports & Remessa
Excel via `TermosExport`, `FolhaPagamentoExport` (Maatwebsite). Arquivo bancário CNAB gerado em `FolhaPagamentoController->gerarRemessa()` – manter layout compatível; não alterar delimitadores sem atualizar documentação.

## Cálculos Chave
Bolsa: `(valor_bolsa / 30) * dias_trabalhados`. Auxílio transporte: diário (`valor_aux * dias_uteis_mes`) ou mensal (`(valor_aux / 30) * dias_trabalhados`). Taxa administrativa: fixa ou percentual sobre bolsa mensal.

## Pitfalls Rápidos
Token mismatch: conferir `@csrf` e header AJAX. ZapSign sem status: checar webhook público e logs (`ZapSignWebhookLog`). PDF falha: preferir base64 e garantir papel A4 portrait. Lote incompleto: confirmar chamadas `/batch` + `/finalize` na sequência.

## Referências
Rotas principais em `routes/web.php`. Serviço assinatura: `app/Services/ZapSignService.php`. Lógica lote: `FolhaPagamentoController`. Documentação adicional: `SOLUCAO_FOLHA_LOTES.md`, `CONFIGURACAO_ZAPSIGN.md`, `ZAPSIGN_README.md`, `CHECKLIST_ZAPSIGN.md`.

Siga sempre locale pt-BR e padrões numéricos (use `number_format(..., ',', '.')`). Pergunte se algum trecho estiver ambíguo antes de refatorar fluxos críticos.
