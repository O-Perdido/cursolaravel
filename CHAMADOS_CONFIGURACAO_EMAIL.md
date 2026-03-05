# Configuração de E-mail para Notificações de Chamados

## Status Atual

O sistema está configurado com `MAIL_MAILER=log`, o que significa que os e-mails **não estão sendo enviados de verdade**, mas sim gravados em arquivos de log para desenvolvimento.

## Como Configurar E-mail Real (Produção)

Para que as notificações de mensagens em chamados funcionem corretamente e os e-mails cheguem aos destinatários, você precisa configurar um servidor SMTP no arquivo `.env`.

### Opção 1: Gmail (Desenvolvimento/Teste)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_de_aplicativo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Importante para Gmail:**
- Você precisa gerar uma "Senha de app" nas configurações de segurança do Google
- Acesse: https://myaccount.google.com/apppasswords
- Ative a verificação em 2 etapas primeiro
- Gere uma senha de 16 dígitos para o Laravel

### Opção 2: Mailtrap (Desenvolvimento)

Ideal para testar e-mails em desenvolvimento sem enviar de verdade:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_username_mailtrap
MAIL_PASSWORD=sua_senha_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sige.local
MAIL_FROM_NAME="${APP_NAME}"
```

Cadastre-se gratuitamente em: https://mailtrap.io

### Opção 3: SMTP Corporativo/Hostgator

Se você tiver um servidor SMTP próprio ou da Hostgator:

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.seudominio.com.br
MAIL_PORT=587
MAIL_USERNAME=noreply@seudominio.com.br
MAIL_PASSWORD=sua_senha_smtp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seudominio.com.br
MAIL_FROM_NAME="SIGE - Sistema de Gestão"
```

### Opção 4: SendGrid (Recomendado para Produção)

Serviço profissional com 100 e-mails/dia gratuitos:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SUA_API_KEY_SENDGRID
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seudominio.com.br
MAIL_FROM_NAME="${APP_NAME}"
```

Cadastre-se em: https://sendgrid.com

## Verificar se E-mail está Funcionando

Após configurar, teste o envio:

```bash
php artisan tinker
```

No tinker, execute:

```php
Mail::raw('Teste de email', function($message) {
    $message->to('seu_email@exemplo.com')
            ->subject('Teste SIGE');
});
```

Se não houver erro e o e-mail chegar, está funcionando!

## Como Funciona no Sistema de Chamados

### Quando os e-mails são enviados?

1. **Operador/Admin envia mensagem** → E-mail vai para todos os usuários da empresa (nível 'empresa')
2. **Empresa envia mensagem** → E-mail vai para todos os admin/operadores do sistema

### Conteúdo do E-mail

- **Assunto:** "Novo retorno no chamado CHAM-YYYYMMDD-XXXXX"
- **Corpo:** 
  - Protocolo do chamado
  - Nome de quem enviou a mensagem
  - Prévia da mensagem (até 600 caracteres)
  - Link direto para abrir o chamado

### Logs de E-mail

Se algum e-mail falhar, fica registrado no Laravel Log em `storage/logs/laravel.log`:

```
[2026-03-05] local.WARNING: Falha ao enviar e-mail de mensagem de chamado {"chamado":123,"email":"teste@exemplo.com","erro":"..."}
```

## Desabilitar Notificações por E-mail

Se quiser desabilitar temporariamente (manter só o chat sem e-mail), comente o método `notificarNovaMensagem` no `ChamadoController`:

```php
// $this->notificarNovaMensagem($chamado, $mensagem, $user);
```

## Troubleshooting

### E-mails não chegam

1. Verifique as configurações do `.env`
2. Execute `php artisan config:clear`
3. Teste com `php artisan tinker` (comando acima)
4. Verifique spam/lixeira
5. Confira os logs em `storage/logs/laravel.log`

### Erro "Connection refused"

- Porta errada (587 ou 465)
- Firewall bloqueando
- Servidor SMTP indisponível

### Erro "Authentication failed"

- Usuário/senha incorretos
- Gmail: precisa gerar senha de app
- Verificar se a conta SMTP está ativa

### E-mails vão para spam

- Configure SPF/DKIM no seu domínio
- Use serviço profissional (SendGrid/Mailgun)
- Evite conteúdo que pareça spam

## Ambiente de Desenvolvimento

Se você quer continuar em desenvolvimento sem enviar e-mails reais, mantenha:

```env
MAIL_MAILER=log
```

Os e-mails ficarão em `storage/logs/laravel.log`.

## Recomendação para Produção

Use **SendGrid** ou **Mailgun** para garantir:
- Alta taxa de entrega
- Reputação de IP limpa
- Métricas de abertura/clique
- Suporte profissional
- Escalabilidade

Nunca use Gmail em produção com alto volume!
