# Sistema de Chamados - Guia de Instalação

## Visão Geral

Sistema modular de chamados para unidades concedentes (empresas) com suporte a tipos específicos (Rescisão, Alteração) e tipos personalizados.

## Funcionalidades Implementadas

### Para Unidades Concedentes (nível: empresa)
- ✅ Abrir chamados através de modal na página inicial
- ✅ Formulários específicos por tipo de chamado:
  - **Rescisão**: Seleção de termo + data + motivo
  - **Alteração**: Seleção de termo + descrição da alteração
  - **Outros/Personalizados**: Título + detalhes + anexos
- ✅ Busca de termos com filtro por CPF/Nome/Número
- ✅ Visualizar e cancelar chamados próprios
- ✅ Conversa em formato chat dentro do detalhe do chamado
- ✅ **Enviar até 5 anexos por mensagem** ⭐
- ✅ **Loading state ao enviar mensagens** (previne duplicação) ⭐
- ✅ **Badge de notificação no card da home** ⭐
- ✅ Notificação visual de mensagens não lidas na listagem
- ✅ Sistema de protocolo único automático

### Para Admin/Operador
- ✅ CRUD completo de tipos de chamados
- ✅ Tipos do sistema (Rescisão/Alteração) não podem ser removidos
- ✅ Visualizar todos os chamados do sistema
- ✅ Responder chamados via chat no detalhe administrativo
- ✅ **Enviar múltiplos anexos em respostas** ⭐
- ✅ **Excluir chamados completos** (com cascata) ⭐
- ✅ **Atribuir responsável ao chamado** ⭐ Novo
- ✅ **Notificações inteligentes por e-mail** (apenas responsável ou todos) ⭐ Novo
- ✅ **Badges animados com sino** para novas mensagens ⭐
- ✅ Notificação visual de novas mensagens da unidade concedente no painel
- ✅ Notificação por e-mail para o outro lado da conversa
- ✅ Gerenciar tipos personalizados nas configurações

## Instalação

### 1. Executar Migrations

```bash
php artisan migrate
```

Isso criará as tabelas:
- `tb_tipos_chamados`: Armazena os tipos de chamados
- `tb_chamados`: Armazena os chamados abertos
- `tb_chamados_mensagens`: Armazena o histórico de mensagens do chat
  - Campo `anexos` (json): Armazena array de caminhos de arquivos anexados

### 2. Executar Seeder

```bash
php artisan db:seed --class=TiposChamadosSeeder
```

Isso criará os tipos iniciais:
- Rescisão de Contrato
- Alteração de Termo de Contrato
- Outros

### 3. Criar link simbólico para storage (se ainda não criou)

```bash
php artisan storage:link
```

Necessário para anexos de chamados funcionarem.

## Estrutura de Arquivos Criados

### Migrations
- `2025_12_20_000001_create_tipos_chamados_table.php`
- `2025_12_20_000002_create_chamados_table.php`
- `2026_03_05_000001_create_chamados_mensagens_table.php`
- `2026_03_05_000002_add_anexos_to_chamados_mensagens.php` ⭐ Nova

### Models
- `app/Models/TipoChamado.php`
- `app/Models/Chamado.php`
- `app/Models/ChamadoMensagem.php`

### Controllers
- `app/Http/Controllers/ChamadoController.php`
- `app/Http/Controllers/TipoChamadoController.php`

### Mail
- `app/Mail/ChamadoMensagemRecebidaMail.php`

### Views

#### Chamados (Empresa/Admin/Operador)
- `resources/views/chamados/index.blade.php` - Lista de chamados
- `resources/views/chamados/create.blade.php` - Formulário de criação
- `resources/views/chamados/show.blade.php` - Detalhes do chamado
- `resources/views/chamados/partials/modal-novo-chamado.blade.php` - Modal de seleção
- `resources/views/chamados/detalhes-admin.blade.php` - Detalhes administrativos com chat

#### E-mails
- `resources/views/emails/chamado_mensagem_recebida.blade.php`

#### Admin (Tipos de Chamados)
- `resources/views/admin/tipos-chamados/index.blade.php`
- `resources/views/admin/tipos-chamados/create.blade.php`
- `resources/views/admin/tipos-chamados/edit.blade.php`

### Seeders
- `database/seeders/TiposChamadosSeeder.php`

## Rotas Adicionadas

### Empresas, Admin e Operador
```ph  /chamados                      # Lista de chamados
GET    /chamados/create               # Formulário de criação
POST   /chamados                      # Criar chamado
GET    /chamados/{id}                 # Detalhes do chamado
POST   /chamados/{id}/mensagens       # Enviar mensagem no chat (com anexos)
PUT    /chamados/{id}/cancelar        # Cancelar chamado (empresa)
DELETE /chamados/{id}                 # Excluir chamado (admin/operador) ⭐ Nova
PUT  /chamados/{id}/cancelar        # Cancelar chamado (empresa)
```

