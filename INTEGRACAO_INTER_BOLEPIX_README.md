# Integracao Banco Inter - BolePix (SIGE Concursos)

## Objetivo
Permitir que o candidato, na area de inscricoes, consiga:

1. Gerar cobranca de taxa de inscricao (boleto com PIX)
2. Atualizar status de pagamento
3. Baixar PDF do boleto emitido

## Fluxo implementado (fase inicial)

1. Candidato gera boleto na tela Minhas Inscricoes
2. Sistema chama Inter API Cobranca V3 (`POST /cobrancas`)
3. Sistema salva `codigoSolicitacao` e dados de cobranca na inscricao
4. Sistema sincroniza detalhes (`GET /cobrancas/{codigoSolicitacao}`)
5. Candidato pode baixar PDF (`GET /cobrancas/{codigoSolicitacao}/pdf`)

## Arquivos principais

1. Servico da integracao: `app/Services/InterBolepixService.php`
2. Controller candidato: `app/Http/Controllers/SigeConcursoCandidatoPortalController.php`
3. Rotas candidato: `routes/web.php`
4. UI na area do candidato: `resources/views/sigeconcursos/candidato/inscricoes/index.blade.php`
5. Config da integracao: `config/inter_bolepix.php`
6. Migrations de campos da cobranca: `database/migrations/2026_04_08_150000_add_inter_bolepix_fields_to_sigeconcursos_tb_inscricoes.php`

## Variaveis de ambiente

Configurar no `.env`:

```env
INTER_BOLEPIX_ENABLED=true
INTER_BOLEPIX_SANDBOX=true
INTER_BOLEPIX_BASE_URL=https://cdpj-sandbox.partners.uatinter.co
INTER_BOLEPIX_OAUTH_TOKEN_PATH=/oauth/v2/token
INTER_BOLEPIX_CHARGE_PATH=/cobranca/v3/cobrancas
INTER_BOLEPIX_CLIENT_ID=
INTER_BOLEPIX_CLIENT_SECRET=
INTER_BOLEPIX_ACCOUNT_NUMBER=
INTER_BOLEPIX_SCOPE_WRITE=boleto-cobranca.write
INTER_BOLEPIX_SCOPE_READ=boleto-cobranca.read
INTER_BOLEPIX_CERT_PATH=
INTER_BOLEPIX_KEY_PATH=
INTER_BOLEPIX_WEBHOOK_HEADER=Authorization
INTER_BOLEPIX_WEBHOOK_SECRET=
INTER_BOLEPIX_TIMEOUT=30
INTER_BOLEPIX_VERIFY_SSL=true
INTER_BOLEPIX_DEFAULT_DUE_DAYS=3
```

## Campos gravados na inscricao

Tabela `sigeconcursos_tb_inscricoes`:

1. `inter_codigo_solicitacao`
2. `inter_seu_numero`
3. `inter_nosso_numero`
4. `inter_situacao`
5. `inter_linha_digitavel`
6. `inter_codigo_barras`
7. `inter_pix_copia_cola`
8. `inter_data_vencimento`
9. `inter_ultima_sincronizacao_em`
10. `inter_payload_cobranca`

## Observacoes importantes

1. O token OAuth do Inter e reutilizado por cache por 55 minutos (a documentacao informa validade de 60 minutos).
2. A integracao usa mTLS (`cert` + `ssl_key`) via paths configurados no `.env`.
3. O header `x-conta-corrente` deve ser enviado apenas com numeros e sem zeros a esquerda.
4. O status interno de pagamento e sincronizado a partir da `situacao` retornada pelo Inter.
4. O PDF retornado pelo Inter vem em base64 e e entregue ao candidato como download.
5. O webhook publico do Inter e tratado na rota `POST /sigeconcursos/inter/webhook` e deve estar liberado de CSRF no bootstrap da aplicacao.
6. Se `INTER_BOLEPIX_WEBHOOK_SECRET` estiver preenchido, o endpoint valida o header configurado em `INTER_BOLEPIX_WEBHOOK_HEADER` (aceita valor puro ou `Bearer <segredo>`).
7. O callback pode chegar como lista de eventos e deve considerar campos de boleto/pix para atualizar linha digitavel e pix copia e cola.
8. Para evitar duplicidade de emissao, o Inter usa chave de idempotencia por 30min com base em `seuNumero`, `valorNominal`, `dataVencimento` e `cpfCnpj` do pagador.

## Proximos passos recomendados

1. Implementar webhook de cobranca Inter (`/cobrancas/webhook`) para atualizar pagamento automaticamente.
2. Criar tela administrativa de auditoria de cobrancas e falhas.
3. Adicionar tentativa de reemissao/cancelamento de cobranca quando necessario.
4. Cobrir fluxo com testes integrados para sandbox.
