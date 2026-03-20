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