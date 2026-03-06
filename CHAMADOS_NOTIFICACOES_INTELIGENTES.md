# Sistema de Notificações Inteligentes para Operadores - Chamados

## 📧 Visão Geral

Sistema aprimorado de notificações por e-mail para operadores/admin no módulo de chamados, com:
- **Controle via configuração**: Habilitar/desabilitar notificações por e-mail
- **Responsável vinculado**: E-mails direcionados apenas ao responsável do chamado
- **Logs detalhados**: Rastreamento de quem recebeu cada notificação

## 🎯 Como Funciona

### Fluxo de Notificações

#### 1. Operador/Admin Responde Chamado
✅ **Sempre notifica** → Todos usuários da empresa recebem e-mail
- Comportamento padrão mantido
- Garante que a empresa nunca perde respostas

#### 2. Empresa Responde Chamado
🔧 **Inteligente** → Depende da configuração + responsável

**Cenário A: Configuração DESABILITADA**
```
❌ Nenhum operador recebe e-mail
💡 Útil para ambientes de teste ou quando operadores monitoram painel diretamente
```

**Cenário B: Configuração HABILITADA + Responsável Definido**
```
📧 Apenas o responsável recebe e-mail
✅ Exemplo: João Silva (operador) é responsável → só ele recebe
💡 Evita spam e foca no atendimento direcionado
```

**Cenário C: Configuração HABILITADA + Sem Responsável**
```
📧 Todos operadores/admin recebem e-mail
⚠️ Comportamento padrão quando ninguém assume o chamado
💡 Garante que chamados não caiam no esquecimento
```

## ⚙️ Configuração

### 1. Habilitar/Desabilitar Notificações para Operadores

**Caminho:** Configurações → Aba "Chamados" → Notificações por E-mail

**Toggle:**
```
☑️ Habilitar notificações por e-mail para operadores/admin
```

**Valor padrão:** ✅ Habilitado

### 2. Email Geral/Administrativo (NOVO!)

**Caminho:** Configurações → Aba "Chamados" → E-mail Geral/Administrativo

**Campos:**
```
E-mail para Cópia (opcional): contato@empresa.com.br
☐ Incluir quando há responsável
```

**Como funciona:**
- **Campo de e-mail**: Digite o email que quer receber cópia das notificações
- **Deixar em branco**: Desabilita completamente o envio para email geral
- **Checkbox "Incluir quando há responsável"**:
  - ❌ Desmarcado (padrão): Email geral NÃO recebe quando há responsável definido
  - ✅ Marcado: Email geral recebe MESMO quando há responsável definido

### 3. Atribuir Responsável ao Chamado

**Caminho:** Painel de Chamados → Detalhes do Chamado → Gerenciar Chamado

**Formulário:**
```
Responsável pelo Atendimento: [Selecionar Operador] [Salvar]
```

**Opções:**
- Não atribuído (padrão)
- Lista de todos operadores e admin do sistema

**Dica visual:**
_"Quando atribuído, apenas o responsável receberá notificações por e-mail"_

## 📋 Tabela de Comportamento

| Configuração | Responsável | Email Geral Ativo | Quem Recebe E-mail |
|--------------|-------------|-------------------|--------------------|
| ❌ Desabilitado | Qualquer | Qualquer | Ninguém |
| ✅ Habilitado | ❌ Não definido | ✅ Sim | Todos operadores/admin + email geral |
| ✅ Habilitado | ❌ Não definido | ❌ Não | Todos operadores/admin |
| ✅ Habilitado | ✅ João Silva | ✅ Sim + "Incluir" | João Silva + email geral |
| ✅ Habilitado | ✅ João Silva | ✅ Sim + "Não incluir" | Apenas João Silva |
| ✅ Habilitado | ✅ João Silva | ❌ Não | Apenas João Silva |

## 🔍 Logs e Rastreamento

O sistema registra todas as notificações enviadas:

**Logs disponíveis em:** `storage/logs/laravel.log`

