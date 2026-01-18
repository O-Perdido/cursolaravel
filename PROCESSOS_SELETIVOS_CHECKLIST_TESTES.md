# Checklist de Testes - Processos Seletivos

Use este checklist para validar que o módulo está funcionando corretamente.

---

## ✅ Testes de Banco de Dados

- [ ] Tabelas criadas com sucesso: `php artisan migrate --force`
- [ ] Tabela `tb_processos_seletivos` existe
- [ ] Tabela `tb_processos_arquivos` existe
- [ ] Tabela `tb_inscricoes_processo` existe
- [ ] Tabela `tb_resultados_processo` existe
- [ ] Chaves estrangeiras configuradas corretamente
- [ ] Índices criados

---

## ✅ Testes de Rotas

### Admin/Operador - Gerenciar Processos

- [ ] `GET /processos-seletivos` - Listagem (acesso permitido)
- [ ] `GET /processos-seletivos/create` - Formulário novo
- [ ] `POST /processos-seletivos` - Criar novo
- [ ] `GET /processos-seletivos/{id}/edit` - Editar
- [ ] `PUT /processos-seletivos/{id}` - Atualizar
- [ ] `DELETE /processos-seletivos/{id}` - Deletar
- [ ] `GET /processos-seletivos/{id}/inscricoes` - Ver inscrições
- [ ] `POST /processos-seletivos/{id}/inscricoes/exportar` - Exportar
- [ ] `GET /processos-seletivos/{id}/resultados` - Gerenciar resultados
- [ ] `POST /processos-seletivos/{id}/resultados` - Publicar resultado

### Estagiário - Inscrição

- [ ] `GET /processos-seletivos-abertos` - Listar processos
- [ ] `GET /processos-seletivos/{id}/detalhes` - Ver detalhes
- [ ] `POST /processos-seletivos/{id}/inscrever` - Se inscrever (AJAX)
- [ ] `GET /minhas-inscricoes` - Minhas inscrições

---

## ✅ Testes de Autorização

### Admin/Operador

- [ ] Consegue acessar listagem de processos
- [ ] Consegue criar novo processo
- [ ] Consegue editar processo
- [ ] Consegue deletar processo
- [ ] Consegue ver inscrições
- [ ] Consegue publicar resultados

### Estagiário

- [ ] Consegue acessar "Ver Processos Abertos"
- [ ] Consegue ver detalhes de processo
- [ ] Consegue se inscrever
- [ ] Consegue acessar "Minhas Inscrições"
- [ ] NÃO consegue acessar listagem administrativa

### Não Autenticado

- [ ] É redirecionado para login ao tentar acessar

---

## ✅ Testes de Funcionalidades

### Criar Processo

- [ ] Formulário carrega corretamente
- [ ] Valida título obrigatório
- [ ] Valida empresa obrigatória
- [ ] Valida status obrigatório
- [ ] Data de fechamento deve ser >= data de abertura
- [ ] Processo é criado com número único
- [ ] Número segue padrão YYYY-NNNN
- [ ] Permite múltiplos arquivos
- [ ] Cada arquivo tem nome customizável
- [ ] Processo é salvo em status "rascunho" por padrão
- [ ] Mensagem de sucesso aparece após salvar

### Editar Processo

- [ ] Formulário pré-popula dados corretos
- [ ] Permite editar todos os campos
- [ ] Permite adicionar novos arquivos
- [ ] Mostra arquivos existentes
- [ ] Permite fazer download dos arquivos
- [ ] Atualização é salva corretamente
- [ ] Número do processo não muda

### Deletar Processo

- [ ] Pede confirmação
- [ ] Deleta processo e todas as inscrições
- [ ] Deleta arquivos do storage
- [ ] Redirecionará para listagem após deletar
- [ ] Mensagem de sucesso aparece

### Listar Inscrições

