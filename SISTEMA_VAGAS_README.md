# Sistema de Vagas de Estágio - Implementação Completa

## Resumo
Foi implementado um sistema completo de cadastro e gerenciamento de vagas de estágio, permitindo que empresas publiquem oportunidades e que admin/operadores vinculem termos automaticamente às vagas disponíveis.

## Funcionalidades Implementadas

### 1. Cadastro de Vagas (CRUD Completo)
- **Tabela**: `tb_vagas` com todos os campos necessários
- **Número Sequencial**: Formato `YYYY-SEQ` gerado automaticamente por empresa/ano
- **Campos**: 
  - Dados da vaga (atividades, orientador, cargo, datas, horário, local, lotação)
  - Valores (bolsa, auxílio transporte)
  - Status (disponível, preenchida, expirada)
  - Vinculação (fk_id_termo, vinculo_tipo)
- **Controller**: `app/Http/Controllers/VagaController.php`
- **Views**: 
  - `resources/views/vagas/index.blade.php` (listagem com filtros)
  - `resources/views/vagas/create.blade.php` (cadastro)
  - `resources/views/vagas/edit.blade.php` (edição)
- **Rotas**: Protegidas por middlewares `admin_ou_operador` e `nivel:empresa`

### 2. Controle de Acesso
- **Empresa**: Vê e gerencia apenas suas próprias vagas
- **Admin/Operador**: Acesso total a todas as vagas de todas as empresas
- **Bloqueios**: Não pode editar/excluir vagas já vinculadas a termos

### 3. Vinculação Termo-Vaga
- **Campo de seleção**: Aparece no formulário de cadastro de termo após selecionar empresa
- **Preenchimento automático**: Quando vaga é selecionada, todos os campos são preenchidos automaticamente:
  - Atividades, orientador, cargo orientador
  - Datas de início e término
  - Horário, local, lotação
  - Valores de bolsa e auxílio transporte
- **Campos readonly**: Após vincular, campos ficam somente leitura (exceto estagiário, escola, supervisor)
- **Replicação *_fixo**: Dados da vaga são copiados para campos normais E campos *_fixo (padrão de auditoria do sistema)

### 4. Desvinculação Automática
- **Ao excluir termo**: Vaga vinculada volta automaticamente para status "disponível"
- **Limpeza de dados**: Remove fk_id_termo e vinculo_tipo da vaga

### 5. Validações Implementadas
- **Backend**:
  - `data_inicio < data_termino` (validação nativa Laravel)
  - `data_termino` não pode estar no passado
  - Vaga deve pertencer à empresa selecionada
  - Validações de existência de FK (empresa, local)
  
- **Frontend**:
  - Alerta se vaga expirada for selecionada (data_termino < hoje)
  - Busca de vagas filtra automaticamente apenas disponíveis e não expiradas
  - Feedback visual de campos readonly quando vaga vinculada

### 6. API/Endpoints
- **GET** `/api/vagas-por-empresa?empresa_id={id}`: Retorna vagas disponíveis de uma empresa
- **Filtros**: Status disponível + data_termino >= hoje
- **Resposta**: JSON com dados completos da vaga incluindo local

## Arquivos Criados/Modificados

### Novos Arquivos
1. `database/migrations/2025_11_12_000000_create_tb_vagas_table.php`
2. `database/migrations/2025_11_12_000001_add_vaga_vinculo_to_tb_termos.php`
3. `database/migrations/ADD_VAGAS_MANUAL.sql` (script manual para banco existente)
4. `app/Models/Vaga.php`
5. `app/Http/Controllers/VagaController.php`
6. `resources/views/vagas/index.blade.php`
7. `resources/views/vagas/create.blade.php`
8. `resources/views/vagas/edit.blade.php`

### Arquivos Modificados
1. `app/Models/Termo.php` - Adicionar relacionamento `vaga()` e campos ao fillable
2. `app/Http/Controllers/TermoController.php`:
   - Método `buscarVagasPorEmpresa()` para API
   - Método `store()` atualizado com lógica de vinculação
   - Método `destroy()` atualizado com lógica de desvinculação
3. `routes/web.php` - Adicionar rotas de vagas e endpoint API
4. `resources/views/termos/create.blade.php`:
   - Campo select de vaga
   - JavaScript para preenchimento automático
   - Lógica de campos readonly

## Instalação/Execução

### Opção 1: Migrations Automáticas (Se banco vazio)
```bash
php artisan migrate
```

### Opção 2: Script Manual (Banco já populado) ⚠️ RECOMENDADO
1. Abrir `database/migrations/ADD_VAGAS_MANUAL.sql`
2. Executar no phpMyAdmin ou via linha de comando MySQL
3. Marcar migrations como executadas:
```bash
php artisan migrate:status
# Se necessário, inserir manualmente na tabela migrations
```

## Fluxo de Uso

### Para Empresas
1. Acessar dashboard
2. Navegar para "Vagas"
3. Cadastrar nova vaga com todos os dados
4. Número da vaga gerado automaticamente (ex: 2025-001)
5. Editar ou excluir vagas não vinculadas

### Para Admin/Operador
1. Visualizar todas as vagas (todas as empresas)
2. Ao cadastrar novo termo:
   - Selecionar empresa
   - Campo "Vincular à Vaga" aparece se houver vagas disponíveis
   - Selecionar vaga ou deixar em branco para preencher manualmente
   - Se vaga selecionada: campos preenchidos automaticamente
   - Preencher apenas estagiário, escola e supervisor
3. Salvar termo: vaga automaticamente marcada como "preenchida"
4. Ao excluir termo vinculado: vaga volta para "disponível"

## Validações de Segurança
- ✅ Empresa só acessa suas vagas
- ✅ Não permite editar vaga vinculada
- ✅ Não permite excluir vaga vinculada
- ✅ Valida propriedade do local à empresa
- ✅ Valida datas (início < término, não no passado)
- ✅ Alerta vagas expiradas
- ✅ FK validadas no backend

## Próximas Melhorias (Opcional)
- Dashboard de estatísticas de vagas por empresa
- Notificação quando vaga expirar automaticamente
- Histórico de vinculações de uma vaga
- Exportação de relatório de vagas
- Filtros avançados (por período, por status, por local)

## Notas Técnicas
- **Padrões do projeto mantidos**: prefixo `tb_`, PK `id_[singular]`, FK `fk_id_[singular]`
- **Relacionamentos Eloquent**: Todos configurados corretamente
- **Locale pt-BR**: Formatações e mensagens em português
- **Middlewares**: Sempre combinando `auth` + `nivel:` conforme padrão
- **Campos *_fixo**: Replicados para manter padrão de auditoria do sistema
