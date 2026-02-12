# Integração ZapSign - Sistema de Gestão de Estágios

## 📋 Sobre a Integração

Esta integração permite enviar termos de estágio para assinatura eletrônica através da plataforma ZapSign.

## 🚀 Configuração

### 1. Obter o API Token do ZapSign

1. Acesse sua conta no [ZapSign](https://app.zapsign.com.br)
2. Vá em **Configurações > Integrações > API Zapsign > Token de Acesso**
3. Copie o token gerado

### 2. Configurar o arquivo .env

Adicione as seguintes linhas no seu arquivo `.env`:

```env
ZAPSIGN_API_TOKEN=seu_token_aqui
ZAPSIGN_API_URL=https://api.zapsign.com.br/api/v1
ZAPSIGN_SANDBOX=false
ZAPSIGN_WEBHOOK_URL=
```

### 3. Executar a migração do banco de dados

Execute o comando para adicionar os campos necessários na tabela `termos`:

```bash
php artisan migrate
```

Isso adicionará os seguintes campos:
- `zapsign_doc_token` - Token do documento no ZapSign
- `zapsign_status` - Status atual do documento
- `zapsign_enviado_em` - Data/hora do envio

## 📖 Como Usar

### Enviar Termo para Assinatura

1. Acesse a listagem de termos
2. Clique no botão verde com ícone de assinatura (📝)
3. Confirme o envio no modal
4. O termo será enviado para o email do estagiário cadastrado

### Verificar Status da Assinatura

Após o envio, você pode verificar o status:
- O sistema salvará o `doc_token` retornado pelo ZapSign
- Use o método `verificarStatusZapSign($id)` para consultar o status

## 🔧 Requisitos Importantes

### URL Pública do PDF

⚠️ **IMPORTANTE**: O PDF do termo precisa estar acessível publicamente para que o ZapSign possa baixá-lo.

Opções:

**Opção 1**: Usar rota pública (atual)
```php
$pdfUrl = route('termos.downloadPdf', $id);
```

**Opção 2**: Salvar em storage público (recomendado)
```php
$pdf = Pdf::loadView('termos.pdf', compact('termo'));
$filename = 'termo_' . $termo->numero_termo . '_' . $termo->ano_termo . '.pdf';
Storage::disk('public')->put('termos/' . $filename, $pdf->output());
$pdfUrl = url(Storage::url('termos/' . $filename));
```

### Dados do Estagiário

Certifique-se que o estagiário possui:
- ✅ Nome cadastrado (obrigatório)
- ✅ Email cadastrado (recomendado)
- ✅ Telefone (opcional, mas útil para WhatsApp)

### Dados de Representantes (Empresa/Escola)

- O envio prioriza representantes cadastrados na tabela `tb_representantes`
- Se não houver representantes, usa `nome_representante` + `email` da empresa/escola
- Garanta que o email esteja preenchido para que o convite seja enviado

### Atualizacao automatica de status (Webhook)

- Se `ZAPSIGN_WEBHOOK_URL` estiver configurado, o sistema envia `webhook_url` no payload
- O endpoint publico ja existe em `/webhooks/zapsign` e atualiza o status automaticamente

### Tela de detalhes do termo (ZapSign)

- Card dedicado com duas secoes: Assinatura TCE e Assinatura TRE
- Acoes principais ficam no card (enviar, atualizar status, excluir documento)
- Lista de destinatarios com status individual e link para baixar PDF assinado quando disponivel

### Lista de alteracoes (ZapSign)

- Botao "Assinaturas" abre um modal com status do TAE
- Exibe destinatarios, status individual, envio, exclusao e download do PDF assinado

### Instituicao de ensino fora do ZapSign

- No cadastro/edicao da escola, marque a opcao "Esta instituicao de ensino nao assina pelo ZapSign"
- Quando marcada, a escola nao e adicionada como destinataria nas assinaturas (TCE, TAE e TRE)

## 🎯 Funcionalidades Implementadas

- ✅ Criar documento no ZapSign via upload de PDF
- ✅ Configurar signatários automaticamente
- ✅ Enviar email automático para assinatura
- ✅ Detalhar status do documento
- ✅ Listar documentos enviados
- ✅ Excluir documentos
- ✅ Interface com modal de confirmação
- ✅ Validação de email do estagiário

## 📚 Estrutura de Arquivos

```
app/
├── Services/
│   └── ZapSignService.php          # Service principal da integração
└── Http/Controllers/
    └── TermoController.php         # Métodos enviarParaZapSign e verificarStatusZapSign

config/
└── zapsign.php                     # Configurações da API

database/migrations/
└── 2025_11_03_000001_add_zapsign_fields_to_termos_table.php

resources/views/termos/
└── index.blade.php                 # Botão de envio para ZapSign
```

## 🔍 Métodos Disponíveis no ZapSignService

### `criarDocumento($pdfUrl, $documentName, $signatarios)`
Cria um novo documento no ZapSign.

**Parâmetros:**
- `$pdfUrl` (string): URL pública do PDF
- `$documentName` (string): Nome do documento
- `$signatarios` (array): Array de signatários

**Exemplo:**
```php
$signatarios = [
    [
        'name' => 'João Silva',
        'email' => 'joao@email.com',
        'phone_number' => '11999999999',
    ]
];

$resultado = $zapSignService->criarDocumento(
    'https://exemplo.com/termo.pdf',
    'Termo de Estágio 001/2025',
    $signatarios
);
```

### `detalharDocumento($docToken)`
Obtém detalhes de um documento específico.

### `listarDocumentos()`
Lista todos os documentos da conta.

### `excluirDocumento($docToken)`
Exclui um documento do ZapSign.

## 🔐 Segurança

- O token da API é armazenado no arquivo `.env` (nunca comite este arquivo)
- Use `.env.example` como modelo
- Logs de erro são registrados automaticamente

## 📞 Suporte

Para dúvidas sobre a API do ZapSign, consulte:
- [Documentação Oficial](https://docs.zapsign.com.br)
- [Suporte ZapSign](mailto:support@zapsign.com.br)

## 🐛 Troubleshooting

### Erro: "PDF não encontrado"
- Verifique se a URL do PDF está acessível publicamente
- Teste acessando a URL diretamente no navegador

### Erro: "Email inválido"
- Certifique-se que o estagiário possui email cadastrado
- Verifique o formato do email

### Erro: "Unauthorized"
- Verifique se o token da API está correto no `.env`
- Confirme que o token não expirou no painel do ZapSign

## 📝 Changelog

### Versão 1.0 (03/11/2025)
- ✅ Integração inicial com ZapSign
- ✅ Envio de documentos para assinatura
- ✅ Interface com modal de confirmação
- ✅ Validação de dados do estagiário
- ✅ Logging de erros
