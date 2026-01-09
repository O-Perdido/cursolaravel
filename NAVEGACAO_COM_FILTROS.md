# Sistema de Navegação com Preservação de Filtros

## 📋 Visão Geral

Este documento descreve a implementação do novo sistema de navegação "Voltar" que **preserva o estado da página anterior**, incluindo **filtros, paginação e scroll**, em vez de simplesmente recarregar a listagem zerada.

---

## 🎯 Problema Resolvido

**Antes:**
- Usuário estava na listagem de Termos com filtros aplicados
- Clicava em um termo para ver detalhes
- Ao clicar em "Voltar", voltava para `/termos` **sem os filtros**
- Precisava reaplicar todos os filtros manualmente

**Agora:**
- Clica em "Voltar"
- Volta exatamente para a URL anterior **com todos os filtros e parâmetros intactos**
- Mantém o scroll na mesma posição (browser cache)

---

## 🔧 Como Funciona Tecnicamente

### 1. **Utilitário JavaScript: `NavigationHistory`**

Localização: [resources/js/navigation.js](resources/js/navigation.js)

```javascript
NavigationHistory.saveCurrentUrl()      // Salva URL atual no sessionStorage
NavigationHistory.getPreviousUrl()      // Recupera URL anterior
NavigationHistory.goBack(fallbackRoute) // Volta para URL anterior ou fallback
NavigationHistory.clear()               // Limpa o histórico
```

**Como funciona:**
- Toda página de **listagem** salva automaticamente sua URL (com filtros) ao carregar
- Quando o usuário clica em um link para **detalhes/edição**, a URL da listagem está salva
- Ao clicar em "Voltar", o código recupera essa URL e redireciona para ela
- Se não houver URL salva, usa uma **fallback route** (ex: `termos.index`)

### 2. **Automatização**

O arquivo [resources/js/navigation.js](resources/js/navigation.js) detecta automaticamente:
- Se você está em uma página de listagem → **salva a URL**
- Se você está em uma página de detalhes → **não faz nada** (apenas está pronta para voltar)

Não precisa de nenhuma configuração manual em cada blade!

---

## 📝 Implementação nos Templates

### Exemplo 1: Termos (Show)

**Antes:**
```blade
<a href="{{ route('termos.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Voltar
</a>
```

**Depois:**
```blade
<button onclick="window.NavigationHistory?.goBack('{{ route('termos.index') }}')" 
        class="btn btn-secondary" 
        title="Voltar para a página anterior com filtros preservados">
    <i class="fas fa-arrow-left"></i> Voltar
</button>
```

**O que mudou:**
- `<a href>` → `<button onclick>`
- `onclick="window.NavigationHistory?.goBack(fallbackRoute)"`
- O `fallbackRoute` é opcional (se não existir URL anterior, usa esse)

### Exemplo 2: Empresas (Create)

```blade
<button onclick="window.NavigationHistory?.goBack('{{ route('empresas.index') }}')" 
        class="btn btn-secondary mb-3" 
        title="Voltar para a página anterior com filtros preservados">
    Voltar
</button>
```

---

## 📦 Arquivos Modificados

### Templates (Blade)

#### Show (Detalhes)
- ✅ [termos/show.blade.php](resources/views/termos/show.blade.php)
- ✅ [folhas_pagamento/show.blade.php](resources/views/folhas_pagamento/show.blade.php)
- ✅ [escolas/show.blade.php](resources/views/escolas/show.blade.php)
- ✅ [empresas/show.blade.php](resources/views/empresas/show.blade.php)
- ✅ [estagiario/show.blade.php](resources/views/estagiario/show.blade.php)
- ✅ [supervisores/show.blade.php](resources/views/supervisores/show.blade.php)
- ✅ [chamados/show.blade.php](resources/views/chamados/show.blade.php)

#### Create (Novo Registro)
- ✅ [termos/create.blade.php](resources/views/termos/create.blade.php)
- ✅ [escolas/create.blade.php](resources/views/escolas/create.blade.php)
- ✅ [empresas/create.blade.php](resources/views/empresas/create.blade.php)
- ✅ [vagas/create.blade.php](resources/views/vagas/create.blade.php)
- ✅ [admin/tipos-chamados/create.blade.php](resources/views/admin/tipos-chamados/create.blade.php)
- ✅ [termos/alteracoes/create.blade.php](resources/views/termos/alteracoes/create.blade.php)

