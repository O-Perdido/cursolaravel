# Módulo de Processos Seletivos de Estagiários - Design & Arquitetura

## Visão Geral
Módulo similar ao de "Vagas", permitindo que operadores/admin criem editais de processos seletivos com inscrição de estagiários, geração de relatórios e publicação de resultados.

## Estrutura de Dados

### Tabelas Necessárias

#### 1. `tb_processos_seletivos` (Edital Principal)
```
- id_processo (PK, autoincrement)
- numero_processo (unique, formato: YYYY-NNNN)
- titulo (string, 200)
- fk_id_empresa (FK -> tb_empresas)
- status (enum: 'rascunho', 'aberto', 'inscricoes', 'encerrado', 'finalizado')
- data_criacao (datetime)
- data_abertura (datetime)
- data_fechamento_inscricoes (datetime)
- descricao_fases (text)
- cursos_destino (text) - JSON array com lista de cursos
- requisitos (text)
- observacoes (text)
- aviso_inscricao (text) - mensagem personalizada para inscrição
- created_at, updated_at
```

#### 2. `tb_processos_arquivos` (Anexos: Edital, Retificações, etc)
```
- id_arquivo (PK)
- fk_id_processo (FK -> tb_processos_seletivos)
- nome_exibicao (string, 150) - "Edital", "Retificação 1", etc
- caminho_arquivo (string)
- tipo_arquivo (enum: 'edital', 'retificacao', 'resultado', 'outro')
- data_upload (datetime)
- created_at, updated_at
```

#### 3. `tb_inscricoes_processo` (Inscrições de Estagiários)
```
- id_inscricao (PK)
- fk_id_processo (FK -> tb_processos_seletivos)
- fk_id_estagiario (FK -> tb_estagiarios)
- data_inscricao (datetime)
- status_inscricao (enum: 'inscrito', 'deferido', 'indeferido')
- observacoes (text)
- created_at, updated_at
```

#### 4. `tb_resultados_processo` (Publicação de Resultados)
```
- id_resultado (PK)
- fk_id_processo (FK -> tb_processos_seletivos)
- numero_resultado (string) - Ex: "Resultado Final"
- data_publicacao (datetime)
- arquivo_resultado (string) - caminho do arquivo PDF/Excel
- created_at, updated_at
```

## Models

### ProcessoSeletivo (extends Model)
- table: `tb_processos_seletivos`
- primaryKey: `id_processo`
- Relações:
  - `empresa()` belongsTo Empresa
  - `arquivos()` hasMany ProcessoArquivo
  - `inscricoes()` hasMany InscricaoProcesso
  - `resultados()` hasMany ResultadoProcesso
  - `inscricoesCount()` helper

### ProcessoArquivo (extends Model)
- table: `tb_processos_arquivos`
- primaryKey: `id_arquivo`
- Relações:
  - `processo()` belongsTo ProcessoSeletivo

### InscricaoProcesso (extends Model)
- table: `tb_inscricoes_processo`
- primaryKey: `id_inscricao`
- Relações:
  - `processo()` belongsTo ProcessoSeletivo
  - `estagiario()` belongsTo Estagiario

### ResultadoProcesso (extends Model)
- table: `tb_resultados_processo`
- primaryKey: `id_resultado`
- Relações:
  - `processo()` belongsTo ProcessoSeletivo

### Estagiario (atualizar relação)
- `inscricoes()` hasMany InscricaoProcesso

## Rotas

### Admin/Operador - Gerenciamento de Processos

```
GET    /processos-seletivos              -> ProcessoSeletivoController@index (listagem)
GET    /processos-seletivos/create       -> ProcessoSeletivoController@create (formulário)
POST   /processos-seletivos              -> ProcessoSeletivoController@store
GET    /processos-seletivos/{id}/edit    -> ProcessoSeletivoController@edit
PUT    /processos-seletivos/{id}         -> ProcessoSeletivoController@update
DELETE /processos-seletivos/{id}         -> ProcessoSeletivoController@destroy

GET    /processos-seletivos/{id}/inscricoes    -> ProcessoSeletivoController@listarInscricoes
GET    /processos-seletivos/{id}/inscricoes/export -> ProcessoSeletivoController@exportarInscricoes (PDF/Excel)

GET    /processos-seletivos/{id}/resultados    -> ProcessoSeletivoController@resultados (gerenciar)
POST   /processos-seletivos/{id}/resultados    -> ProcessoSeletivoController@publicarResultado
```

