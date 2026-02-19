# Acesso de Unidades Concedentes aos Processos Seletivos

## 📋 Visão Geral

Este documento descreve a implementação do sistema de acesso para **Unidades Concedentes (Empresas)** aos processos seletivos e seus inscritos, com permissões configuráveis pelo administrador do sistema.

## 🎯 Funcionalidades Implementadas

### 1. Acesso à Lista de Processos Seletivos

As unidades concedentes agora podem:
- ✅ Visualizar a lista de processos seletivos vinculados à sua empresa
- ✅ Ver informações resumidas de cada processo (título, status, número de inscrições, etc.)
- ✅ Acessar dois botões de ação principais:
  - 🔗 **Visualizar Edital/Processo**: Abre a página pública do processo em nova guia
  - 👥 **Ver Inscritos**: Acessa a lista de candidatos inscritos (se permitido)

### 2. Visualização de Inscritos

Quando habilitado, as empresas podem:
- ✅ Ver a lista completa de inscritos (ou apenas deferidos, conforme configuração)
- ✅ Acessar informações dos candidatos:
  - Número de inscrição
  - Nome completo
  - E-mail e telefone
  - Curso e instituição
  - Status da inscrição
  - Anexos enviados (se houver)
  - Data da inscrição
- ❌ **Não podem** alterar o status das inscrições (apenas Admin/Operador)

### 3. Exportação de Relatórios

Se permitido pelo administrador, as empresas podem:
- ✅ Exportar lista de inscritos em **PDF** ou **Excel**
- ✅ Filtrar por status (Todos, Deferidos, Indeferidos, Inscritos)
- ✅ Selecionar colunas específicas para exportação
- ⚠️ Exportações respeitam as restrições de visualização configuradas

## ⚙️ Configurações do Sistema

O administrador pode gerenciar as permissões em **Sistema → Configurações → Aba "Processos Seletivos"**.

### Configurações Disponíveis

#### 1️⃣ Permitir visualização de inscritos
- **Chave**: `processos_empresa_pode_ver_inscritos`
- **Tipo**: Boolean
- **Padrão**: ✅ Ativo
- **Descrição**: Habilita/desabilita o acesso das empresas à lista de inscritos

#### 2️⃣ Restringir apenas para deferidos
- **Chave**: `processos_empresa_apenas_deferidos`
- **Tipo**: Boolean
- **Padrão**: ❌ Inativo
- **Descrição**: Quando ativo, empresas visualizam apenas candidatos com status "Deferido"

#### 3️⃣ Permitir exportação de relatórios
- **Chave**: `processos_empresa_pode_exportar`
- **Tipo**: Boolean
- **Padrão**: ✅ Ativo
- **Descrição**: Permite que empresas gerem e baixem relatórios em PDF/Excel

## 🔐 Segurança e Restrições

### Controles Implementados

1. **Isolamento de Dados**
   - Empresas visualizam **apenas** processos vinculados à sua própria unidade
   - Tentativas de acesso a processos de outras empresas são bloqueadas

2. **Permissões de Alteração**
   - Empresas **nunca** podem alterar status de inscrições
   - Botões de ação (Deferir/Indeferir/Reverter) são ocultos para empresas
   - Apenas Admin e Operador mantêm permissão de alteração

3. **Controle de Exportação**
   - Verificação de permissão antes de gerar relatórios
   - Filtros de status forçados conforme configuração
   - Empresas sem permissão veem mensagem informativa

4. **Validações no Backend**
   - Todas as permissões são verificadas no controller
   - Não há apenas ocultação visual - há validação real de autorização

## 📂 Arquivos Modificados

### Controllers
- **`ProcessoSeletivoController.php`**
  - `listarInscricoes()`: Adiciona verificações de permissão para empresas
  - `atualizarStatusInscricao()`: Bloqueia alterações por empresas
  - `exportarInscricoes()`: Valida permissões de exportação

- **`ConfiguracaoController.php`**
  - `update()`: Processa novas configurações de processos seletivos

### Views
- **`processos-seletivos/index.blade.php`**
  - Botões de ação adaptados conforme nível do usuário
  - Mensagem informativa para empresas

- **`processos-seletivos/inscricoes.blade.php`**
  - Coluna de ações oculta para empresas
  - Botão de exportar condicionado à permissão
  - Alert informativo sobre limitações

- **`configuracoes/index.blade.php`**
  - Nova aba "Processos Seletivos" com 3 configurações
  - Interface intuitiva com checkboxes e descrições

### Models
- **`Configuracao.php`**: Sem alterações (já suportava as novas configurações)

### Seeders
- **`ProcessoSeletivoConfigSeeder.php`** (NOVO)
  - Cria as 3 configurações iniciais com valores padrão

## 🚀 Como Usar

### Para Administradores

1. Acesse **Sistema → Configurações**
2. Clique na aba **"Processos Seletivos"**
3. Configure as permissões conforme necessário:
   - ✅ Marque para permitir
   - ❌ Desmarque para bloquear
