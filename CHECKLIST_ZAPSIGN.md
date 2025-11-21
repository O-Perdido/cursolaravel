# ✅ Checklist - Integração ZapSign

## 📦 Arquivos Criados/Modificados

### Novos Arquivos
- [x] `config/zapsign.php` - Configuração da API
- [x] `app/Services/ZapSignService.php` - Service de integração
- [x] `app/Http/Controllers/ZapSignWebhookController.php` - Controller para webhooks
- [x] `database/migrations/2025_11_03_000001_add_zapsign_fields_to_termos_table.php` - Migração
- [x] `ZAPSIGN_README.md` - Documentação completa
- [x] `INSTALACAO_ZAPSIGN.md` - Guia rápido

### Arquivos Modificados
- [x] `app/Http/Controllers/TermoController.php` - Métodos de envio e verificação
- [x] `app/Models/Termo.php` - Campos fillable do ZapSign
- [x] `resources/views/termos/index.blade.php` - Botão de envio
- [x] `routes/web.php` - Rotas da integração
- [x] `.env` - Variáveis de ambiente
- [x] `.env.example` - Exemplo de configuração

## 🔧 Tarefas de Configuração

### 1. Banco de Dados
- [ ] Executar `php artisan migrate` para adicionar campos na tabela termos
- [ ] Verificar se os campos foram criados corretamente

### 2. Configuração do Ambiente
- [x] Token do ZapSign adicionado no `.env`
- [x] URL da API configurada
- [ ] Testar se as credenciais estão funcionando

### 3. Configuração de PDFs
Escolha uma das opções:

**Opção A: Rota Pública (Mais Simples)**
- [ ] Remover middleware `auth` da rota `termos.downloadPdf`

**Opção B: Storage Público (Recomendado)**
- [ ] Executar `php artisan storage:link`
- [ ] Modificar método `enviarParaZapSign` para salvar PDF no storage
- [ ] Testar acesso público aos PDFs

### 4. Validação de Dados
- [ ] Verificar se todos os estagiários têm email cadastrado
- [ ] Verificar formato dos emails no banco de dados
- [ ] Testar com um estagiário que NÃO tem email (deve mostrar alerta)

## 🧪 Testes a Realizar

### Teste 1: Envio Básico
- [ ] Acessar listagem de termos
- [ ] Clicar no botão verde de assinatura
- [ ] Verificar modal com informações corretas
- [ ] Confirmar envio
- [ ] Verificar mensagem de sucesso
- [ ] Verificar log em `storage/logs/laravel.log`

### Teste 2: Verificar no ZapSign
- [ ] Acessar [Painel ZapSign](https://app.zapsign.com.br)
- [ ] Verificar se documento aparece na lista
- [ ] Verificar se email foi enviado ao estagiário
- [ ] Verificar dados do documento (nome, signatários)

### Teste 3: Assinatura
- [ ] Acessar email do estagiário
- [ ] Clicar no link de assinatura
- [ ] Realizar assinatura eletrônica
- [ ] Verificar se documento fica como "Assinado" no ZapSign

### Teste 4: Verificação de Status
- [ ] Testar método `verificarStatusZapSign`
- [ ] Verificar se status está sendo atualizado corretamente

### Teste 5: Tratamento de Erros
- [ ] Tentar enviar termo sem email de estagiário
- [ ] Tentar enviar com PDF inválido
- [ ] Verificar mensagens de erro amigáveis

## 🔍 Verificações Importantes

### Segurança
- [ ] Token da API não está exposto publicamente
- [ ] Arquivo `.env` está no `.gitignore`
- [ ] Logs não mostram dados sensíveis

### Performance
- [ ] Envio não trava a interface
- [ ] Timeout adequado para chamadas API
- [ ] Logs de erro estão sendo gravados

### UX/UI
- [ ] Botão visível e intuitivo
- [ ] Modal com informações claras
- [ ] Mensagens de sucesso/erro amigáveis
- [ ] Validação de email do estagiário

## 📊 Campos no Banco de Dados

Após executar a migração, verifique se estes campos existem na tabela `tb_termos`:

- [ ] `zapsign_doc_token` (string, nullable)
- [ ] `zapsign_status` (string, nullable)
- [ ] `zapsign_enviado_em` (timestamp, nullable)

## 🔄 Próximas Melhorias (Opcional)

### Webhook
- [ ] Criar rota pública para webhook
- [ ] Configurar URL no painel ZapSign
- [ ] Testar recebimento de notificações
- [ ] Implementar notificações por email quando documento for assinado

### Interface
- [ ] Mostrar status do ZapSign na listagem de termos
- [ ] Badge colorido indicando status (Enviado/Assinado/Recusado)
- [ ] Botão para reenviar documento
- [ ] Botão para baixar PDF assinado

### Funcionalidades Avançadas
- [ ] Adicionar múltiplos signatários (empresa + estagiário + escola)
- [ ] Configurar ordem de assinatura
- [ ] Adicionar campos de formulário no documento
- [ ] Integrar com storage na nuvem (S3, etc)

## 📞 Suporte

Se encontrar problemas:

1. **Verificar logs**: `storage/logs/laravel.log`
2. **Documentação ZapSign**: https://docs.zapsign.com.br
3. **Suporte ZapSign**: support@zapsign.com.br

## 🎉 Conclusão

Quando todos os itens estiverem marcados, a integração estará completa e funcional!

Data de instalação: ____/____/______
Responsável: _______________________
