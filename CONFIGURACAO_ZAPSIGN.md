# 📋 Guia de Configuração - Integração ZapSign

Este guia explica **passo a passo** como configurar a integração com a API do ZapSign no seu sistema.

---

## 🔑 1. Obter o Token da API ZapSign

### 1.1. Acesse o painel ZapSign
- Entre em: https://app.zapsign.com.br/
- Faça login com suas credenciais

### 1.2. Navegue até a seção de API
1. Clique no menu **Configurações** (ícone de engrenagem no canto superior direito)
2. No menu lateral, clique em **Integrações**
3. Clique em **API ZapSign**
4. Você verá o seu **Token de Acesso** (uma string longa como: `2af49f29-9e90-4381-be11-...`)

### 1.3. Copie o token
- Clique no ícone de copiar ao lado do token
- **Importante:** Guarde este token em local seguro, ele dá acesso total à sua conta ZapSign

---

## ⚙️ 2. Configurar o Sistema (Arquivo .env)

Abra o arquivo `.env` na raiz do seu projeto Laravel e configure as seguintes variáveis:

```env
# ZapSign API Configuration
ZAPSIGN_API_TOKEN=seu_token_aqui_copiado_do_painel
ZAPSIGN_API_URL=https://api.zapsign.com.br/api/v1
ZAPSIGN_SANDBOX=false
ZAPSIGN_WEBHOOK_URL=
ZAPSIGN_WEBHOOK_SECRET=
ZAPSIGN_WEBHOOK_HEADER=Authorization
```

### Explicação das variáveis:

- **`ZAPSIGN_API_TOKEN`**: Cole aqui o token que você copiou do painel ZapSign (passo 1.3)
- **`ZAPSIGN_API_URL`**: URL base da API (já está configurada corretamente)
- **`ZAPSIGN_SANDBOX`**: 
  - `true` = modo teste (documentos não têm validade legal)
  - `false` = modo produção (documentos têm validade legal) ✅ **Use false em produção**
- **`ZAPSIGN_WEBHOOK_URL`**: Será configurado no próximo passo
- **`ZAPSIGN_WEBHOOK_SECRET`**: Segredo para validar webhooks (recomendado)
- **`ZAPSIGN_WEBHOOK_HEADER`**: Nome do cabeçalho HTTP usado para autenticar o webhook

---

## 🔔 3. Configurar Webhook (Atualizações Automáticas de Status)

O webhook permite que o ZapSign notifique seu sistema automaticamente quando:
- Um documento é assinado
- Uma assinatura é recusada
- O status do documento muda

### 3.1. Determinar a URL do webhook

A URL do webhook do seu sistema é:
```
https://seu-dominio.com.br/webhooks/zapsign
```

**Exemplos:**
- Produção: `https://sige.ebcp.com.br/webhooks/zapsign`
- Desenvolvimento local (usando ngrok/Laragon público): `https://abc123.ngrok.io/webhooks/zapsign`

⚠️ **Importante:** Em desenvolvimento local, você precisará expor o servidor usando ferramentas como:
- **ngrok** (recomendado): `ngrok http 80`
- **Laragon** com IP público configurado
- Servidor de homologação/staging

### 3.2. Gerar um segredo (secret) para o webhook

Para segurança, gere uma string aleatória forte:

```bash
# No terminal do Laravel, execute:
php artisan tinker

# Depois digite:
Str::random(64)
```

Copie a string gerada e guarde.

### 3.3. Configurar no arquivo .env

Atualize as variáveis no `.env`:

```env
ZAPSIGN_WEBHOOK_URL=https://seu-dominio.com.br/webhooks/zapsign
ZAPSIGN_WEBHOOK_SECRET=cole_aqui_a_string_gerada_no_passo_3.2
ZAPSIGN_WEBHOOK_HEADER=Authorization
```

### 3.4. Registrar o webhook no painel ZapSign

1. Acesse: https://app.zapsign.com.br/
2. Vá em **Configurações** > **Integrações** > **Webhooks**
3. Clique em **"+ Novo Webhook"** ou **"Adicionar"**
4. Preencha:
   - **URL do Webhook**: `https://seu-dominio.com.br/webhooks/zapsign`
   - **Eventos**: Selecione os eventos que deseja receber:
     - ✅ `document.finished` (documento concluído/assinado)
     - ✅ `document.partial` (assinatura parcial)
     - ✅ `document.refused` (documento recusado)
     - ✅ `document.cancelled` (documento cancelado)
     - ✅ `document.error` (erro no processamento)
   - **Cabeçalho de autenticação** (Headers customizados):
     - Nome: `Authorization`
     - Valor: `Bearer {cole_aqui_o_secret_do_passo_3.2}`
5. Clique em **Salvar**