#### Edit (Editar Registro)
- ✅ [escolas/edit.blade.php](resources/views/escolas/edit.blade.php)
- ✅ [empresas/edit.blade.php](resources/views/empresas/edit.blade.php)
- ✅ [folhas_pagamento/edit.blade.php](resources/views/folhas_pagamento/edit.blade.php)
- ✅ [vagas/edit.blade.php](resources/views/vagas/edit.blade.php)
- ✅ [admin/tipos-chamados/edit.blade.php](resources/views/admin/tipos-chamados/edit.blade.php)

### JavaScript

- ✅ [resources/js/navigation.js](resources/js/navigation.js) - **Novo arquivo criado**
- ✅ [resources/js/app.js](resources/js/app.js) - Import adicionado

---

## 🧪 Como Testar

### Teste Manual: Termos com Filtros

1. **Acesse a listagem:** `http://localhost:8000/termos`

2. **Aplique alguns filtros:**
   - Status: "Ativo"
   - Empresa: "Qualquer uma"
   - Data início: após 01/01/2025
   - Clique em "Filtrar"

3. **Verifique a URL:** deve ter parametros como:
   ```
   ?status=ativo&empresa=1&data_inicio=2025-01-01&page=1
   ```

4. **Clique em um termo** para abrir os detalhes

5. **Clique no botão "Voltar"**

6. **Resultado esperado:**
   - Volta para `/termos` **COM TODOS OS FILTROS APLICADOS**
   - Mantém a mesma página (se estava na página 2, volta na página 2)
   - Scroll mantém a posição aproximada

### Teste Manual: Empresas sem Filtros

1. Acesse a listagem de empresas
2. Abra uma empresa (show)
3. Clique em "Voltar"
4. Deve voltar para a listagem de empresas (como fallback)

### Teste de Fallback

Se a URL anterior não existir no `sessionStorage`:
- O navegador redireciona para a rota fallback
- Ex: `route('termos.index')` se configurado no onclick

---

## 🔒 Segurança e Performance

### Considerações

1. **SessionStorage vs LocalStorage**
   - Usamos `sessionStorage` (não `localStorage`)
   - Dados limpos quando a aba é fechada
   - Não persiste entre navegadores/abas

2. **Validação de URL**
   - O sistema valida que a URL anterior é válida
   - Fallback garante que sempre haverá uma rota segura

3. **Performance**
   - Sem chamadas AJAX extras
   - Apenas redirecionamento HTTP nativo
   - Sem impacto no servidor

---

## 🚀 Fluxo de Dados

```
┌─────────────────────────────────────────────────────────┐
│ 1. Usuário acessa /termos?status=ativo&page=2          │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 2. JavaScript detecta: "não é página de detalhes"      │
│    → Executa: NavigationHistory.saveCurrentUrl()       │
│    → sessionStorage['lastNavigationUrl'] = URL completa│
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 3. Usuário clica em um termo                            │
│    → Navega para /termos/123                            │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 4. Usuário clica no botão "Voltar"                      │
│    → Executa: window.NavigationHistory?.goBack()       │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 5. Sistema recupera URL anterior do sessionStorage      │
│    → window.location.href = URL original                │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 6. Navegador redireciona para URL com filtros          │
│    → /termos?status=ativo&page=2                        │
└─────────────────────────────────────────────────────────┘
```

---

## 📌 Dicas de Uso

### Para Desenvolvedores

Se você criar uma **nova página de detalhes** (`show.blade.php`):

```blade
<button onclick="window.NavigationHistory?.goBack('{{ route('seumodelo.index') }}')" 
        class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Voltar
</button>
```

O utilitário fará o resto automaticamente!

### Customização Avançada

Se precisar limpar o histórico manualmente:

```javascript
// Em algum evento (ex: após salvar)
window.NavigationHistory.clear();
```

---

## 🎓 Conclusão

Agora o sistema oferece uma **experiência de navegação mais intuitiva**, onde os usuários podem:
- ✅ Voltar com todos os filtros preservados
- ✅ Manter contexto ao navegar entre páginas
- ✅ Melhorar a produtividade (sem replicar filtros)
- ✅ Sem quebra de funcionalidade (fallback sempre disponível)

---

## 📞 Suporte

Se encontrar problemas:

1. **Limpar cache do navegador** (Ctrl+Shift+Delete)
2. **Verificar console** (F12 → Console) para erros
3. **Testar em aba anônima** para descartar extensões
4. **Reportar no Slack** com print da URL e passos para reproduzir