### APIs (AJAX)
```php
GET /api/chamados/buscar-termos    # Buscar termos para Select2
GET /api/tipos-chamados/ativos     # Listar tipos ativos
```

### Admin (Gerenciamento de Tipos)
```php
GET    /admin/tipos-chamados           # Lista de tipos
GET    /admin/tipos-chamados/create    # Criar tipo
POST   /admin/tipos-chamados           # Salvar tipo
GET    /admin/tipos-chamados/{id}/edit # Editar tipo
PUT    /admin/tipos-chamados/{id}      # Atualizar tipo
DELETE /admin/tipos-chamados/{id}      # Remover tipo
```

## Uso

### Para Unidade Concedente

1. **Abrir Chamado**
   - Na página inicial, clique no botão "Abrir Chamado"
   - Selecione o tipo de chamado no modal
   - Preencha o formulário específico
   - Clique em "Abrir Chamado"

2. **Visualizar Chamados**
   - Acesse "Chamados" no menu ou card da home
   - Veja todos os seus chamados com status e protocolo
   - Clique para ver detalhes

3. **Cancelar Chamado**
   - Na listagem ou detalhes, clique em "Cancelar"
   - Só é possível cancelar chamados não finalizados

### Para Admin

1. **Gerenciar Tipos de Chamados**
   - Acesse: Configurações > Tipos de Chamados
   - Crie novos tipos personalizados
   - Ative/desative tipos existentes
   - Altere ordem de exibição

2. **Ver Todos os Chamados**
   - Acesse menu "Chamados"
   - Visualize chamados de todas as empresas
   - Acompanhe status e responsáveis

## Campos da Tabela `tb_chamados`

### Relacionamentos
- `fk_id_tipo_chamado`: Tipo do chamado
- `fk_id_empresa`: Empresa solicitante
- `fk_id_user_solicitante`: Usuário que abriu
- `fk_id_termo`: Termo (para Rescisão/Alteração)
- `fk_id_user_responsavel`: Operador responsável (opcional)

### Campos por Tipo

#### Rescisão
- `data_rescisao`: Data da rescisão
- `motivo_rescisao`: Motivo detalhado

#### Alteração
- `descricao_alteracao`: Descrição da alteração solicitada

#### Genéricos (Outros/Personalizados)
- `titulo`: Título resumido (máx 200 caracteres)
- `detalhes`: Descrição detalhada (máx 5000 caracteres)
- `anexos`: Array JSON com caminhos dos arquivos

### Controle
- `protocolo`: Formato CHAM-YYYYMMDD-XXXXX (gerado automaticamente)
- `status`: pendente, em_analise, em_andamento, concluido, cancelado
- `observacoes_internas`: Notas do operador (não visível para empresa)
- `data_conclusao`: Data de conclusão

## Campos da Tabela `tb_chamados_mensagens`

- `id_chamado_mensagem`: ID da mensagem
- `fk_id_chamado`: Chamado relacionado
- `fk_id_user_remetente`: Usuário que enviou a mensagem
- `remetente_nivel`: `empresa` ou `operador`
- `mensagem`: Conteúdo textual da resposta
- `lido_empresa_em`: Data/hora de leitura pela unidade concedente
- `lido_operador_em`: Data/hora de leitura por admin/operador

## Validações

### Rescisão
- Termo: obrigatório, deve estar ativo e pertencer à empresa
- Data rescisão: obrigatória, formato data
- Motivo: obrigatório, máx 1000 caracteres

### Alteração
- Termo: obrigatório, deve estar ativo e pertencer à empresa
- Descrição alteração: obrigatória, máx 2000 caracteres

### Genéricos
- Título: obrigatório, máx 200 caracteres
- Detalhes: obrigatório, máx 5000 caracteres
- Anexos: opcional, formatos PDF/JPG/PNG/DOC/DOCX, máx 5MB cada

## Busca de Termos (Select2)

O campo de seleção de termos nos formulários de Rescisão e Alteração usa Select2 com busca AJAX:

- Busca por: número do termo, nome do estagiário, CPF
- Mínimo 2 caracteres
- Retorna apenas termos ativos da empresa logada
- Formato: "Número - Nome do Estagiário"

## Status de Chamados

- **Pendente** (amarelo): Aguardando análise
- **Em Análise** (azul): Sendo analisado pela equipe
- **Em Andamento** (roxo): Ação em progresso
- **Concluído** (verde): Finalizado com sucesso
- **Cancelado** (vermelho): Cancelado pela empresa ou admin

## Chat e Notificações