### 3.5. Testar o webhook

Após configurar, você pode testar enviando um documento de teste:
1. Envie um termo para o ZapSign pelo sistema
2. Assine o documento (ou peça para um destinatário assinar)
3. Verifique nos logs do Laravel se o webhook foi recebido:
   - Banco de dados: tabela `zapsign_webhook_logs`
   - Arquivo de log: `storage/logs/laravel.log`
4. Verifique se o status do termo foi atualizado na lista de termos

---

## ✅ 4. Verificar se está funcionando

### 4.1. Enviar um termo para assinatura

1. Acesse **Termos** no menu
2. Clique no botão **📄** (ícone de assinatura) ou abra os detalhes de um termo
3. Clique em **ZapSign** e confirme o envio
4. Verifique se:
   - Aparece a mensagem de sucesso
   - Na tela de detalhes do termo, o status ZapSign mudou para "Enviado"
   - Os destinatários receberam o e-mail do ZapSign

### 4.2. Verificar atualização de status

1. Peça para um destinatário assinar o documento
2. Acompanhe na lista de termos ou nos detalhes:
   - Status deve mudar automaticamente para "Parcialmente assinado" ou "Assinado"
3. Use o botão **"Atualizar status"** na tela de detalhes para consultar manualmente

### 4.3. Verificar logs de webhook

```bash
# Ver últimos webhooks recebidos no banco de dados
php artisan tinker

# Depois digite:
\App\Models\ZapSignWebhookLog::latest()->take(5)->get(['document_token', 'status', 'created_at'])
```

Ou acesse direto no banco de dados a tabela `zapsign_webhook_logs`.

---

## 🔧 5. Solução de Problemas

### ❌ Erro: "Token inválido" ou "Unauthorized"
- Verifique se o `ZAPSIGN_API_TOKEN` no `.env` está correto
- Confirme que copiou o token completo do painel ZapSign
- Rode `php artisan config:clear` para limpar o cache de configuração

### ❌ Webhook não está chegando
1. Confirme que a URL do webhook está acessível publicamente
   - Teste abrindo a URL no navegador (deve retornar erro 405 Method Not Allowed, o que é normal)
2. Verifique se o firewall/servidor permite requisições POST externas
3. Em desenvolvimento local:
   - Use ngrok: `ngrok http 80` e registre a URL gerada no ZapSign
   - Verifique se a URL pública está acessível

### ❌ Status não atualiza automaticamente
- Verifique se o webhook foi registrado corretamente no painel ZapSign
- Confirme que os eventos estão selecionados (especialmente `document.finished`)
- Consulte a tabela `zapsign_webhook_logs` para ver se os eventos estão chegando
- Rode `php artisan queue:work` se estiver usando filas

### ❌ Erro ao enviar documento
- Verifique se todos os destinatários têm e-mail cadastrado
- Confirme que o `ZAPSIGN_SANDBOX` está configurado conforme desejado
- Verifique os logs: `storage/logs/laravel.log`

---

## 📚 6. Recursos Adicionais

### Documentação oficial ZapSign
- API: https://docs.zapsign.com.br/
- Webhooks: https://docs.zapsign.com.br/documentos/webhooks
- Posicionamento de assinaturas: https://docs.zapsign.com.br/documentos/opcional-posicionar-assinaturas

### Comandos úteis Laravel
```bash
# Limpar cache de configuração
php artisan config:clear

# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Executar migrations (se necessário)
php artisan migrate

# Gerar uma string aleatória para secret
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

---

## 🎯 7. Checklist Final

Antes de usar em produção, confirme:

- [ ] Token da API configurado no `.env`
- [ ] `ZAPSIGN_SANDBOX=false` em produção
- [ ] URL do webhook configurada e acessível
- [ ] Webhook secret gerado e configurado
- [ ] Webhook registrado no painel ZapSign com os eventos corretos
- [ ] Cabeçalho de autenticação configurado no webhook (Authorization)
- [ ] Teste de envio funcionando
- [ ] Webhook recebendo eventos (verificar `zapsign_webhook_logs`)
- [ ] Status atualizando automaticamente

---

## 💡 Dicas de Segurança

1. **Nunca compartilhe** o `ZAPSIGN_API_TOKEN` publicamente
2. **Use webhook secret** (ZAPSIGN_WEBHOOK_SECRET) para validar que os eventos vêm do ZapSign
3. **Mantenha o .env** fora do controle de versão (já deve estar no `.gitignore`)
4. **Em produção**, use HTTPS para o webhook
5. **Monitore os logs** de webhook periodicamente

---

**Pronto!** 🎉 Sua integração com o ZapSign está configurada e funcionando.

Se precisar de ajuda adicional, consulte a documentação oficial ou entre em contato com o suporte do ZapSign.
