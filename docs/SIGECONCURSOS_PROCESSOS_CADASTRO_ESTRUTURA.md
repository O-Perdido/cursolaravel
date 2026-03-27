# SIGE Concursos - Estrutura Proposta para Cadastro de Processos

## Objetivo
Definir a estrutura funcional e técnica da área de cadastro de processos do módulo SIGE Concursos, usando como base o fluxo já existente de processos seletivos de estagiários, mas mantendo o domínio totalmente separado.

## Premissas da Etapa
- O módulo deve permanecer isolado do SIGE de estágios.
- Todas as rotas devem continuar sob o prefixo sigeconcursos.
- Todas as views devem permanecer em resources/views/sigeconcursos.
- As tabelas novas devem seguir o prefixo sigeconcursos_tb_.
- Nesta etapa será tratado apenas o cadastro e gerenciamento do processo.
- A parte de inscrição do candidato ficará para uma próxima fase.

## Referência Direta no Sistema Atual
O cadastro de processos seletivos de estagiários já possui uma boa base conceitual e deve servir de inspiração para:

- status do processo
- datas importantes
- cronograma por fases
- anexos do edital
- tela de listagem com filtros
- formulário dividido por blocos

No novo módulo, a diferença principal é que o processo de concurso precisa nascer já preparado para:

- cargos vinculados ao processo
- locais de prova vinculados ao processo
- casos de isenção
- configuração futura de inscrição
- organização de prova e aplicação

## Escopo Funcional da Primeira Entrega
Sugestão de escopo para a primeira implementação:

1. CRUD completo de processos do SIGE Concursos para admin e operador.
2. Cadastro auxiliar de cargos.
3. Cadastro auxiliar de locais de prova.
4. Cadastro auxiliar de salas por local.
5. Vinculação de cargos e locais no processo.
6. Upload e gerenciamento de anexos do processo.
7. Tela de detalhes do processo.

Fica explicitamente fora desta etapa:

1. inscrição do candidato
2. pagamento de taxa
3. boleto
4. distribuição automática de candidatos por sala
5. classificação, resultado e convocações

## Estrutura de Dados Proposta

### 1. Tabela principal de processos
Tabela sugerida: sigeconcursos_tb_processos

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_processo | integer auto increment | PK |
| fk_id_empresa | integer | órgão público/empresa responsável |
| tipo_processo | string(30) | concurso_publico ou processo_seletivo |
| numero_edital | string(50) | número externo do edital |
| titulo | string(255) | nome principal do processo |
| slug | string(255) nullable | útil para área pública futura |
| status | string(30) | rascunho, publicado, inscricoes_abertas, inscricoes_encerradas, em_andamento, finalizado, suspenso |
| resumo | text nullable | resumo curto para listagem e detalhes |
| descricao | longText nullable | descrição completa |
| requisitos_gerais | text nullable | regras gerais do processo |
| observacoes | text nullable | observações administrativas ou públicas |
| data_publicacao | dateTime nullable | publicação do edital |
| data_inicio_inscricoes | dateTime nullable | uso futuro |
| data_fim_inscricoes | dateTime nullable | uso futuro |
| data_prova_objetiva | dateTime nullable | opcional |
| data_resultado_final | dateTime nullable | opcional |
| etapa_fluxo_atual | string(50) | cadastro, inscricoes, homologacao_inscricoes, distribuicao_locais, distribuicao_salas, local_prova_liberado, etapas_finais |
| exige_aceite_edital | boolean | default true |
| permite_condicao_especial | boolean | default true |
| exige_documento_condicao_especial | boolean | default true |
| possui_taxa_inscricao | boolean | default false |
| valor_taxa_padrao | decimal(10,2) nullable | usar apenas se houver taxa única |
| permite_pcd | boolean | default true |
| permite_ampla_concorrencia | boolean | default true |
| quantidade_total_vagas | integer nullable | cache para listagem |
| created_at | timestamp nullable | recomendado adicionar |
| updated_at | timestamp nullable | recomendado adicionar |