- [ ] Mostra todos os inscritos
- [ ] Exibe dados: nome, email, telefone, curso, status, data
- [ ] Paginação funciona (se > 50 inscritos)
- [ ] Botões de marcar como deferido funcionam
- [ ] Botões de marcar como indeferido funcionam
- [ ] Status é atualizado corretamente
- [ ] Botão de exportação existe (placeholder)

### Publicar Resultado

- [ ] Modal de publicação abre
- [ ] Valida nome do resultado obrigatório
- [ ] Arquivo é opcional
- [ ] Arquivo é armazenado corretamente
- [ ] Resultado aparece na listagem
- [ ] Estagiário consegue acessar resultado

---

## ✅ Testes de Views - Admin

### Listagem (index)

- [ ] Tabela carrega com dados
- [ ] Filtro de status funciona
- [ ] Filtro de empresa funciona (se não for empresa)
- [ ] Botão "Novo Processo" funciona
- [ ] Botões de ação funcionam:
  - [ ] Editar
  - [ ] Ver Inscrições
  - [ ] Resultados
  - [ ] Deletar
- [ ] Paginação funciona
- [ ] Dados aparecem corretos
- [ ] Badges de status aparecem com cores corretas

### Criar/Editar

- [ ] Todos os campos do formulário aparecem
- [ ] Labels estão corretos
- [ ] Validações mensagens aparecem
- [ ] Upload múltiplo de arquivos funciona
- [ ] Botão "Adicionar Arquivo" funciona
- [ ] Botão "Remover Arquivo" funciona
- [ ] JavaScript para adicionar/remover funciona
- [ ] Submit salva corretamente
- [ ] Link "Cancelar" funciona

### Inscrições

- [ ] Header com informações do processo aparece
- [ ] Tabela com inscritos carrega
- [ ] Badge de status aparece com cores corretas
- [ ] Botões de marcar deferido/indeferido funcionam
- [ ] Modal de exportação abre
- [ ] Paginação funciona

### Resultados

- [ ] Mostra resultados publicados
- [ ] Botão "Publicar Resultado" funciona
- [ ] Modal abre corretamente
- [ ] Upload de arquivo funciona
- [ ] Resultado aparece na lista após publicar

---

## ✅ Testes de Views - Estagiário

### Processos Abertos (Cards)

- [ ] Carrega com lista de processos
- [ ] Cards aparecem com:
  - [ ] Logo da empresa
  - [ ] Nome da empresa
  - [ ] Título do processo
  - [ ] Badges (número, status)
  - [ ] Cursos
  - [ ] Data de fechamento
  - [ ] Botão "Ver Detalhes"
- [ ] Cards são responsivos
- [ ] Click no card leva para detalhes
- [ ] Aparência é bonita e mobile-friendly

### Detalhes

- [ ] Todas as informações aparecem:
  - [ ] Título grande
  - [ ] Logo e nome da empresa
  - [ ] Status
  - [ ] Cards com datas
  - [ ] Fases do processo
  - [ ] Cursos
  - [ ] Requisitos
  - [ ] Observações
  - [ ] Arquivos para download
- [ ] Sidebar com ações aparece:
  - [ ] Botão "Se Inscrever" (se não inscrito)
  - [ ] Aviso "Já está inscrito" (se inscrito)
  - [ ] Link "Minhas Inscrições"
- [ ] Layout é responsivo
- [ ] Arquivos são baixáveis
- [ ] Modal de inscrição abre ao clicar "Se Inscrever"

### Modal de Inscrição

- [ ] Modal abre corretamente
- [ ] Mostra aviso personalizado
- [ ] Botões "Cancelar" e "Confirmar" aparecem
- [ ] Cancelar fecha o modal
- [ ] Confirmar envia AJAX
- [ ] Mensagem de sucesso aparece
- [ ] Página recarrega após sucesso

### Minhas Inscrições

