# Landing Page Pública - Documentação

## Visão Geral
O sistema agora possui uma página inicial pública que permite qualquer pessoa (não-autenticada) navegar pelos processos seletivos disponíveis sem ser redirecionada automaticamente para login.

## Fluxo do Usuário Não-Autenticado

### 1. Acesso à Página Inicial
- **Rota**: `/` (GET)
- **Controlador**: `ProcessoSeletivoPublicoController::landing()`
- **View**: `resources/views/landing.blade.php`

**Características:**
- Hero section com título, descrição e CTA
- Seção de estatísticas (total de processos, empresas)
- Grid com 6 processos seletivos em destaque
- Botões de "Ver Processos" e "Entrar" no topo

### 2. Listar Todos os Processos
- **Rota**: `/processos-publicos` (GET)
- **Controlador**: `ProcessoSeletivoPublicoController::listarPublicos()`
- **View**: `resources/views/processos-seletivos/publicos.blade.php`

**Características:**
- Lista completa de todos os processos (status != 'rascunho')
- Barra de busca para filtrar por empresa, título ou número
- Cards com informações de cada processo
- Botão "Ver Detalhes" para acessar informações completas
- Link para "Minhas Inscrições" (apenas se autenticado)

### 3. Visualizar Detalhes do Processo
- **Rota**: `/processos-seletivos/{id}/detalhes-publico` (GET)
- **Controlador**: `ProcessoSeletivoPublicoController::detalhesPublico()`
- **View**: `resources/views/processos-seletivos/detalhes-publico.blade.php`

**Características:**
- Hero section com ícone, título e empresa
- Descrição, requisitos e benefícios
- Lista de cursos de destino
- Download de edital/documentos
- Sidebar sticky com informações resumidas

**Comportamento conforme autenticação:**

#### Usuário Não-Autenticado
- Vê todos os detalhes do processo
- Botões de "Entrar para Inscrever" e "Criar Conta"
- Links redirecionam para:
  - Login: `route('login')` com parâmetro `redirect`
  - Cadastro: `route('novo-estagiario-ajax-create')`

#### Usuário Autenticado (Estagiário)
- Já inscrito: Mostra mensagem "Você já está inscrito"
- Não inscrito: Botão "Inscrever-me" abre modal de confirmação
- Não-estagiário: Mensagem informativa

### 4. Inscrição em um Processo
- **Rota**: `/processos-seletivos/{id}/inscrever` (POST)
- **Controlador**: `ProcessoSeletivoPublicoController::inscrever()`

**Fluxo:**
1. Se usuário não autenticado → Redireciona para login
2. Se usuário não é estagiário → Erro JSON
3. Se período de inscrições fechado → Erro JSON
4. Se já inscrito → Erro JSON
5. Caso contrário → Cria inscrição e retorna sucesso

## Arquivos Modificados/Criados

### Views Criadas
1. **`resources/views/landing.blade.php`** - Página inicial pública
   - Hero section com CTA
   - Estatísticas do sistema
   - Grid de processos em destaque
   - Seção de call-to-action para login/signup

2. **`resources/views/processos-seletivos/publicos.blade.php`** - Lista pública de processos
   - Barra de busca
   - Cards com previsualizações
   - Filtro por empresa/título/número

3. **`resources/views/processos-seletivos/detalhes-publico.blade.php`** - Detalhes público do processo
   - Informações completas
   - Sidebar com CTA conforme autenticação
   - Compartilhamento em redes sociais

### Controller
**`app/Http/Controllers/ProcessoSeletivoPublicoController.php`**
- Novo método `landing()`: Retorna página inicial
- Novo método `listarPublicos()`: Lista com search
- Novo método `detalhesPublico()`: Detalhes com verificação de inscrição
- Modificado método `inscrever()`: Agora redireciona não-autenticados para login

### Rotas
**`routes/web.php`**
Adicionadas 3 rotas públicas fora do middleware autenticado:
```php
Route::get('/', [ProcessoSeletivoPublicoController::class, 'landing'])->name('landing');
Route::get('/processos-publicos', [ProcessoSeletivoPublicoController::class, 'listarPublicos'])->name('processos-seletivos.publicos');
Route::get('/processos-seletivos/{id}/detalhes-publico', [ProcessoSeletivoPublicoController::class, 'detalhesPublico'])->name('processos-seletivos.detalhes.publico');
```

## Rotas Conexas

### Autenticação
- **Login**: `route('login')`
- **Cadastro Estagiário**: `route('novo-estagiario-ajax-create')`
- **Minhas Inscrições**: `route('processos-seletivos.minhas-inscricoes')` (requer auth)

### Download de Arquivos
- **Edital**: `route('processos-seletivos.arquivos.download', $arquivo->id_arquivo)`

## Segurança

### Validações Implementadas
1. ✅ Processos com status 'rascunho' não aparecem publicamente
2. ✅ Apenas estagiários podem se inscrever
3. ✅ Período de inscrições validado antes de criar inscrição
4. ✅ Duplicação de inscrição prevenida
5. ✅ Download de arquivos verifica permissões

### Redirecionamentos
- Não-autenticado tenta inscrever → Redireciona para login com redirect_to
- Não-estagiário tenta inscrever → Erro JSON 403

## Testes Recomendados

### Teste 1: Fluxo Completo Não-Autenticado
1. Acessa `/` (landing page)
2. Clica "Ver Processos"
3. Busca um processo
4. Clica "Ver Detalhes"
5. Tenta "Entrar para Inscrever"
6. ✅ Sistema redireciona para login

### Teste 2: Login e Inscrição
1. De onde parou, faz login
2. ✅ Redireciona de volta para detalhes do processo
3. Clica "Inscrever-me"
4. ✅ Inscrição realizada com sucesso
5. ✅ Botão muda para "Você já está inscrito"

### Teste 3: Criar Nova Conta
1. Do landing, clica "Criar Conta"
2. ✅ Abre página de cadastro de estagiário
3. Preenche e cadastra
4. ✅ Sistema permite inscrever no mesmo acesso

### Teste 4: Busca de Processos
1. Em `/processos-publicos`, digita empresa/número
2. ✅ Filtra resultados em tempo real (GET)

### Teste 5: Compartilhamento
1. Em detalhes do processo, clica ícone de rede social
2. ✅ Abre janela de compartilhamento com link correto

## Notas de Deploy

- Não requer novas migrações
- Não requer novas dependências
- Compatível com PWA (public routes funcionam offline se configuradas)
- URLs amigáveis já funcionam (sem .php)

## Atualizações

- 29/01/2026: Ajustes de responsividade do carrossel no mobile (evita sobreposição de slides e melhora legibilidade).

## Continuidade

### Melhorias Futuras
- [ ] Adicionar filtro por curso de destino
- [ ] Adicionar ordenação (mais recentes, por salário, por vagas)
- [ ] Analytics de visualizações
- [ ] Notificações quando novo processo é publicado
- [ ] Favoritar processos (para usuários logados)
- [ ] Histórico de inscrições