Observação:
Mesmo que a inscrição fique para depois, vale a pena deixar desde já os campos da etapa operacional, aceite do edital, condição especial, taxa e modalidades habilitadas, porque isso evita migration corretiva logo na sequência.

### 2. Tabela geral de cargos
Tabela sugerida: sigeconcursos_tb_cargos

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_cargo | integer auto increment | PK |
| nome_cargo | string(255) | nome do cargo |
| descricao | text nullable | descrição resumida |
| escolaridade_minima | string(120) nullable | apoio para o cadastro |
| ativo | boolean | para inativar sem excluir |
| created_at | timestamp nullable | recomendado |
| updated_at | timestamp nullable | recomendado |

### 3. Tabela de relacionamento entre processo e cargo
Tabela sugerida: sigeconcursos_tb_processo_cargos

Essa é a tabela que resolve a necessidade de o mesmo cargo ter valores diferentes conforme o processo.

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_processo_cargo | integer auto increment | PK |
| fk_id_processo | integer | processo pai |
| fk_id_cargo | integer | cargo base |
| quantidade_vagas | integer nullable | vagas imediatas |
| quantidade_cadastro_reserva | integer nullable | cadastro reserva |
| valor_remuneracao | decimal(10,2) nullable | remuneração do cargo naquele processo |
| valor_taxa_inscricao | decimal(10,2) nullable | taxa específica do cargo, se existir |
| carga_horaria | string(100) nullable | ex: 30h ou 40h |
| requisitos_especificos | text nullable | requisitos do cargo naquele edital |
| conteudo_programatico | text nullable | opcional |

### 4. Tabela de locais de prova
Tabela sugerida: sigeconcursos_tb_locais_prova

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_local_prova | integer auto increment | PK |
| nome_local | string(255) | nome do local |
| numero_cep | string(8) | CEP limpo |
| endereco | string(255) | logradouro |
| numero_endereco | string(20) | número |
| complemento_endereco | string(255) nullable | complemento |
| bairro | string(255) | bairro |
| fk_id_cidade | integer | cidade já existente no sistema |
| ativo | boolean | para inativação lógica |
| observacoes | text nullable | apoio operacional |
| created_at | timestamp nullable | recomendado |
| updated_at | timestamp nullable | recomendado |

### 5. Tabela de salas
Tabela sugerida: sigeconcursos_tb_salas

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_sala | integer auto increment | PK |
| fk_id_local_prova | integer | local pai |
| nome_sala | string(120) | identificação da sala |
| bloco | string(120) nullable | bloco/ala/pavilhão |
| capacidade_maxima | integer | número máximo de candidatos |
| observacoes | text nullable | apoio operacional |
| ativo | boolean | para inativação lógica |

### 6. Tabela de vínculo entre processo e local de prova
Tabela sugerida: sigeconcursos_tb_processo_locais

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_processo_local | integer auto increment | PK |
| fk_id_processo | integer | processo pai |
| fk_id_local_prova | integer | local selecionado |
| observacoes | text nullable | mensagem específica do local no processo |

### 7. Tabela de casos de isenção
Tabela sugerida: sigeconcursos_tb_processo_isencoes

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_isencao | integer auto increment | PK |
| fk_id_processo | integer | processo pai |
| titulo | string(255) | nome do caso de isenção |
| descricao | text | regra da isenção |
| data_inicio | dateTime nullable | período opcional |
| data_fim | dateTime nullable | período opcional |
| exige_comprovacao | boolean | uso futuro |

### 8. Tabela de anexos do processo
Tabela sugerida: sigeconcursos_tb_processo_arquivos

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_arquivo | integer auto increment | PK |
| fk_id_processo | integer | processo pai |
| nome_exibicao | string(255) | rótulo do arquivo |
| tipo_arquivo | string(50) | edital, retificacao, anexo, conteudo_programatico, resultado, outro |
| caminho_arquivo | string(255) | storage path |
| ordem_exibicao | integer nullable | ordenar na tela |
| created_at | timestamp nullable | recomendado |

