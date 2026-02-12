# Recuperar Acesso do Estagiario (Login)

## Objetivo
Permitir que estagiarios com cadastro existente consigam criar acesso quando nao lembram ou nao conseguem entrar na conta.

## Fluxo
1. Na tela de login, o estagiario abre o modal "Ja tenho cadastro, mas nao consigo acessar".
2. Informa o CPF e o sistema busca cadastros em `tb_estagiarios`.
3. Resultado:
   - Nenhum cadastro: exibe botao para novo cadastro (ajax).
   - Mais de um cadastro: orienta contato via WhatsApp.
   - Cadastro unico com usuario: informa o email ja cadastrado e orienta contato.
   - Cadastro unico sem usuario: exibe formulario para criar usuario (email + senha + confirmacao).

## Rotas
- `POST /estagiarios/buscar-cpf`
  - Retorna status `not_found`, `multiple`, `has_user` ou `can_create_user`.
- `POST /estagiarios/{id}/criar-usuario`
  - Cria usuario vinculado ao estagiario quando nao existe usuario.

## Observacoes
- O CPF e sanitizado antes da busca.
- Regras de senha seguem o mesmo padrao do cadastro de estagiario (min 8, maiuscula, minuscula, numero e especial).
- Em caso de email incorreto ou cadastro duplicado, o atendimento segue via WhatsApp.
