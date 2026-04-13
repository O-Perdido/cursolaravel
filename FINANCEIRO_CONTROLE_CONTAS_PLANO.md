# Modulo Financeiro - Controle de Contas (Plano de Implementacao)

## 1) Objetivo

Criar um novo modulo financeiro dentro do SIGE Estagios para substituir a planilha do Google Sheets, com foco inicial em controle de contas.

Escopo inicial:
- Controle de receitas e despesas por mes.
- Consolidado acumulado no ano.
- Acesso apenas para usuario admin.
- Entrada no mesmo menu onde hoje existe o atalho de Configuracoes.

## 2) O que foi entendido da planilha atual

Fluxo atual da planilha:
1. Aba CADASTRO: cadastro de nomes de receitas e despesas.
2. Abas JAN..DEZ: em cada linha o usuario seleciona uma conta cadastrada e informa valor.
3. Aba ACUMULADO: soma anual por conta, mostrando totais de receitas, despesas e saldo.

Pontos positivos preservados:
- Simplicidade do cadastro de contas.
- Reuso das contas em todos os meses.
- Visao mensal e visao acumulada.

Pontos a melhorar no sistema:
- Evitar duplicidade e erros de digitacao comuns em planilha.
- Melhor rastreabilidade (quem alterou, quando alterou).
- Padronizar validacoes e formato monetario.
- Facilitar filtros por ano/mes sem depender de abas.

## 3) Proposta de reformulacao no SIGE

### 3.1 Conceito

Em vez de abas, usar uma estrutura por Ano + Mes:
- Tela de Cadastro de Contas (equivale a aba CADASTRO).
- Tela de Lancamentos Mensais (equivale as abas JAN..DEZ).
- Tela de Acumulado Anual (equivale a aba ACUMULADO).

### 3.2 Entidades (MVP)

1. Conta Financeira
- Tipo: Receita ou Despesa.
- Nome da conta.
- Status ativo/inativo.
- Ordem de exibicao.

2. Lancamento Financeiro
- Ano de referencia.
- Mes de referencia.
- Conta selecionada.
- Valor.
- Observacao opcional.
- Usuario criador e usuario da ultima alteracao.

3. Resumo (calculado)
- Total de receitas no mes.
- Total de despesas no mes.
- Saldo no mes.
- Acumulado anual por conta e por tipo.

## 4) Proposta de Banco de Dados (seguindo convencoes do projeto)

Observacao importante: validar no contexto do banco atual se as FKs devem ou nao ser unsigned antes de migrar, pois o projeto possui padrao misto.

### 4.1 Tabela de contas
- Nome sugerido: tb_financeiro_contas
- PK: id_financeiro_conta
- Campos principais:
  - tipo_conta (receita|despesa)
  - nome_conta
  - ativo (1/0)
  - ordem_exibicao
  - created_at / updated_at

### 4.2 Tabela de lancamentos
- Nome sugerido: tb_financeiro_lancamentos
- PK: id_financeiro_lancamento
- FKs:
  - fk_id_financeiro_conta
  - fk_id_usuario_criacao
  - fk_id_usuario_atualizacao
- Campos principais:
  - ano_referencia
  - mes_referencia
  - valor
  - observacao (nullable)
  - created_at / updated_at

### 4.3 Regras de integridade
- Evitar valor negativo no MVP (valor sempre positivo e o sinal vem do tipo da conta).
- Validar ano e mes obrigatorios.
- Restringir exclusao fisica de conta se houver lancamentos (preferir inativacao).

## 5) Permissoes e acesso

Meta solicitada: modulo exclusivamente admin.

Aplicacao no SIGE:
- Rotas protegidas por auth + nivel:admin.
- Item no menu de usuario no mesmo bloco de Configuracoes.
- Item no sidebar apenas para admin, ao lado/abaixo de Configuracoes.

Importante:
- Mesmo que o link apareca por condicao de tela, a seguranca principal fica no middleware de rota.

## 6) UX proposta (simples e melhor que planilha)

### 6.1 Tela 1 - Contas
- Lista separada por Receita e Despesa.
- Criar, editar, inativar, reordenar.
- Busca rapida por nome.

### 6.2 Tela 2 - Lancamentos Mensais
- Filtros no topo: Ano e Mes.
- Duas secoes: Receitas e Despesas.
- Botao "Adicionar linha" com select de conta + valor + observacao.
- Totais do mes em cards:
  - Total de Receitas
  - Total de Despesas
  - Saldo (receitas - despesas)