### 9. Tabela de documentos exigidos na inscrição
Tabela sugerida: sigeconcursos_tb_processo_documentos_exigidos

Campos sugeridos:

| Campo | Tipo sugerido | Observação |
| --- | --- | --- |
| id_documento_exigido | integer auto increment | PK |
| fk_id_processo | integer | processo pai |
| titulo | string(255) | nome do documento a ser solicitado |
| descricao | text nullable | instrução complementar para o candidato |
| obrigatorio | boolean | indica se o envio é obrigatório |
| ordem_exibicao | integer nullable | ordem na tela |

## Modelos Laravel Sugeridos
- SigeConcursoProcesso
- SigeConcursoCargo
- SigeConcursoProcessoCargo
- SigeConcursoLocalProva
- SigeConcursoSala
- SigeConcursoProcessoLocal
- SigeConcursoProcessoIsencao
- SigeConcursoProcessoArquivo

Relações principais:

- processo belongsTo orgao
- processo hasMany processoCargos
- processo hasMany processoLocais
- processo hasMany isencoes
- processo hasMany arquivos
- processo hasMany documentosExigidos
- cargo hasMany processoCargos
- localProva hasMany salas
- localProva belongsTo cidade

## Estrutura de Rotas Sugerida

### Rotas de processos
- GET sigeconcursos/processos
- GET sigeconcursos/processos/create
- POST sigeconcursos/processos
- GET sigeconcursos/processos/{id}
- GET sigeconcursos/processos/{id}/edit
- PUT sigeconcursos/processos/{id}
- DELETE sigeconcursos/processos/{id}
- DELETE sigeconcursos/processos/arquivos/{id}

### Rotas auxiliares de cargos
- GET sigeconcursos/cargos
- GET sigeconcursos/cargos/create
- POST sigeconcursos/cargos
- GET sigeconcursos/cargos/{id}/edit
- PUT sigeconcursos/cargos/{id}
- DELETE sigeconcursos/cargos/{id}

### Rotas auxiliares de locais e salas
- GET sigeconcursos/locais-prova
- GET sigeconcursos/locais-prova/create
- POST sigeconcursos/locais-prova
- GET sigeconcursos/locais-prova/{id}
- GET sigeconcursos/locais-prova/{id}/edit
- PUT sigeconcursos/locais-prova/{id}
- DELETE sigeconcursos/locais-prova/{id}
- POST sigeconcursos/locais-prova/{id}/salas
- PUT sigeconcursos/salas/{id}
- DELETE sigeconcursos/salas/{id}

## Controller Principal Sugerido
Controller: SigeConcursoProcessoController

Métodos esperados:

- index
- create
- store
- show
- edit
- update
- destroy
- removerArquivo

### Responsabilidades do controller
- montar filtros de listagem
- validar os dados do formulário
- persistir processo principal
- sincronizar cargos vinculados
- sincronizar locais vinculados
- persistir casos de isenção
- salvar anexos

### Observação de arquitetura
Como o formulário de processo será mais complexo que o de órgãos e candidatos, vale usar um service para salvar os relacionamentos e manter o controller menor.

Service sugerido:

- SigeConcursoProcessoService

Responsabilidades do service:

- criar processo completo
- atualizar processo completo
- sincronizar anexos e relacionamentos
- recalcular quantidade_total_vagas

## Estrutura das Telas

### 1. Listagem de processos
View sugerida: resources/views/sigeconcursos/processos/index.blade.php

Filtros sugeridos:

- título
- número do edital
- órgão público
- tipo do processo
- status
- período de publicação
- ordenar por cadastro ou publicação

Colunas sugeridas na tabela:

- título
- número do edital
- órgão
- tipo
- status
- período de inscrição
- ações

Ações sugeridas:

- detalhes
- editar
- excluir

### 2. Cadastro e edição do processo
Views sugeridas:

- resources/views/sigeconcursos/processos/create.blade.php
- resources/views/sigeconcursos/processos/edit.blade.php
- resources/views/sigeconcursos/processos/_form.blade.php

