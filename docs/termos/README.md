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
- A vaga vinculada nao pode ser editada; para mudar a vaga, e necessario excluir o termo e criar outro.
- O botao de editar so fica habilitado se o termo nao estiver rescindido e nao tiver alteracoes.

## Reverter Rescisao
- Admin pode reverter uma rescisao diretamente na tela de detalhes do termo.
- Ao reverter, a rescisao e excluida e a data final do termo volta para:
	- a ultima alteracao de data (se existir), ou
	- a data fixa original (data_fim_estagio_fixo).

## Rotas
- GET /termos/{id}/edit (admin)
- PUT /termos/{id} (admin)
- POST /termos/{id}/reverter-rescisao (admin)