**Exemplos de logs:**

```php
// Notificação para responsável específico
[2026-03-06] local.INFO: Notificação de chamado enviada apenas para responsável
{
    "chamado": "CHAM-20260306-00042",
    "responsavel": "João Silva",
    "email": "joao.silva@ebcp.com.br"
}

// Notificação para todos (sem responsável)
[2026-03-06] local.INFO: Notificação de chamado enviada para todos operadores (sem responsável definido)
{
    "chamado": "CHAM-20260306-00043",
    "total_emails": 5
}
```

## 💻 Implementação Técnica

### Migration
```
database/migrations/2026_03_06_000001_add_configuracao_email_operadores.php
```
- Cria tabela `configuracoes` (se não existir)
- Insere configuração `chamados_notificar_operadores_email`

### Banco de Dados

**Tabela:** `tb_chamados`
- Campo: `fk_id_user_responsavel` (já existia)
- Tipo: `foreignId` nullable
- Relacionamento: `users.id`

**Tabela:** `configuracoes`
- Campo: `chave` = 'chamados_notificar_operadores_email'
- Campo: `valor` = 'true'/'false'
- Campo: `tipo` = 'boolean'

### Lógica no Controller

**Arquivo:** `app/Http/Controllers/ChamadoController.php`

**Método:** `notificarNovaMensagem()`

```php
if ($mensagem->remetente_nivel === 'operador') {
    // Operador respondeu → notificar empresa (sempre)
    $emails = /* todos usuários da empresa */;
} else {
    // Empresa respondeu → verificar configuração
    $notificarOperadores = Configuracao::obter('chamados_notificar_operadores_email', true);
    
    if (!$notificarOperadores) {
        return; // Desabilitado, não envia
    }

    if ($chamado->fk_id_user_responsavel) {
        // Notifica APENAS responsável
        $responsavel = User::find($chamado->fk_id_user_responsavel);
        $emails = [$responsavel->email];
    } else {
        // Notifica TODOS operadores/admin
        $emails = User::whereIn('nivel', ['admin', 'operador'])->pluck('email');
    }
}
```

### Views

**Configurações:** `resources/views/configuracoes/index.blade.php`
- Toggle switch na aba "Chamados"
- Card explicativo com instruções

**Detalhes Admin:** `resources/views/chamados/detalhes-admin.blade.php`
- Card "Gerenciar Chamado"
- Select para escolher responsável
- Informação sobre impacto nas notificações

## 🚀 Como Usar na Ppenas Responsável Recebe (Padrão)
```
Configurações:5
- ✅ Habilitar notificações
- E-mail geral: contato@empresa.com.br
- ❌ "Incluir quando há responsável" (desmarcado)

Resultado: Apenas João Silva recebe
```

### Caso de Uso 2: Responsável + Email Geral
```6
Configurações:
- ✅ Habilitar notificações
- E-mail geral: contato@empresa.com.br
- ✅ "Incluir quando há responsável" (marcado)

Resultado: João Silva + contato@empresa.com.br recebem
```

### Caso de Uso 3: Desabilitar Email Geral Completamente
```
Configurações:
- ✅ Habilitar notificações
- E-mail geral: (deixar em branco)
- Checkbox não importa

Resultado: Apenas responsável recebe, email geral nunca recebe
```

### Caso de Uso 4: Ambiente de Teste (Sem Operadores)

### Caso de Uso 1: Atendimento Pessoal
```
1. Operador recebe chamado novo
2. Operador clica em "Detalhes"
3. Operador seleciona a si mesmo como responsável
4. Operador clica em "Salvar"
5. ✅ Agora só ele recebe e-mails desse chamado
```

### Caso de Uso 2: Redistribuir Chamado
```
1. João Silva está como responsável
2. Admin entra no chamado
3. Admin muda responsável para "Maria Santos"
4. Admin clica em "Salvar"
5. ✅ Agora Maria recebe os e-mails (João para de receber)
```

