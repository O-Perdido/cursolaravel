# 🧪 Guia Completo de Testes - Landing Page

## ⚡ Quick Start (2 minutos)

```bash
# 1. Limpar cache
php artisan cache:clear && php artisan view:clear

# 2. Iniciar servidor (se não estiver rodando)
php artisan serve

# 3. Abrir navegador
# http://localhost:8000
```

---

## 🧪 TESTE 1: Landing Page Básico

### Objetivo
Verificar se landing page carrega corretamente sem erros

### Passos
1. Abra `http://localhost:8000/`
2. Verifique os seguintes elementos:
   - [ ] Hero section com gradiente roxo
   - [ ] Título "Processos Seletivos"
   - [ ] Descrição "Encontre as melhores oportunidades"
   - [ ] Botão "Ver Processos" (azul)
   - [ ] Botão "Entrar" (branco com borda)
   - [ ] Seção de estatísticas com 4 cards
   - [ ] Grid de 6 processos
   - [ ] Cards com ícones
   - [ ] Botões CTA "Cadastre-se" e "Faça Login"

### Resultado Esperado
✅ Página carrega sem erros 404 ou 500  
✅ Todos os elementos aparecem corretamente  
✅ Gradientes e cores aparecem  
✅ Responsiva em mobile (abra DevTools, modo mobile)

### Checklist de Console
- [ ] DevTools → Console: sem erros vermelhos
- [ ] DevTools → Network: todos os arquivos carregaram (status 200)
- [ ] DevTools → Application → Service Worker: offline.html pode estar cached

---

## 🧪 TESTE 2: Navegação para Lista de Processos

### Objetivo
Verificar se link "Ver Processos" funciona e lista aparece

### Passos
1. Na landing page, clique em "Ver Processos" (botão azul)
   - OU acesse diretamente: `http://localhost:8000/processos-publicos`
2. Verifique:
   - [ ] URL mudou para `/processos-publicos`
   - [ ] Cabeçalho mostra "Processos Seletivos Disponíveis"
   - [ ] Botão "Voltar" aparece
   - [ ] Barra de busca aparece (com placeholder "Buscar por empresa...")
   - [ ] Grid de cards com processos
   - [ ] Cada card tem: ícone, empresa, número, título, status, datas
   - [ ] Botão "Ver Detalhes" em cada card

### Resultado Esperado
✅ Lista mostra todos os processos (status != 'rascunho')  
✅ Sem botão "Minhas Inscrições" (não está logado)  
✅ Processa sem erro

### Checklist
- [ ] Nenhum processo com status 'rascunho' aparece
- [ ] Empresas aparecem com logos corretas
- [ ] Datas em formato dd/mm/YYYY

---

## 🧪 TESTE 3: Busca e Filtro

### Objetivo
Verificar se barra de busca filtra processos

### Passos
1. Em `/processos-publicos`, na barra de busca:
   - [ ] Digite nome de uma empresa (ex: "ACME")
   - [ ] Clique "Buscar" ou pressione Enter
   - [ ] Verifique se apenas processos dessa empresa aparecem
   
2. Tente outras buscas:
   - [ ] Digite número de processo (ex: "2025")
   - [ ] Digite título parcial (ex: "desenvolvedor")
   - [ ] Deixe em branco e clique buscar
   
3. Verifique:
   - [ ] Resultados filtram corretamente
   - [ ] URL muda para `?search=termo`
   - [ ] Aceita múltiplos caracteres
   - [ ] Não é case-sensitive

### Resultado Esperado
✅ Filtro funciona para empresa, número e título  
✅ URL preserva parâmetro `?search=`  
✅ Sem resultados mostra mensagem "Nenhum processo disponível"

---

## 🧪 TESTE 4: Visualizar Detalhes (Não-Logado)

### Objetivo
Verificar página de detalhes para usuário não-autenticado

### Passos
1. Na lista de processos, clique "Ver Detalhes" em um processo
2. Verifique:
   - [ ] URL é `/processos-seletivos/{id}/detalhes-publico`
   - [ ] Hero section com ícone grande (120px)
   - [ ] Título, empresa, número do processo
   - [ ] Status badge (aberto/inscricoes/encerrado/etc)
   - [ ] Descrição completa
   - [ ] Seção "Requisitos"
   - [ ] Seção "Benefícios"
   - [ ] Seção "Cursos Disponíveis"
   - [ ] Edital/Documentos (se existir)
   - [ ] Sidebar direita (sticky)
   - [ ] Logo da empresa na sidebar
   - [ ] Informações resumidas (prazo, vagas, bolsa)
   - [ ] Botões "Entrar para Inscrever" e "Criar Conta"
   - [ ] Botões de compartilhamento (Facebook, Twitter, WhatsApp, Copiar)