Blocos sugeridos no formulário:

#### Bloco A - Informações básicas
- tipo do processo
- órgão responsável
- número do edital
- título
- status
- resumo
- descrição completa

#### Bloco B - Datas e cronograma
- data de publicação
- data de início das inscrições
- data de fim das inscrições
- data prevista da prova
- data prevista do resultado final
- tabela dinâmica de fases, igual ao conceito já usado em estágios

#### Bloco C - Cargos e vagas
- seleção de um ou mais cargos cadastrados
- para cada cargo vinculado, permitir informar:
  - quantidade de vagas
  - cadastro reserva
  - remuneração
  - taxa de inscrição específica
  - carga horária
  - requisitos específicos

#### Bloco D - Locais de prova
- seleção múltipla de locais cadastrados
- chave para marcar se o processo permite ou não escolha do local na inscrição futura

#### Bloco E - Casos de isenção
- lista dinâmica com título e descrição
- período da solicitação de isenção
- marcar se exigirá documento futuro

#### Bloco F - Configurações futuras de inscrição
- aceite obrigatório do edital
- modalidades habilitadas
- se haverá taxa de inscrição
- valor padrão da taxa, quando aplicável

#### Bloco G - Arquivos
- edital
- retificações
- anexos complementares
- conteúdo programático

#### Bloco H - Observações finais
- observações públicas
- observações administrativas, se desejar separar em campo próprio

### 3. Tela de detalhes do processo
View sugerida: resources/views/sigeconcursos/processos/show.blade.php

Objetivo:

- visão consolidada do processo
- mostrar o fluxo operacional atual
- mostrar cargos vinculados
- mostrar locais de prova
- mostrar isenções cadastradas
- mostrar documentos exigidos na inscrição
- listar anexos
- servir de base futura para acompanhar inscrições, resultados e relatórios

## Comportamentos Importantes

### Status do processo
Sugestão de status:

- rascunho
- publicado
- inscricoes_abertas
- inscricoes_encerradas
- em_andamento
- finalizado
- suspenso

### Exclusão
- Excluir processo deve verificar vínculos futuros antes de remover.
- Se já houver inscrições em etapa futura, a exclusão idealmente deverá ser bloqueada.
- Antes da fase de inscrição, a exclusão pode ser física.

### Upload de arquivos
- Reaproveitar a ideia do módulo de estágios.
- Separar tipo do arquivo para exibição organizada.
- Armazenar em pasta própria, por exemplo: storage/app/public/sigeconcursos/processos/{id_processo}

## Estratégia de Implementação Recomendada

### Fase 1
- criar migrations e models de cargos, locais, salas e processos
- cadastrar rotas básicas do módulo
- montar CRUD de cargos
- montar CRUD de locais de prova e salas

### Fase 2
- montar CRUD de processos com formulário completo
- implementar relacionamento de cargos, locais e isenções
- implementar anexos do processo
- implementar tela de detalhes

### Fase 3
- revisar UX
- validar mensagens, filtros e exclusões
- documentar fluxo completo

## Decisões que Já Valem a Pena Assumir
Para acelerar a implementação, esta proposta já considera:

1. cargos em tabela mestre separada
2. valores variáveis no pivot processo x cargo
3. locais de prova independentes do processo, com reaproveitamento
4. salas vinculadas ao local, e não diretamente ao processo
5. cronograma por fases no mesmo estilo do módulo de estágios
6. inscrição tratada em etapa posterior, mas com o processo já preparado para recebê-la

## Resultado Esperado da Próxima Etapa de Desenvolvimento
Ao iniciar a implementação, o objetivo será entregar um módulo onde admin e operador consigam:

1. cadastrar cargos
2. cadastrar locais de prova e salas
3. cadastrar um processo completo
4. vincular cargos, valores e vagas ao processo
5. vincular locais de prova ao processo
6. anexar edital e documentos complementares
7. consultar, editar e excluir o processo

Com isso, a próxima fase de inscrição ficará apoiada sobre uma base já pronta, sem retrabalho estrutural.