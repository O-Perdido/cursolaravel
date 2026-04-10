# SIGE Concursos

## Etapa inicial implementada

Esta primeira etapa cria a base estrutural do módulo SIGE Concursos dentro da aplicação atual, mantendo separação visual e organizacional do módulo de estágios.

## O que foi criado

- Rotas prefixadas com `sigeconcursos`
- Dashboard própria para admin e operador
- Views separadas em `resources/views/sigeconcursos`
- Navegação contextual na navbar quando o usuário está dentro do módulo
- Atalho no menu `Opções` do módulo atual para abrir o SIGE Concursos
- CRUD inicial de órgãos públicos/empresas do módulo
- Migration e tabela própria `sigeconcursos_tb_empresas`

## Rotas iniciais

- `/sigeconcursos/dashboard`
- `/sigeconcursos/processos`
- `/sigeconcursos/orgaos`
- `/sigeconcursos/candidatos`

## CRUD de órgãos públicos/empresas

- Model: `App\Models\SigeConcursoEmpresa`
- Controller: `App\Http\Controllers\SigeConcursoEmpresaController`
- Tabela: `sigeconcursos_tb_empresas`
- Views:
	- `sigeconcursos/orgaos/index`
	- `sigeconcursos/orgaos/create`
	- `sigeconcursos/orgaos/edit`
	- `sigeconcursos/orgaos/show`

### Campos implementados

- nome/razão social
- CNPJ
- telefone
- celular
- e-mail
- CEP
- endereço
- número
- complemento
- bairro
- cidade vinculada à `tb_cidade`
- nome do representante
- cargo do representante
- CPF do representante
- dados bancários

### Regras aplicadas

- máscaras de CNPJ, CPF, telefone, celular e CEP
- validação de CNPJ e CPF no front e no backend
- persistência dos documentos e telefones somente com dígitos
- listagem com filtros por nome/razão social, CNPJ e e-mail

## Observações

- O acesso inicial está restrito a usuários com nível `admin` ou `operador`
- As páginas de processos, órgãos/empresas e candidatos foram criadas como base navegável para as próximas etapas
- O layout principal é reaproveitado, mas a navbar troca de contexto dentro do módulo SIGE Concursos

## Distribuição por salas

- A distribuição automática por salas usa somente a quantidade mínima de salas necessária para comportar os candidatos de cada local.
- Depois de definir quantas salas serão usadas, o sistema reparte os candidatos de forma balanceada entre elas, mantendo diferença máxima de 1 candidato entre salas sempre que a capacidade permitir.
- A ordem de alocação continua alfabética dentro de cada local e a capacidade máxima de cada sala continua sendo respeitada.

## Exportação da homologação

- A tela de homologação de inscrições permite exportar a listagem atual em PDF ou Excel.
- Os arquivos respeitam exatamente os filtros ativos na tela, incluindo nome, CPF, modalidade, status da inscrição, status da isenção e status do pagamento.
- O operador pode decidir se o CPF será exportado de forma censurada ou completa.
- O relatório foi pensado para apoiar publicação de listas de deferidos e indeferidos e também servir de base para listas operacionais, como conferência e lançamento posterior de notas.

## Lista de presença por sala

- Após a distribuição por salas, a tela disponibiliza um PDF de lista de presença por sala.
- O arquivo gera uma página por sala, com cabeçalho do local, identificação da sala e relação nominal dos candidatos em ordem de assento.
- A lista foi desenhada para uso operacional no dia da prova, incluindo espaço para conferência de documento, assinatura e observações de recepção.