- [ ] Carrega com lista de inscrições do estagiário
- [ ] Cards aparecem com:
  - [ ] Título do processo
  - [ ] Empresa
  - [ ] Status (badge com cor)
  - [ ] Número do processo
  - [ ] Data de inscrição
  - [ ] Data de fechamento
  - [ ] Texto descritivo do status
  - [ ] Botões de ação
- [ ] Botão "Ver Detalhes" leva para página de detalhes
- [ ] Botão "Resultados" aparece se resultados publicados
- [ ] Modal de resultados mostra arquivos
- [ ] Página vazia mostra mensagem apropriada

---

## ✅ Testes de Segurança

- [ ] Token CSRF presente em formulários
- [ ] Não autenticado é redirecionado para login
- [ ] Estagiário não consegue acessar admin
- [ ] Admin/Operador consegue gerenciar processos
- [ ] Empresa consegue gerenciar seus processos
- [ ] Validações no servidor (não só JavaScript)
- [ ] SQL injection não funciona
- [ ] XSS prevention ativo

---

## ✅ Testes de Dados

### Criar Processo

- [ ] [ ] Cria processo com sucesso
- [ ] Número é único
- [ ] Número segue padrão YYYY-NNNN
- [ ] Dados são salvos corretamente no BD
- [ ] Arquivos são salvos em storage/public

### Inscrição

- [ ] Cria inscrição com sucesso
- [ ] Previne duplicação (unique constraint)
- [ ] Status default é "inscrito"
- [ ] Data de inscrição é atual
- [ ] Relacionamento com processo está correto
- [ ] Relacionamento com estagiário está correto

### Resultados

- [ ] Cria resultado com sucesso
- [ ] Relacionamento com processo está correto
- [ ] Arquivo é armazenado corretamente
- [ ] Arquivo é acessível via URL

---

## ✅ Testes de Performance

- [ ] Listagem de processos carrega em < 2s
- [ ] Listagem de inscrições carrega em < 2s
- [ ] Upload de arquivo é rápido
- [ ] AJAX de inscrição é responsivo
- [ ] Sem lentidão na navegação

---

## ✅ Testes de Layout

### Desktop (1920x1080)

- [ ] Todas as views aparecem corretamente
- [ ] Sem scroll horizontal desnecessário
- [ ] Fonte legível
- [ ] Botões clicáveis

### Tablet (768x1024)

- [ ] Views responsivas
- [ ] Sem elementos quebrados
- [ ] Botões acessíveis

### Mobile (375x667)

- [ ] Cards empilham corretamente
- [ ] Tabelas scrolláveis
- [ ] Botões com tap targets adequados
- [ ] Sem overflow horizontal

---

## ✅ Testes de Navegação

- [ ] Links na navbar funcionam
- [ ] Links internos funcionam
- [ ] Botão voltar funciona
- [ ] Paginação funciona
- [ ] Botões de ação funcionam

---

## ✅ Testes de Mensagens

- [ ] Mensagens de sucesso aparecem
- [ ] Mensagens de erro aparecem
- [ ] Mensagens de validação aparecem
- [ ] Mensagens em português correto
- [ ] Nenhuma mensagem quebrada

---

## ✅ Testes de Integração

- [ ] Models relacionados funcionam
- [ ] Cascade delete funciona
- [ ] Relações hasMany funcionam
- [ ] Relações belongsTo funcionam
- [ ] Eager loading funciona

---

## 📝 Executar Testes Completos

```bash
# Resetar banco e rodar todas as migrations
php artisan migrate:fresh

# Seedar dados de teste (opcional)
php artisan db:seed

# Verificar rotas
php artisan route:list | grep processo

# Verificar models
php artisan tinker
> App\Models\ProcessoSeletivo::count()

# Executar testes (se implementados)
php artisan test
```

---

## 🎯 Resultado Final

Quando todos os testes passarem, o módulo está **pronto para produção**!

✅ = Passou
❌ = Falhou

Se algum teste falhar, revise a documentação de design ou implementação.
