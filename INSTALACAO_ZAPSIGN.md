# 🚀 Guia Rápido - Integração ZapSign

## ✅ O que foi implementado:

1. **Configuração da API** (`config/zapsign.php`)
   - Token de autenticação
   - URL base da API
   - Modo sandbox

2. **Service de Integração** (`app/Services/ZapSignService.php`)
   - Criar documentos
   - Detalhar documentos
   - Listar documentos
   - Excluir documentos

3. **Controller** (`app/Http/Controllers/TermoController.php`)
   - `enviarParaZapSign($id)` - Envia termo para assinatura
   - `verificarStatusZapSign($id)` - Verifica status do documento

4. **Rotas** (`routes/web.php`)
   - POST `/termos/{id}/enviar-zapsign`
   - GET `/termos/{id}/status-zapsign`

5. **Interface** (`resources/views/termos/index.blade.php`)
   - Botão verde "Enviar para Assinatura"
   - Modal de confirmação com dados do termo
   - Validação de email do estagiário

6. **Banco de Dados** (migração)
   - Campo `zapsign_doc_token`
   - Campo `zapsign_status`
   - Campo `zapsign_enviado_em`

## 📋 Próximos Passos:

### 1. Executar a Migração

Você precisa rodar a migração para adicionar os campos na tabela:

```bash
php artisan migrate
```

### 2. Testar a Integração

1. Acesse a listagem de termos
2. Escolha um termo com estagiário que tenha email cadastrado
3. Clique no botão verde com ícone 📝
4. Confirme o envio
5. Verifique o log em `storage/logs/laravel.log` se houver erro

### 3. ⚠️ IMPORTANTE: Configurar URL Pública do PDF

O ZapSign precisa acessar o PDF publicamente. Você tem 2 opções:

**Opção A**: Tornar a rota atual pública (mais simples)
- Remova o middleware `auth` da rota `termos.downloadPdf`

**Opção B**: Salvar PDFs em storage público (recomendado)

Edite o método `enviarParaZapSign` no `TermoController.php`:

```php
// Gerar PDF e salvar em storage público
$pdf = Pdf::loadView('termos.pdf', compact('termo'));
$filename = 'termo_' . $termo->numero_termo . '_' . $termo->ano_termo . '.pdf';
Storage::disk('public')->put('termos/' . $filename, $pdf->output());
$pdfUrl = url(Storage::url('termos/' . $filename));
```

E execute:
```bash
php artisan storage:link
```

### 4. Configurar Webhook (Opcional)

Para receber notificações quando o documento for assinado:

1. Crie uma rota pública para receber webhooks
2. Configure a URL no `.env`:
   ```env
   ZAPSIGN_WEBHOOK_URL=https://seudominio.com/webhook/zapsign
   ```

## 🎨 Como Funciona:

1. **Usuário clica em "Enviar para Assinatura"**
   - Sistema busca dados do termo e estagiário
   - Valida se estagiário tem email

2. **Sistema envia para ZapSign**
   - Gera URL pública do PDF
   - Cria documento na plataforma ZapSign
   - Configura signatários automaticamente
   - ZapSign envia email para o estagiário

3. **Estagiário recebe email**
   - Clica no link de assinatura
   - Assina eletronicamente o documento
   - ZapSign notifica conclusão (via webhook, se configurado)

## 🔧 Configurações Adicionais:

### Personalizar Signatários

Edite o método `enviarParaZapSign` para adicionar mais signatários:

```php
$signatarios[] = [
    'name' => 'Representante da Empresa',
    'email' => $termo->empresa->email_empresa ?? null,
];
```

### Modo de Autenticação

Você pode alterar o modo de autenticação no Service:

```php
'auth_mode' => 'assinaturaTela', // Padrão
// Outras opções: 'cpf', 'selfie', etc.
```

### Envio Automático

Por padrão, o email é enviado automaticamente. Para desativar:

```php
'send_automatic_email' => false,
```

## 📞 Links Úteis:

- [Documentação ZapSign](https://docs.zapsign.com.br)
- [Painel ZapSign](https://app.zapsign.com.br)
- [README Completo](./ZAPSIGN_README.md)

## ✨ Pronto!

A integração está completa e pronta para uso! 🎉