### Resultado Esperado
✅ Página exibe todas as informações corretamente  
✅ Sidebar fica sticky ao fazer scroll  
✅ Botões apontam para /login e /novo-estagiario-ajax

### Comportamento Desktop vs Mobile
- [ ] **Desktop**: Sidebar à direita, conteúdo à esquerda
- [ ] **Tablet**: Sidebar ainda à direita mas mais estreita
- [ ] **Mobile**: Sidebar desce após conteúdo (col-12)

---

## 🧪 TESTE 5: Compartilhamento

### Objetivo
Verificar se botões de compartilhamento funcionam

### Passos
1. No detalhes do processo, na sidebar:
2. Clique em ícone Facebook
   - [ ] Abre nova aba com compartilhador do Facebook
   - [ ] URL do processo está na mensagem
   
3. Clique em ícone Twitter
   - [ ] Abre nova aba com tweet pré-preenchido
   - [ ] Título do processo aparece no tweet
   
4. Clique em ícone WhatsApp
   - [ ] Abre WhatsApp (web ou app)
   - [ ] Mensagem pré-preenchida com título + link
   
5. Clique em ícone Copiar
   - [ ] Link é copiado para clipboard
   - [ ] Você pode colar em email/mensagem

### Resultado Esperado
✅ Todos os 4 botões funcionam  
✅ Links estão corretos  
✅ Novas abas abrem (não na mesma aba)

---

## 🧪 TESTE 6: Download de Edital (Não-Logado)

### Objetivo
Verificar se download de documentos funciona sem login

### Passos
1. No detalhes do processo, procure por seção "Documentos"
2. Se houver arquivo:
   - [ ] Clique no link para download
   - [ ] Arquivo baixa corretamente
   - [ ] Tipo de arquivo é correto (PDF, DOCX, etc)
   - [ ] Nome do arquivo aparece corretamente
3. Se NÃO houver arquivo:
   - [ ] Seção "Documentos" não aparece (esperado)

### Resultado Esperado
✅ Download funciona sem autenticação  
✅ Arquivo tem conteúdo válido  
✅ Nome do arquivo é legível

---

## 🧪 TESTE 7: Tentativa de Inscrição (Não-Logado)

### Objetivo
Verificar se tentar inscrever sem login redireciona para login

### Passos
1. No detalhes do processo (não-logado):
2. Clique em "Entrar para Inscrever"
3. Verifique:
   - [ ] Redireciona para `/login`
   - [ ] Página de login mostra
   - [ ] Formulário de login pronto

### Resultado Esperado
✅ Redireciona para /login sem erro  
✅ URL muda para /login  
✅ Formulário de login disponível

---

## 🧪 TESTE 8: Login e Redirect de Volta

### Objetivo
Verificar se após login, volta para detalhes do processo

### Passos
1. Ainda na página de login (vindo do teste anterior)
2. Faça login com uma conta estagiário:
   - Email: (sua conta de teste)
   - Senha: (sua senha)
3. Clique "Entrar"
4. Verifique:
   - [ ] Sistema processa login
   - [ ] Redireciona para DETALHES DO PROCESSO (não para dashboard)
   - [ ] URL volta para `/processos-seletivos/{id}/detalhes-publico`
   - [ ] Agora mostra botão "Inscrever-me" (em vez de "Entrar")

### Resultado Esperado
✅ Login bem-sucedido  
✅ Redireciona de volta ao processo  
✅ Interface agora mostra botão "Inscrever-me"

---

## 🧪 TESTE 9: Inscrição (Logado)

### Objetivo
Verificar se pode inscrever após fazer login

### Passos
1. No detalhes do processo (agora logado):
2. Verifique:
   - [ ] Sidebar não tem mais botão "Entrar"
   - [ ] Sidebar mostra "Inscrever-me" OU "Já inscrito"
   
3. Se "Inscrever-me":
   - [ ] Clique no botão
   - [ ] Modal de confirmação aparece
   - [ ] Modal mostra título do processo
   - [ ] Botões "Cancelar" e "Confirmar"
   - [ ] Clique "Confirmar"
   - [ ] Página processa
   - [ ] ✅ Toast/alert de sucesso aparece
   - [ ] Botão muda para "Já inscrito"
   - [ ] Badge verde com checkmark aparece
   
4. Se "Já inscrito":
   - [ ] Significa que você já está inscrito neste processo (esperado)