### Estagiário - Inscrição

```
GET    /estagiario/processos-seletivos         -> ProcessoSeletivoPublicoController@listarAbertos (cards bonitos)
GET    /estagiario/processos-seletivos/{id}    -> ProcessoSeletivoPublicoController@detalhes
POST   /estagiario/processos-seletivos/{id}/inscrever -> ProcessoSeletivoPublicoController@inscrever (AJAX)

GET    /estagiario/minhas-inscricoes           -> ProcessoSeletivoPublicoController@minhasInscricoes
```

## Controllers

### ProcessoSeletivoController (Admin/Operador)
- `index()` - listagem com filtros
- `create()` - formulário novo
- `store()` - salvar
- `edit()` - formulário edição
- `update()` - atualizar
- `destroy()` - deletar
- `listarInscricoes()` - ver inscritos
- `exportarInscricoes()` - gerar PDF/Excel
- `resultados()` - gerenciar resultados
- `publicarResultado()` - publicar resultado

### ProcessoSeletivoPublicoController (Estagiário)
- `listarAbertos()` - lista com cards
- `detalhes()` - página detalhada
- `inscrever()` - realizar inscrição (AJAX)
- `minhasInscricoes()` - inscrições do usuário

## Views

### Admin/Operador

#### `processos-seletivos/index.blade.php`
- Tabela com listagem (numero, empresa, status, data_abertura)
- Filtros: status, empresa
- Botão: Novo Processo

#### `processos-seletivos/create.blade.php` & `edit.blade.php`
- Formulário completo
- Seções:
  1. Informações básicas (titulo, empresa, status)
  2. Datas (abertura, fechamento inscrições)
  3. Descrição (fases, cursos, requisitos, observações)
  4. Aviso de Inscrição (texto personalizado)
  5. Upload de Arquivos (edital, retificações com nome customizável)
  6. Resultados (anexar PDFs com resultados)

### Estagiário

#### `processos-seletivos/listar-abertos.blade.php`
- Cards layout (mobile-friendly)
- Cada card mostra:
  - Logo empresa (pequena)
  - Nome empresa
  - Título processo (ou número)
  - Status
  - Número interno
  - Data relevante
- Dois botões principais:
  1. "Ver Detalhes" / "Se inscrever"
  2. "Minhas Inscrições"

#### `processos-seletivos/detalhes.blade.php`
- Informações completas
- Descrição fases, cursos, requisitos
- Arquivos do edital para download
- Botão "Se inscrever" (abre modal)
- Modal de inscrição com aviso personalizado

#### `processos-seletivos/minhas-inscricoes.blade.php`
- Lista de processos em que está inscrito
- Status de cada inscrição (inscrito, deferido, indeferido)
- Links para detalhes e resultado

## Fluxo de Negócio

### Criar Processo Seletivo
1. Operador preenche formulário
2. Define empresa, datas, descrições
3. Faz upload de edital + retificações com nomes customizáveis
4. Salva em status "rascunho"
5. Pode editar/atualizar antes de publicar

### Publicar Processo
1. Operador muda status para "aberto" ou "inscricoes"
2. Estagiários veem processo nos cards

### Inscrição do Estagiário
1. Estagiário clica "Se inscrever"
2. Modal mostra aviso personalizado
3. Confirma e cria InscricaoProcesso
4. Sucesso e atualiza status na UI

### Exportar Inscrições
1. Operador clica "Exportar Inscrições"
2. Gera PDF ou Excel com todos os inscritos
3. Download automático

### Publicar Resultados
1. Operador faz upload arquivo com resultados
2. Muda status do processo para "finalizado"
3. Estagiários veem resultado nas "Minhas Inscrições"

## Middleware & Autorização
- Operador/Admin: rotas `/processos-seletivos/` (admin_ou_operador)
- Estagiário: rotas `/estagiario/processos-seletivos/` (estagiario_verified)
- Listagem pública filtrada por status/datas

## Padrões de Código
- Seguir convenções do projeto (prefix `tb_`, chaves `id_[singular]`, `fk_id_[singular]`)
- Usar `fillable` explícito em Models
- Validações no Controller
- Relações bem definidas
- Locales pt_BR para mensagens
