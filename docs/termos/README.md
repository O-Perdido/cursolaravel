# Termos - Edicao TCE

## Visao Geral
- Admin pode editar um termo completo a partir da pagina de detalhes.
- A edicao atualiza campos fixos e variaveis, seguindo os mesmos dados do cadastro.
- Ao salvar, o sistema exige confirmacao de senha do usuario logado.

## Fluxo de Edicao
1. Acesse detalhes do termo.
2. Clique em "Editar TCE" (apenas admin).
3. Ajuste os campos necessarios.
4. Clique em salvar e confirme a senha.

## Regras Importantes
- Somente admin tem acesso ao formulario de edicao.
- A senha e validada no backend antes de persistir alteracoes.
- Se o termo estiver vinculado a uma vaga, a vinculacao e atualizada conforme o novo valor.

## Rotas
- GET /termos/{id}/edit (admin)
- PUT /termos/{id} (admin)