### 6.3 Tela 3 - Acumulado Anual
- Filtro por Ano.
- Tabela consolidada por conta com colunas:
  - Conta
  - Tipo
  - Total no ano
- Cards de resumo anual (receitas, despesas, saldo).

## 7) Melhorias em relacao a planilha

- Sem duplicar cadastro entre abas.
- Sem risco de quebrar formula manual.
- Controle de acesso por perfil admin.
- Historico auditavel por usuario/data.
- Possibilidade futura de exportar (Excel/PDF) sem retrabalho estrutural.

## 8) Roadmap de implementacao (fases)

### Fase 1 - Estrutura base
- Migrations das tabelas.
- Models com table e primaryKey explicitos.
- Rotas admin do modulo financeiro.
- Controller inicial e views base.

### Fase 2 - Cadastro de contas
- CRUD de contas.
- Validacoes e inativacao.

### Fase 3 - Lancamentos mensais
- CRUD de lancamentos por ano/mes.
- Cards de totais mensais.

### Fase 4 - Acumulado anual
- Consultas agregadas por conta/tipo.
- Tela de consolidado anual.

### Fase 5 - Qualidade
- Testes Pest (happy path + edge) para regras principais.
- Revisao de permissao de acesso.
- Documentacao final do modulo.

## 9) Criterios de aceite do MVP

1. Apenas admin acessa qualquer rota financeira.
2. Admin consegue cadastrar contas de receita e despesa.
3. Admin consegue lancar valores por mes e ano usando select de conta.
4. Sistema calcula total de receitas, total de despesas e saldo mensal.
5. Sistema mostra acumulado anual por conta e resumo geral.

## 10) Fora do escopo inicial (deixar para evolucao)

- Multiempresa por unidade concedente.
- Fluxo de aprovacao de lancamentos.
- Importacao automatica de extrato bancario.
- Integracao com remessa bancaria/CNAB.
- Dashboard avancado com comparativo mensal.

## 11) Sugestao de nome e rotas do modulo

Nome de menu: Financeiro

Rotas sugeridas:
- GET /financeiro
- GET /financeiro/contas
- POST /financeiro/contas
- PUT /financeiro/contas/{id}
- GET /financeiro/lancamentos
- POST /financeiro/lancamentos
- PUT /financeiro/lancamentos/{id}
- DELETE /financeiro/lancamentos/{id}
- GET /financeiro/acumulado

## 12) Proximo passo recomendado

Com esta proposta aprovada, iniciar pela Fase 1 e Fase 2 para entregar rapidamente valor real (cadastro de contas + base pronta para lancamentos).

## 13) Andamento da implementacao

### Concluido nesta etapa
- Fase 1 - Estrutura base.
- Fase 2 - Cadastro de contas.
- Fase 3 - CRUD de lancamentos mensais.
- Fase 4 - Acumulado anual com tabela pivot (conta x mes).

### O que ja foi implementado
- Rotas admin do modulo financeiro (12 rotas no total).
- Item Financeiro no menu de admin e no sidebar de admin.
- Dashboard inicial do modulo.
- Migrations de contas e lancamentos.
- Migrations executadas com sucesso no ambiente local.
- Models de contas e lancamentos.
- CRUD completo de contas financeiras.
- CRUD completo de lancamentos mensais (FinanceiroLancamentoController).
  - store: cria lancamento com usuario de criacao automatico.
  - update: edicao inline na tabela, preserva ano/mes.
  - destroy: exclusao com confirmacao, retorna ao mesmo periodo.
- View de lancamentos com formulario de adicao inline e edicao/exclusao por linha.
- Cards de totais mensais (receitas, despesas, saldo).
- Tela de acumulado anual com tabela pivot completa (conta x mes JAN-DEZ):
  - Secao Receitas, secao Despesas, linha de saldo por mes.
  - Totais mensais e total anual por conta.
  - Saldo com cor dinamica (verde/vermelho).

### Pendente para a proxima fase
- Fase 5 - Qualidade: testes Pest para regras financeiras (base desativada no workspace).
- Melhoria opcional: seeder com contas da planilha original (ESTAGIARIOS, IMPOSTOS, INSS para despesas; cidades para receitas).

### Observacao tecnica
- A base de testes do workspace esta desativada no momento (arquivos de teste principais estao reduzidos a stubs), entao a implementacao foi validada por rotas, compilacao e migration local.