4. Clique em **"Salvar Configurações"**
5. As mudanças entram em vigor **imediatamente**

### Para Unidades Concedentes (Empresas)

1. Acesse o menu **"Processos Seletivos"**
2. Visualize os processos vinculados à sua empresa
3. Clique em:
   - 🔗 **Ícone de link externo**: Ver edital público
   - 👥 **Ícone de usuários**: Ver inscritos (se permitido)
4. Na lista de inscritos:
   - Visualize informações dos candidatos
   - Use o botão **"Exportar"** para gerar relatórios (se permitido)
   - Filtre por status e selecione colunas desejadas

## 🧪 Testando a Implementação

### 1. Executar o Seeder
```bash
php artisan db:seed --class=ProcessoSeletivoConfigSeeder
```

### 2. Acessar como Admin
- Ir em **Configurações → Processos Seletivos**
- Verificar se as 3 configurações estão disponíveis
- Testar ativar/desativar cada opção

### 3. Acessar como Empresa
- Login com usuário de nível `empresa`
- Ir em **Processos Seletivos**
- Verificar que:
  - ✅ Aparecem apenas processos da empresa
  - ✅ Botões corretos são exibidos (link externo + ver inscritos)
  - ❌ Não aparecem botões de editar, deletar, resultados

### 4. Testar Visualização de Inscritos
- Com empresa, clicar em **"Ver Inscritos"**
- Verificar que:
  - ✅ Lista de inscritos é exibida corretamente
  - ✅ Alert informativo aparece no topo
  - ❌ Coluna de ações não aparece
  - ❌ Não há botões para alterar status

### 5. Testar Restrições
- No admin, ativar **"Apenas Deferidos"**
- Com empresa, verificar que:
  - ✅ Só aparecem candidatos deferidos
  - ✅ Exportação força filtro de deferidos

- No admin, desativar **"Pode Exportar"**
- Com empresa, verificar que:
  - ❌ Botão de exportar não aparece
  - ✅ Mensagem indicando restrição é exibida

## 📊 Fluxo de Permissões

```
┌─────────────────────────────────────────────┐
│  Admin: Configurações → Processos Seletivos │
│  ├─ Pode ver inscritos?                    │
│  ├─ Apenas deferidos?                       │
│  └─ Pode exportar?                          │
└─────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────┐
│      Empresa: Lista de Processos            │
│  ├─ Ver apenas processos da empresa         │
│  ├─ Botão: Ver Edital (Público)            │
│  └─ Botão: Ver Inscritos (se permitido)    │
└─────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────┐
│      Empresa: Visualizar Inscritos          │
│  ├─ Lista filtrada (todos ou deferidos)     │
│  ├─ Sem botões de alterar status            │
│  └─ Botão Exportar (se permitido)          │
└─────────────────────────────────────────────┘
```

## 🆕 O Que Mudou

### Antes
- ❌ Empresas não tinham acesso aos inscritos dos processos
- ❌ Apenas Admin/Operador podiam visualizar candidatos
- ❌ Empresas não conseguiam exportar relatórios

### Depois
- ✅ Empresas podem visualizar inscritos (configurável)
- ✅ Restrição seletiva por status (todos ou apenas deferidos)
- ✅ Exportação de relatórios para empresas (configurável)
- ✅ Segurança mantida (empresas não alteram status)
- ✅ Interface adaptada conforme nível do usuário

## 🔧 Manutenção

### Adicionar Nova Configuração

1. Criar entrada no seeder:
```php
Configuracao::updateOrCreate(
    ['chave' => 'processos_nova_config'],
    [
        'valor' => '1',
        'descricao' => 'Descrição da nova configuração',
        'tipo' => 'boolean',
    ]
);
```

2. Adicionar na view de configurações (`configuracoes/index.blade.php`)
3. Adicionar validação no controller (`ConfiguracaoController::update()`)
4. Usar no código: `\App\Models\Configuracao::obter('processos_nova_config', true)`

### Troubleshooting

**Empresa não vê botão de ver inscritos**
- ✅ Verificar se configuração está ativa: `processos_empresa_pode_ver_inscritos`
- ✅ Verificar se o processo pertence à empresa (`fk_id_empresa`)

**Empresa consegue acessar processo de outra empresa**
- ❌ Bug de segurança - verificar validações no controller
- ✅ Deve retornar erro de permissão

**Exportação não respeita filtro de deferidos**
- ✅ Verificar lógica em `exportarInscricoes()` do controller

## 📝 Notas Adicionais

- As configurações são persistidas no banco de dados (tabela `configuracoes`)
- Alterações entram em vigor imediatamente (sem necessidade de cache clear)
- Sistema mantém compatibilidade com Admin/Operador (sem mudanças no fluxo existente)
- Mensagens são exibidas em português (pt-BR)

---

**Documentação criada em**: 19/02/2026  
**Versão do Laravel**: 11.x  
**Módulo**: Processos Seletivos - Acesso Empresas