### Caso de Uso 7: Remover Responsável
```
1. Chamado tem responsável definido
2. Admin seleciona "Não atribuído"
3. Admin clica em "Salvar"
4. ✅ Volta a notificar TODOS operadores/admin
```

### Caso de Uso 8: Desabilitar Todas Notificações
```
1. Admin vai em Configurações → Chamados
2. Admin desmarca "Habilitar notificações por e-mail para operadores/admin"
3. Admin clica em "Salvar Configurações de Chamados"
4. ✅ Operadores param de receber e-mails (empresas continuam recebendo)
```

## ⚠️ Observações Importantes

### Empresas Sempre Recebem
- Notificações para empresas **nunca são desabilitadas**
- Mesmo com configuração off, empresas recebem quando operador responde
- Garante que clientes sempre sejam notificados

### Responsável Não Altera Visualização
- Todos operadores/admin podem ver todos os chamados no painel
- Responsável afeta APENAS as notificações por e-mail
- Sistema não implementa "ownership" restritivo

### Status vs Responsável
- São conceitos independentes
- Status: Pendente, Em Análise, Em Andamento, Concluído, Cancelado
- Responsável: Quem recebe notificações daquele chamado

### Remoção de Usuário
- Se responsável for deletado do sistema, campo fica NULL
- Chamado volta a notificar todos operadores automaticamente
- Relacionamento: `onDelete: SET NULL` (já configurado)

## 📊 Benefícios

### Para Operadores
- ✅ Reduz spam de e-mails
- ✅ Foco em chamados relevantes
- ✅ Clareza sobre responsabilidades

### Para Admin
- ✅ Controle granular de notificações
- ✅ Possibilidade de testar sem spam
- ✅ Distribuição organizada de trabalho

### Para o Sistema
- ✅ Menos e-mails enviados = melhor performance
- ✅ Logs detalhados para auditoria
- ✅ Flexibilidade para diferentes fluxos de trabalho

## 🛠️ Troubleshooting

### Operador não está recebendo e-mails

**Checklist:**
1. ☑️ Configuração está habilitada? (Configurações → Chamados)
2. ☑️ Operador está definido como responsável?
3. ☑️ E-mail do operador está cadastrado no perfil?
4. ☑️ SMTP está configurado corretamente? (veja CHAMADOS_CONFIGURACAO_EMAIL.md)
5. ☑️ Verificar logs em `storage/logs/laravel.log`

### Todos operadores recebem mesmo com responsável

**Verificar:**
1. Confirmar que responsável foi salvo (recarregar página)
2. Ver se campo `fk_id_user_responsavel` está NULL no banco
3. Conferir logs para validar lógica

### Configuração não persiste

**Solução:**
```bash
php artisan config:clear
php artisan cache:clear
```

## 📝 Migration Necessária

**Executar após implementação:**
```bash
php artisan migrate
```

**Migration criada:**
- `2026_03_06_000001_add_configuracao_email_operadores.php`

## 🔗 Arquivos Relacionados

### Backend
- `app/Http/Controllers/ChamadoController.php` (notificarNovaMensagem)
- `app/Http/Controllers/ConfiguracaoController.php` (salvar configuração)
- `app/Models/Configuracao.php` (obter configuração)
- `app/Models/Chamado.php` (relacionamento responsavel)

### Frontend
- `resources/views/chamados/detalhes-admin.blade.php` (seletor responsável)
- `resources/views/configuracoes/index.blade.php` (toggle notificações)

### Database
- `database/migrations/2026_03_06_000001_add_configuracao_email_operadores.php`

### Documentação
- `SISTEMA_CHAMADOS_README.md` (documentação geral)
- `CHAMADOS_CONFIGURACAO_EMAIL.md` (configuração SMTP)
- `CHAMADOS_MELHORIAS_RESUMO.md` (resumo de melhorias)

---

**✅ Sistema pronto para uso em produção!**
