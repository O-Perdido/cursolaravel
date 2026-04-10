# Plano do Modulo de Divulgacao de Vagas

## Objetivo

Expandir o modulo de vagas para permitir divulgacao publica controlada, candidatura de estagiarios com envio de curriculo, acompanhamento das candidaturas pela unidade concedente e pelos operadores/admin, notificacoes por e-mail sobre mudancas de status e aproveitamento do estagiario definido no fluxo de geracao do termo.

## O que ja existe hoje

- Cadastro e listagem de vagas para admin, operador e empresa.
- Campo manual na vaga indicando se ja existe estagiario definido pela unidade concedente.
- Fluxo de geracao de termo com parametro de vaga e com suporte a pre-selecao de estagiario.
- Fluxo semelhante de inscricao em processos seletivos com upload opcional de arquivo.

## Entendimento consolidado do pedido

1. As vagas cadastradas passarao a ter uma pagina publica para divulgacao e busca pelos estagiarios.
2. O estagiario podera visualizar a vaga publicamente, mas para se candidatar precisara estar autenticado como estagiario.
3. A candidatura exigira anexo de curriculo no momento do envio.
4. Empresa, admin e operador poderao abrir a vaga e visualizar todas as candidaturas recebidas.
5. Empresa, admin e operador poderao alterar o status de cada candidatura.
6. Cada mudanca relevante de status devera disparar e-mail ao estagiario.
7. Empresa, admin e operador poderao definir um estagiario para a vaga a partir das candidaturas recebidas ou manualmente.
8. Quando admin ou operador clicar para preencher a vaga e gerar o termo, o estagiario definido devera vir previamente selecionado.
9. A listagem interna das vagas devera destacar melhor a situacao operacional: vaga preenchida, vaga com estagiario definido, vaga com candidaturas pendentes, quantidade de candidatos etc.
10. O fluxo deve ficar mais pratico e intuitivo, inclusive com a liberdade de adicionar detalhes uteis para operacao.

## Direcao tecnica proposta

### 1. Criar entidade propria de candidatura de vaga

Motivo:
Hoje a vaga so guarda um campo manual de estagiario definido. Isso nao sustenta historico de candidaturas, curriculo por candidato, status individual nem notificacao por evento.

Proposta:
Criar uma nova tabela, model e relacoes para candidaturas da vaga.

Estrutura sugerida da tabela:

- tabela: tb_vaga_candidaturas
- pk: id_candidatura
- fk_id_vaga
- fk_id_estagiario
- status_candidatura
- curriculo_arquivo
- observacoes_estagiario
- observacoes_internas
- analisado_em
- fk_id_usuario_analisou
- notificado_em
- created_at
- updated_at

Regra importante:
- criar indice unico para evitar o mesmo estagiario se candidatar duas vezes para a mesma vaga.

### 2. Padronizar status da candidatura

Sugestao de status:

- enviada: candidatura recem recebida
- em_analise: vaga visualizada e em triagem
- entrevista: candidato avancou para contato/entrevista
- aprovado: candidato aprovado pela unidade
- nao_selecionado: candidato encerrado sem aprovacao
- desistente: candidato desistiu ou nao respondeu
- definido: candidato efetivamente escolhido para preencher a vaga

Observacao:
- definido sera o status terminal operacional que vincula o candidato escolhido a vaga.
- aprovado pode existir como etapa anterior a definido, caso a unidade ainda nao tenha mandado gerar o termo.

### 3. Tratar o estagiario definido da vaga como dado estruturado

Hoje existe:

- tem_estagiario_definido
- nome_estagiario
- contato_whatsapp
- contato_email

Direcao sugerida:

- manter esses campos por compatibilidade e para casos manuais/transitorios.
- adicionar fk_id_estagiario_definido na vaga.
- quando a definicao vier de uma candidatura, preencher esse fk e sincronizar os campos textuais para exibicao rapida.
- quando a definicao for manual, permitir continuar preenchendo nome/contatos sem exigir cadastro formal imediato.

Isso permite dois cenarios:

1. unidade escolheu um estagiario ja cadastrado e/ou candidato da vaga.
2. unidade informou um nome manualmente antes da formalizacao completa.

### 4. Separar status da vaga de status da candidatura

Status da vaga continua representando a situacao macro:

- disponivel
- suspensa
- preenchida

Indicadores derivados para operacao:

- divulgada_publicamente: sim/nao
- total_candidaturas
- possui_candidaturas
- possui_estagiario_definido
- possui_candidato_em_analise
- possui_candidato_aprovado
- termo_pendente_geracao: sim/nao

Regra util:
- termo_pendente_geracao = vaga sem fk_id_termo, com estagiario definido.

### 5. Criar area publica/portal do estagiario para vagas

Fluxos sugeridos:

- pagina publica de listagem das vagas divulgadas
- pagina de detalhes da vaga
- area autenticada do estagiario com:
  - minhas candidaturas
  - status atual de cada candidatura
  - download ou visualizacao do curriculo enviado, se fizer sentido

Filtros sugeridos na listagem publica:

- busca por titulo/atividade/local/empresa
- remunerada ou nao
- periodo da vaga
- unidade concedente
- somente vagas abertas para candidatura

### 6. Regras de candidatura

- somente usuario com nivel estagiario pode se candidatar.
- exigir login e middleware de estagiario verificado.
- exigir upload de curriculo no envio.
- permitir formatos comuns como pdf, doc, docx.
- tamanho maximo sugerido: 5 MB.
- impedir candidatura duplicada para a mesma vaga.
- impedir candidatura em vaga suspensa, preenchida ou nao divulgada.