### Resultado Esperado
✅ Inscrição é criada com sucesso  
✅ BD: linha criada em `tb_inscricoes_processos`  
✅ UI atualiza imediatamente  
✅ Segunda tentativa de inscrever mostra "Já inscrito"

---

## 🧪 TESTE 10: Minhas Inscrições (Logado)

### Objetivo
Verificar se link "Minhas Inscrições" mostra inscrições

### Passos
1. Volte para `/processos-publicos`
2. Verifique:
   - [ ] Agora mostra botão "Minhas Inscrições" no topo
3. Clique em "Minhas Inscrições"
4. Verifique:
   - [ ] URL é `/minhas-inscricoes`
   - [ ] Lista mostra todos os processos que você se inscreveu
   - [ ] Inclui o processo do teste anterior
   - [ ] Mostra status, datas, empresa
   - [ ] Pode voltar de lá

### Resultado Esperado
✅ Link aparece apenas para usuários logados  
✅ Mostra inscrições corretamente  
✅ Inclui a inscrição que acabou de fazer

---

## 🧪 TESTE 11: Criar Conta (Novo Usuário)

### Objetivo
Verificar se link "Criar Conta" funciona

### Passos
1. Faça logout (clique perfil → Logout ou acesse `/logout`)
2. Volte para `/processos-publicos` OU `/`
3. Clique em "Criar Conta" (se na landing) OU em detalhes clique "Criar Conta"
4. Verifique:
   - [ ] Redireciona para página de cadastro de estagiário
   - [ ] URL é `/novo-estagiario-ajax` (ou similar)
   - [ ] Formulário de cadastro aparece
   - [ ] Campos: nome, email, senha, etc

### Resultado Esperado
✅ Link redireciona para cadastro  
✅ Página de cadastro carrega corretamente  
✅ Pode se cadastrar normalmente

---

## 🧪 TESTE 12: Inscrições Encerradas

### Objetivo
Verificar comportamento quando inscrições estão encerradas

### Passos
1. Encontre um processo com:
   - Status: "encerrado" OU
   - Data de fechamento < hoje
2. Clique "Ver Detalhes"
3. Verifique:
   - [ ] Sidebar mostra badge "Inscrições encerradas"
   - [ ] Badge é vermelha/orange
   - [ ] Botão "Inscrever-me" não aparece (desabilitado)
   - [ ] Se tentar fazer POST manualmente:
     - [ ] Retorna erro JSON 422: "Período de inscrições encerrado"

### Resultado Esperado
✅ UI indica claramente que inscrições fecharam  
✅ Botão está desabilitado  
✅ Backend valida e rejeita POST

---

## 🧪 TESTE 13: Responsividade Mobile

### Objetivo
Verificar se tudo funciona bem em mobile

### Passos
1. Abra DevTools (F12)
2. Clique mode mobile (Ctrl+Shift+M)
3. Simule iPhone 12 (390x844)
4. Teste cada página:

**Landing:**
- [ ] Hero section redimensiona
- [ ] Cards em 1 coluna
- [ ] Botões CTA empilham
- [ ] Sem scroll horizontal

**Lista:**
- [ ] Barra de busca ocupa linha inteira
- [ ] Botão buscar fica abaixo em mobile
- [ ] Grid em 1 coluna
- [ ] Cards ficam legíveis

**Detalhes:**
- [ ] Conteúdo em 1 coluna
- [ ] Sidebar desce após conteúdo
- [ ] Ícone hero redimensiona
- [ ] Sidebar sticky desativa em mobile
- [ ] Botões CTA ficam grandes e clicáveis

### Resultado Esperado
✅ Zero scroll horizontal  
✅ Texto legível (min 16px)  
✅ Botões clicáveis em touch  
✅ Responsivo até 320px

---

## 🧪 TESTE 14: Performance

### Objetivo
Verificar velocidade de carregamento

### Passos
1. Abra DevTools → Lighthouse
2. Rode auditoria para cada página:
   - Landing (`/`)
   - Lista (`/processos-publicos`)
   - Detalhes (`/processos-seletivos/{id}/detalhes-publico`)

3. Verificar scores:
   - [ ] Performance: > 80
   - [ ] Accessibility: > 80
   - [ ] Best Practices: > 80
   - [ ] SEO: > 80

4. Verificar Network tab:
   - [ ] Landing: < 2 segundos
   - [ ] Lista: < 2 segundos
   - [ ] Detalhes: < 2 segundos
   - [ ] Nenhum erro 404/500

### Resultado Esperado
✅ Scores > 80 em todos os metrics  
✅ Carregamento < 2s por página  
✅ Sem bottlenecks óbvios

