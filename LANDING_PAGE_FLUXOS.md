# 🔄 Fluxo de Navegação - Landing Page Pública

## Diagrama Visual do Sistema

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           NOVA ARQUITETURA PÚBLICA                       │
└─────────────────────────────────────────────────────────────────────────┘

                            ┌─────────────────────┐
                            │  Qualquer Usuário   │
                            │  (Não-logado)       │
                            └──────────┬──────────┘
                                       │
                                       ↓
                            ┌─────────────────────┐
                       ┌────→│   Landing Page      │────→┐
                       │    │    (/)              │     │
                       │    └──────┬──────────────┘     │
                       │           │                     │
                       │        Hero + Stats             │
                       │        6 Processos              │
                       │        CTA Buttons              │
                       │                                 │
              ┌────────┴──────────────────────────┐     │
              │                                   │     │
              ↓                                   ↓     │
    ┌─────────────────────┐        ┌────────────────────┐
    │ "Ver Processos"     │        │ "Entrar"/"Cadastro"│
    │ Rota: /processos    │        │ Route: /login      │
    │ -publicos           │        │ Route: /novo-etc   │
    └─────────┬───────────┘        └────────┬───────────┘
              │                             │
              ↓                             ↓
    ┌─────────────────────────────────────────────┐
    │    Listagem de Processos                    │
    │    (/processos-publicos)                    │
    │                                             │
    │    • Barra de Busca (filtro)                │
    │    • Grid de Cards                          │
    │    • Link "Ver Detalhes" para cada um       │
    │                                             │
    │    [Busca por: Empresa/Título/Número]      │
    └─────────────┬───────────────────────────────┘
                  │
                  ↓ (Clica em "Ver Detalhes")
    ┌─────────────────────────────────────────────┐
    │    Detalhes do Processo                     │
    │    (/processos/{id}/detalhes-publico)       │
    │                                             │
    │    Coluna Principal:                        │
    │    • Hero Section (ícone, título)           │
    │    • Descrição completa                     │
    │    • Requisitos                             │
    │    • Benefícios                             │
    │    • Cursos de Destino                      │
    │    • Download de Edital                     │
    │                                             │
    │    Sidebar Direita (Sticky):                │
    │    • Informações resumidas                  │
    │    • Logo da Empresa                        │
    │    • Prazo de inscrição                     │
    │    ┌──────────────────────┐                 │
    │    │  BOTÕES (Dinâmicos)  │                 │
    │    └──────────┬───────────┘                 │
    └─────────────┬┴───────────────────────────────┘
                  │
        ┌─────────┴──────────┬──────────┐
        │                    │          │
        ↓                    ↓          ↓
    (Logado?)        (Não-Logado)  (Outros)
        │                  │           │
        ├─→ Sim           │           │
        │    ├─→ Estagiário│           │
        │    │   │                    │
        │    │   ├─ Já inscrito       │
        │    │   │  "✓ Já inscrito"   │
        │    │   │                    │
        │    │   ├─ Não inscrito       │
        │    │   │  "Inscrever-me"    │
        │    │   │   ↓ (Click)         │
        │    │   │   Modal Confirma    │
        │    │   │   ↓ (Confirma)      │
        │    │   │   POST /inscrever   │
        │    │   │   ✅ Sucesso!       │
        │    │   │   Botão muda        │
        │    │   │                    │
        │    │   └─ Não é estagiário  │
        │    │      "Apenas estag..."  │
        │    │                         │
        │    └─→ Não (Auth::guest)     │
        │        "Entrar para         │
        │        Inscrever"           │
        │        ↓ POST /inscrever    │
        │        ❌ Não autorizado!   │
        │        Redireciona: /login  │
        │        ↓ (Login)             │
        │        ✅ Volta aos          │
        │        detalhes             │
        │        Agora pode inscrever  │
        │                             │
        └─────────────────────────────┘