- Cada chamado possui um histórico de mensagens na página de detalhes
- Ao abrir o chamado, as mensagens do outro lado são marcadas como lidas
- A listagem (`/chamados`) mostra badge de mensagens novas para empresa/admin/operador
- O painel (`/painel/chamados`) mostra badge de mensagens novas enviadas pela unidade concedente
- **Notificações por e-mail inteligentes:**
  - ✅ Operador responde → **Sempre** notifica todos usuários da empresa
  - 🔧 Empresa responde → **Depende da configuração:**
    - Se desabilitado → Nenhum operador recebe e-mail
    - Se habilitado + responsável definido → **Apenas o responsável** recebe
    - Se habilitado + sem responsável → **Todos operadores/admin** recebem
- Possibilidade de atribuir **responsável** ao chamado (operador/admin específico)
- Configuração global para habilitar/desabilitar notificações de operadores

**Veja mais:** [CHAMADOS_NOTIFICACOES_INTELIGENTES.md](CHAMADOS_NOTIFICACOES_INTELIGENTES.md)

## Permissões

### Empresa
- Abrir chamados próprios
- Visualizar apenas seus chamados
- Cancelar chamados próprios (se não finalizados)

### Admin/Operador
- Visualizar todos os chamados
- Gerenciar tipos de chamados (admin)
- Adicionar observações internas
- Alterar status
- Atribuir responsáveis

## Próximos Passos (Sugestões)

- [ ] Incluir anexos por mensagem no chat
- [ ] Adicionar notificação em tempo real (WebSocket/polling)
- [ ] Registrar eventos de mudança de status no histórico do chat
- [ ] Criar filtros por chamados com mensagens não lidas
- [ ] Expandir relatórios e métricas de tempo de resposta

## Notas Técnicas

- Protocolos são únicos e sequenciais por dia
- Anexos são armazenados em `storage/app/public/chamados/anexos`
- Select2 requer bibliotecas CSS/JS (já incluídas nas views)
- Soft deletes habilitado em ambos models
- Relacionamentos eager loading para performance

## Melhorias e Recursos Avançados (2026)

### 📎 Anexos em Mensagens do Chat
- Suporte a até **5 arquivos por mensagem**
- Tamanho máximo de **5MB por arquivo**
- Formatos: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, TXT, ZIP, RAR
- Download direto dos anexos nas mensagens
- Limpeza automática de arquivos ao excluir chamado

**Migration adicional necessária:**
```bash
php artisan migrate  # Executa 2026_03_05_000002_add_anexos_to_chamados_mensagens.php
```

### ⏳ Loading States
- Spinner de loading ao enviar mensagens
- Botão desabilitado durante envio
- Previne duplicação de mensagens por cliques múltiplos

### 🔔 Notificações Visuais Aprimoradas
- **Badge na Home**: Card de "Chamados" mostra total de mensagens não lidas
- **Badges Animados**: Efeito pulse em notificações do painel de operadores
- **Ícones Intuitivos**: Sino (🔔) para notificações, clipe (📎) para anexos
- **Cores Chamativas**: Vermelho com texto "nova(s)" para operadores

### 🗑️ Exclusão Completa de Chamados
- Operadores e admin podem excluir chamados
- Modal de confirmação antes da exclusão
- Exclusão em cascata: chamado + mensagens + arquivos anexos
- Limpeza automática do storage (sem arquivos órfãos)

**Rota adicional:**
```php
DELETE /chamados/{id}  # Apenas admin/operador
```

### 📧 Sistema de E-mail
- Notificações automáticas quando mensagem é recebida
- Empresa recebe e-mail quando operador/admin responde
- Operador/Admin recebem e-mail quando empresa responde
- **[CHAMADOS_NOTIFICACOES_INTELIGENTES.md](CHAMADOS_NOTIFICACOES_INTELIGENTES.md)** - Sistema de notificações com responsável ⭐ Novo
- Link direto para o chamado no corpo do e-mail

**Configuração:** Veja [CHAMADOS_CONFIGURACAO_EMAIL.md](CHAMADOS_CONFIGURACAO_EMAIL.md) para configurar SMTP/SendGrid/Gmail

### 📚 Documentação Adicional
- **[CHAMADOS_MELHORIAS_RESUMO.md](CHAMADOS_MELHORIAS_RESUMO.md)** - Resumo completo de todas as melhorias
- **[CHAMADOS_CONFIGURACAO_EMAIL.md](CHAMADOS_CONFIGURACAO_EMAIL.md)** - Guia de configuração de e-mail

## Troubleshooting

### Erro ao carregar tipos de chamado no modal
- Verifique se executou o seeder
- Confirme que há tipos ativos no banco

### Erro ao fazer upload de anexos
- Execute `php artisan storage:link`
- Verifique permissões da pasta storage

### Select2 não carrega termos
- Verifique se a empresa tem termos ativos
- Confirme autenticação do usuário empresa

### Tipos do sistema não aparecem para deletar
- Correto! Rescisão e Alteração são protegidos (campo `sistema = true`)