---

## 🧪 TESTE 15: Offline (PWA)

### Objetivo
Verificar se funciona offline via service worker

### Passos
1. Abra DevTools → Application → Service Workers
2. Marque "Offline"
3. Tente acessar:
   - [ ] Landing (`/`) - deve carregar do cache
   - [ ] Lista (`/processos-publicos`) - pode estar cacheada
   - [ ] Tente fazer inscrição - deve falhar (esperado)
4. Desmarque "Offline"
5. Atualizar página

### Resultado Esperado
✅ Landing carrega offline  
✅ Assets (CSS/JS) carregam offline  
✅ POST /inscrever falha offline (esperado)  
✅ Volta ao normal online

---

## 🧪 TESTE 16: Processo em Rascunho

### Objetivo
Verificar se processos em status 'rascunho' não aparecem publicamente

### Passos
1. Abra banco de dados (Tinker ou PHPMyAdmin)
2. Encontre um processo com `status = 'rascunho'`
3. Teste:
   - [ ] Não aparece em `/processos-publicos`
   - [ ] Não aparece em `/` (landing)
   - [ ] Se tentar acessar direto `/processos-seletivos/{id}/detalhes-publico`:
     - [ ] Pode ver (se for o processo)
     - [ ] OU pode não ver (depende da lógica)

### Resultado Esperado
✅ Processos rascunho nunca aparecem em listagens públicas  
✅ Apenas admin/operador/empresa podem ver rascunhos

---

## 🧪 TESTE 17: Erros Edge Case

### Objetivo
Testar cenários de erro

### Passos
1. **Processo não existe**
   - Acesse: `/processos-seletivos/9999999/detalhes-publico`
   - Esperado: Erro 404

2. **Busca vazia**
   - Deixe campo em branco, clique buscar
   - Esperado: Mostra todos os processos

3. **Busca com caracteres especiais**
   - Digite: `@#$%^&*()`
   - Esperado: Nenhum resultado ou todos (depende)

4. **Muito resultados**
   - Se tiver 100+ processos, busque "a"
   - Esperado: Todos aparecem (sem paginação por enquanto)

### Resultado Esperado
✅ 404 para processo não-existente  
✅ Buscas vazias funcionam  
✅ Caracteres especiais não quebram página

---

## 📊 Relatório de Testes

Copie este template:

```markdown
# Relatório de Testes - Landing Page Pública

## Informações
- Data: [data]
- Testador: [nome]
- Navegador: [Chrome/Firefox/Safari]
- Versão: [versão do browser]

## Testes Executados

| # | Teste | Resultado | Notas |
|----|-------|-----------|-------|
| 1 | Landing Page Básico | ✅/❌ | |
| 2 | Navegação para Lista | ✅/❌ | |
| 3 | Busca e Filtro | ✅/❌ | |
| 4 | Detalhes (Não-Logado) | ✅/❌ | |
| 5 | Compartilhamento | ✅/❌ | |
| 6 | Download Edital | ✅/❌ | |
| 7 | Inscrição Sem Login | ✅/❌ | |
| 8 | Login e Redirect | ✅/❌ | |
| 9 | Inscrição (Logado) | ✅/❌ | |
| 10 | Minhas Inscrições | ✅/❌ | |
| 11 | Criar Conta | ✅/❌ | |
| 12 | Inscrições Encerradas | ✅/❌ | |
| 13 | Responsividade Mobile | ✅/❌ | |
| 14 | Performance | ✅/❌ | |
| 15 | Offline (PWA) | ✅/❌ | |
| 16 | Processo Rascunho | ✅/❌ | |
| 17 | Edge Cases | ✅/❌ | |

## Resultado Geral
- Total de testes: 17
- Passaram: __
- Falharam: __
- Taxa de sucesso: ___%

## Bugs Encontrados
1. ...
2. ...

## Observações
...

## Aprovado para Produção?
- [ ] SIM ✅
- [ ] NÃO ❌ (motivo: ...)
```

---

## ✅ Checklist Final

Antes de fazer deploy:
- [ ] Todos os 17 testes passam
- [ ] Console sem erros (DevTools)
- [ ] Network sem 404/500 (DevTools)
- [ ] Mobile responsivo (DevTools mobile mode)
- [ ] Landing page atraente
- [ ] Busca funciona
- [ ] Login redireciona de volta
- [ ] Inscrição cria registro no BD
- [ ] Offline básico funciona
- [ ] Performance > 80 (Lighthouse)
- [ ] Documentação revisada
- [ ] Código sem console.log desnecessários

---

🎉 **Sistema pronto para produção!**