Opcional util:
- permitir atualizar o curriculo apenas antes da candidatura, via perfil do estagiario, mas na primeira versao o upload no ato da candidatura ja resolve o requisito.

### 7. Regras de analise da unidade concedente

Empresa, admin e operador poderao:

- listar candidaturas por vaga
- abrir detalhes do estagiario candidato
- baixar curriculo anexado
- alterar status da candidatura
- registrar observacao interna
- definir candidato escolhido
- remover definicao, se ainda nao houver termo gerado

Controle de acesso:

- empresa so enxerga vagas e candidaturas da propria unidade
- admin e operador enxergam tudo

### 8. Notificacoes por e-mail

Eventos sugeridos:

- candidatura recebida: opcional para o estagiario
- candidatura em analise
- candidatura em entrevista
- candidatura aprovada
- candidatura nao selecionada
- candidatura definida para preenchimento da vaga

Regra adicional definida:

- ao salvar uma mudanca de status relevante, a interface deve perguntar: "Ao salvar, enviar e-mail para estagiário?"
- opcoes esperadas: Enviar, Não enviar, Cancelar
- o envio nao deve ser automatico e silencioso; o operador decide a cada alteracao

Observacao tecnica:
- hoje o projeto quase nao usa notifications/mailables para esse tipo de fluxo, entao sera preciso criar pelo menos uma mailable especifica ou um conjunto pequeno de mailables.
- a mensagem deve informar vaga, unidade concedente, novo status e eventual observacao publicada ao candidato.

### 9. Integracao com o cadastro do termo

Estado desejado:

- ao clicar em preencher vaga, o sistema abre o create do termo com a vaga preselecionada.
- se a vaga tiver fk_id_estagiario_definido, o campo de estagiario deve vir automaticamente selecionado.
- se existir apenas nome manual e nao houver fk estruturada, o sistema mantem o aviso visual, mas nao consegue selecionar automaticamente um cadastro inexistente.

Melhoria adicional sugerida:
- mostrar um alerta no formulario do termo quando a vaga tiver estagiario definido, informando se a selecao foi automatica ou se a definicao ainda esta apenas manual/textual.

### 10. Melhorias de visibilidade na listagem interna

Na lista de vagas para empresa, admin e operador, incluir destaques operacionais como:

- badge de quantidade de candidaturas
- badge indicando estagiario definido
- badge de termo pendente
- badge de vaga preenchida
- acao rapida para ver candidaturas
- possivel ordenacao priorizando vagas com estagiario definido sem termo

Tambem vale considerar:

- filtro por vagas com candidaturas
- filtro por vagas com estagiario definido
- filtro por vagas prontas para gerar termo

### 11. Compatibilidade com o que ja existe

Pontos de reaproveitamento:

- padrao de rotas autenticadas por nivel
- padrao de upload usado em inscricoes de processos seletivos
- create do termo ja aceita vaga_id e id_estagiario
- listagem de estagiarios e telas internas ja existentes para consulta do candidato

Pontos que precisarao ser ajustados:

- model Vaga e VagaController
- listagem interna de vagas
- formulario create/edit de vaga para incluir configuracoes de divulgacao
- create do termo para selecionar automaticamente o estagiario definido
- novas views publicas e autenticadas para o estagiario

## Entregas sugeridas por fase

### Fase 1 - Base de dados e regras centrais

- criar tabela de candidaturas da vaga
- criar relacoes no model Vaga e Estagiario
- adicionar fk_id_estagiario_definido na vaga
- criar controller/acoes para candidatura e para gestao interna
- criar validacoes e autorizacoes

### Fase 2 - Portal do estagiario

- listagem publica/autenticada de vagas divulgadas
- detalhes da vaga
- candidatura com upload de curriculo
- tela de minhas candidaturas

### Fase 3 - Operacao interna

- tela de candidaturas por vaga
- troca de status
- definicao do candidato escolhido
- notificacoes por e-mail
- indicadores na listagem de vagas

### Fase 4 - Integracao fina com o termo

- pre-selecao automatica do estagiario no create do termo
- alertas de termo pendente
- refinamento de badges e filtros

## Decisoes que ja considero adequadas para implementar sem te travar

1. Usar entidade propria de candidatura, e nao tentar encaixar tudo dentro da vaga.
2. Exigir curriculo no ato da candidatura.
3. Usar status mais operacionais que conversem com o fluxo real da unidade.
4. Permitir definicao manual de estagiario na vaga, mas priorizar definicao estruturada via candidatura.
5. Destacar fortemente vagas com estagiario definido e sem termo gerado.

## Pontos de atencao

- se o estagiario escolhido ainda nao estiver cadastrado no sistema, nao sera possivel preselecionar no create do termo ate que exista um cadastro correspondente.
- como o projeto ainda nao tem uma infraestrutura forte de notificacoes para este tipo de evento, a parte de e-mail precisa ser criada com cuidado para nao virar disparo duplicado.
- sera melhor evitar expor arquivo de curriculo por URL publica simples; idealmente o download deve passar por rota autorizada, especialmente para empresa/admin/operador.

## Minha recomendacao de implementacao

Implementar em cima da vaga existente, sem criar um modulo paralelo de processo seletivo para vagas. Em outras palavras:

- vaga continua sendo a origem da oportunidade
- candidatura vira subfluxo da vaga
- termo continua sendo o fechamento operacional da vaga

Essa abordagem aproveita o que o sistema ja tem, reduz risco de regressao e deixa o fluxo mais natural para empresa, operador e estagiario.