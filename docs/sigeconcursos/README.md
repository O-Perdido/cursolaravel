# SIGE Concursos

## Etapa inicial implementada

Esta primeira etapa cria a base estrutural do módulo SIGE Concursos dentro da aplicação atual, mantendo separação visual e organizacional do módulo de estágios.

## O que foi criado

- Rotas prefixadas com `sigeconcursos`
- Dashboard própria para admin e operador
- Views separadas em `resources/views/sigeconcursos`
- Navegação contextual na navbar quando o usuário está dentro do módulo
- Atalho no menu `Opções` do módulo atual para abrir o SIGE Concursos

## Rotas iniciais

- `/sigeconcursos/dashboard`
- `/sigeconcursos/processos`
- `/sigeconcursos/orgaos`
- `/sigeconcursos/candidatos`

## Observações

- O acesso inicial está restrito a usuários com nível `admin` ou `operador`
- As páginas de processos, órgãos/empresas e candidatos foram criadas como base navegável para as próximas etapas
- O layout principal é reaproveitado, mas a navbar troca de contexto dentro do módulo SIGE Concursos