```

---

## Fluxo Detalhado: Inscrição (Caso Crítico)

```
Usuário Não-Logado Tenta Inscrever
│
├─ Navegador: POST /processos-seletivos/{id}/inscrever
│
├─ Laravel ProcessoSeletivoPublicoController::inscrever()
│
├─ Check: Auth::check() ?
│  │
│  ├─ NÃO → Redireciona para /login
│  │  └─ Parâmetro: ?redirect=/processos/...
│  │
│  └─ SIM → Continua
│     │
│     ├─ Check: user->nivel === 'estagiario' ?
│     │  │
│     │  ├─ NÃO → JSON Error 403
│     │  │
│     │  └─ SIM → Continua
│     │     │
│     │     ├─ Check: Período de inscrições aberto ?
│     │     │  │
│     │     │  ├─ NÃO → JSON Error 422
│     │     │  │
│     │     │  └─ SIM → Continua
│     │     │     │
│     │     │     ├─ Check: Já inscrito ?
│     │     │     │  │
│     │     │     │  ├─ SIM → JSON Error 422
│     │     │     │  │
│     │     │     │  └─ NÃO → Continua
│     │     │     │     │
│     │     │     │     ├─ InscricaoProcesso::create()
│     │     │     │     │
│     │     │     │     └─ JSON Success ✅
│     │     │     │        Mensagem: "Inscrição realizada!"
│     │     │     │
│     │     │     └─ View atualiza via JavaScript
│     │     │        Botão muda para "Já inscrito"
│     │     │        Toast/Alert mostra sucesso
│
└─ Fim
```

---

## Rotas Públicas vs Privadas

```
┌─────────────────────────────────────────────────────────────────┐
│                        SISTEMA DE ROTAS                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  PÚBLICAS (Sem middleware 'auth'):                             │
│  ✅ GET  /                                                      │
│  ✅ GET  /processos-publicos                                    │
│  ✅ GET  /processos-seletivos/{id}/detalhes-publico            │
│  ✅ GET  /processos-seletivos/{id}/detalhes-publico?search=... │
│  ✅ GET  /processos-seletivos/arquivos/{id}/download           │
│                                                                 │
│  SEMI-PÚBLICAS (POST requer auth):                             │
│  🔓 POST /processos-seletivos/{id}/inscrever                   │
│     └─ Redireciona não-autenticados para /login                │
│                                                                 │
│  PRIVADAS (Requer 'auth' + 'nivel:estagiario'):               │
│  🔐 GET  /meus-processos                                        │
│  🔐 GET  /minhas-inscricoes                                     │
│  🔐 GET  /logout                                                │
│                                                                 │
│  PRIVADAS ADMIN/OPERADOR:                                      │
│  🔐 GET/POST  /processos-seletivos/* (CRUD)                    │
│  🔐 GET  /processos-seletivos/inscricoes                       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Estados da Interface (Detalhes do Processo)

```
┌─────────────────────────────────────────────────────────────────┐
│           BOTÕES/CTA - Estados Dinâmicos                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  CASO 1: Não-Autenticado                                       │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ [🔓 Entrar para Inscrever]  [👤 Criar Conta]           │   │
│  │ route('login')              route('novo-estag-ajax')    │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  CASO 2: Autenticado + Estagiário + Não Inscrito              │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ [✍️ Inscrever-me]                                        │   │
│  │ Abre Modal de Confirmação                               │   │
│  │ POST /processos-seletivos/{id}/inscrever               │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  CASO 3: Autenticado + Estagiário + Já Inscrito               │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ✅ Você já está inscrito neste processo                 │   │
│  │ (Badge de sucesso, botão desabilitado)                 │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  CASO 4: Autenticado + Não é Estagiário                       │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ℹ️ Apenas estagiários podem se inscrever               │   │
│  │ (Mensagem informativa)                                  │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│  CASO 5: Inscrições Encerradas                                │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ ❌ Inscrições encerradas                               │   │
│  │ (Badge de alerta)                                       │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Fluxo Comum: Login → Inscrição

```
1. NAVEGAÇÃO
   Landing / Processes → Ver Detalhes
   
2. VISUALIZAÇÃO (Não-Logado)
   Vê: Descrição + Botões "Entrar" + "Cadastro"
   
3. AÇÃO 1: Clica "Entrar para Inscrever"
   POST /processos-seletivos/{id}/inscrever
   
4. INTERCEPTAÇÃO
   Controller::inscrever() detecta Auth::guest()
   Redireciona: redirect('login')
   
5. AUTENTICAÇÃO
   /login (formulário tradicional)
   Faz login com email/senha
   
6. REDIRECIONAMENTO
   Após sucesso, volta para detalhes do processo
   (Via parâmetro ?redirect na URL)
   
7. REVALIDAÇÃO
   @auth agora é verdadeiro
   Botão muda de "Entrar" para "Inscrever-me"
   
8. INSCRIÇÃO
   Clica "Inscrever-me"
   Modal de confirmação
   POST /processos-seletivos/{id}/inscrever
   
9. CRIAÇÃO
   InscricaoProcesso::create() executa
   BD: INSERT into tb_inscricoes_processos
   
10. SUCESSO
    JSON retorna: { success: true, message: "..." }
    UI atualiza: Botão vira "Já inscrito"
    Toast: "Inscrição realizada com sucesso!"
```

---

## Otimizações Implementadas

```
✅ QUERY OPTIMIZATION
   - ProcessoSeletivo::with(['empresa'])
     └─ Evita N+1 queries
   
   - Limit 6 na landing
     └─ Performance melhor
   
   - where('status', '!=', 'rascunho')
     └─ Segurança + Filtro automático

✅ VIEW CACHING
   - Blade compila para PHP
   - Storage/framework/views/ armazena
   - Reutilizado em requisições subsequentes

✅ DATABASE INDEXES
   - tb_processos_seletivos.status
   - tb_processos_seletivos.data_abertura
   - tb_inscricoes_processos.fk_id_processo

✅ LAZY LOADING (Sidebar Sticky)
   - position: sticky apenas em >lg screens
   - Evita JavaScript desnecessário
   - CSS puro para melhor performance

✅ ASSET VERSIONING
   - Mix/Vite injetar hash em assets
   - Cache browser inteligente
   - CACHE_NAME em service-worker.js
```

---

## Fluxos Alternativos (Edge Cases)

```
┌─────────────────────────────────────────────────────────────────┐
│  EDGE CASE 1: Inscrições Encerradas                             │
├─────────────────────────────────────────────────────────────────┤
│  • Usuário logado vê: Badge "Inscrições encerradas"            │
│  • Botão desabilitado                                           │
│  • POST /inscrever retorna: Error 422 (validação)               │
│  • Mensagem: "Período de inscrições encerrado"                 │
│                                                                 │
│  EDGE CASE 2: Processo Deletado                                 │
├─────────────────────────────────────────────────────────────────┤
│  • /processos/{id}/detalhes-publico retorna: 404                │
│  • Laravel redireciona para /404                                │
│  • Mensagem: "Processo não encontrado"                         │
│                                                                 │
│  EDGE CASE 3: Usuário Tenta Inscrever 2x (Rapidamente)         │
├─────────────────────────────────────────────────────────────────┤
│  • Primeira requisição: ✅ Sucesso                              │
│  • Segunda requisição: ❌ Error "Já inscrito"                   │
│  • Prevenção de duplicação via CHECK constraint                │
│                                                                 │
│  EDGE CASE 4: Session Expirou (Estava Logado)                   │
├─────────────────────────────────────────────────────────────────┤
│  • POST /inscrever: Auth::check() retorna false                │
│  • Redireciona: /login com ?redirect=...                        │
│  • Usuário faz login novamente                                  │
│  • Volta ao ponto anterior                                      │
│                                                                 │
│  EDGE CASE 5: Erro no BD (Constraint Violation)                 │
├─────────────────────────────────────────────────────────────────┤
│  • InscricaoProcesso::create() falha                            │
│  • Exception capturada: (poderia implementar)                   │
│  • Retorna: Error JSON com mensagem útil                        │
│  • Log: storage/logs/laravel.log                                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Integração com PWA (Offline-First)

```
┌──────────────────────────────────────────────────────────────────┐
│             ESTRATÉGIA DE CACHE (service-worker.js)              │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│  HTML (Network-First):                                           │
│  ├─ /                      → Network primeiro                    │
│  ├─ /processos-publicos    → Network primeiro                    │
│  ├─ /processos/.../detalhes-publico → Network primeiro           │
│  └─ Fallback: offline.html se falhar                             │
│                                                                  │
│  Assets (Cache-First):                                           │
│  ├─ /css/**               → Cache primeiro                       │
│  ├─ /js/**                → Cache primeiro                       │
│  ├─ /images/**            → Cache primeiro                       │
│  ├─ /fonts/**             → Cache primeiro                       │
│  └─ Atualiza background                                          │
│                                                                  │
│  API (Network-Only):                                             │
│  ├─ POST /processos-seletivos/{id}/inscrever → Sempre online    │
│  └─ GET /processos-seletivos/arquivos/.../download → Sempre online
│                                                                  │
│  RESULTADO:                                                      │
│  ✅ Usuário vê landing offline                                   │
│  ✅ Usuário vê processos cached                                  │
│  ✅ ❌ Não consegue inscrever offline (esperado)                 │
│  ✅ ❌ Não consegue fazer download offline (esperado)            │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

---

## Métricas de Sucesso

```
✅ Acesso Público
   • Landing carrega sem autenticação
   • Temporesposta < 500ms
   • Sem erros 401/403

✅ Engajamento
   • Usuários conseguem navegar processos
   • Botões funcionam
   • Buscas filtram corretamente

✅ Conversão
   • Clique em "Entrar" redireciona para login
   • Depois do login, consegue voltar
   • Inscrição funciona

✅ Segurança
   • Processos em rascunho não aparecem
   • Apenas estagiários se inscrevem
   • Sem duplicações

✅ Performance
   • Landing: < 500ms
   • Lista: < 1s
   • Detalhes: < 1s
   • Buscas: instantânea (GET)

✅ UX/UI
   • Responsivo mobile/tablet/desktop
   • Botões claros e acessíveis
   • Mensagens de erro úteis
   • Feedback imediato (toasts)
```

---

Essa é a visão geral do novo sistema público! 🎉
