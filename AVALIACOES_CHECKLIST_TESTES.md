# Checklist de Testes - Módulo de Avaliação

## ✅ Testes Básicos

### 1. Banco de Dados
- [ ] Tabela `tb_avaliacoes` foi criada corretamente
- [ ] Índices foram criados
- [ ] Foreign keys estão funcionando

### 2. Model Avaliacao
- [ ] Relação `termo()` funciona corretamente
- [ ] Relação `supervisor()` funciona corretamente
- [ ] Método `podeSerAcessada()` retorna true para pendentes
- [ ] Método `gerarTokenCompartilhamento()` gera tokens únicos

### 3. Service AvaliacaoService
- [ ] `obterQuestoesBase()` retorna array de 9 questões
- [ ] `criarAvaliacao()` cria avaliação com status "pendente"
- [ ] `termoEstaAtivo()` retorna false para termos com rescisão
- [ ] `atingiuSeisMeses()` valida corretamente a data
- [ ] `gerarAvaliacoesAutomaticas()` cria apenas 1 por tipo pendente

## ✅ Testes de Rotas (Admin/Operador)

### 4. GET /avaliacoes
- [ ] Listagem carrega corretamente
- [ ] Filtro por busca funciona
- [ ] Filtro por tipo_avaliacao funciona
- [ ] Paginação exibe corretamente
- [ ] Badge de contador exibe número correto

### 5. GET /avaliacoes/{avaliacao}
- [ ] Visualização carrega todas as informações
- [ ] Questões e respostas são exibidas
- [ ] Botões de ação aparecem corretamente

### 6. GET /avaliacoes/termo/{termo}
- [ ] Listagem por termo carrega corretamente
- [ ] Informações do termo aparecem
- [ ] Modal de gerar avaliação funciona

### 7. POST /avaliacoes/gerar-manual
- [ ] Cria avaliação do tipo selecionado
- [ ] Impede duplicatas do mesmo tipo pendente
- [ ] Redireciona para visualização após criação

### 8. POST /avaliacoes/{avaliacao}/link-compartilhamento
- [ ] Gera token único
- [ ] Retorna JSON com link correto
- [ ] Link é acessível publicamente

### 9. POST /avaliacoes/{avaliacao}/limpar
- [ ] Muda status de "respondida" para "pendente"
- [ ] Gera novo token
- [ ] Limpa questoes_respostas

### 10. DELETE /avaliacoes/{avaliacao}
- [ ] Exclui a avaliação do banco
- [ ] Redireciona corretamente

## ✅ Testes Públicos (Sem Autenticação)

### 11. GET /avaliacoes/responder/{token}
- [ ] Link válido abre formulário
- [ ] Link inválido mostra "acesso-negado"
- [ ] Link respondido mostra "acesso-negado"
- [ ] Carrega questões corretamente

### 12. POST /avaliacoes/salvar-respostas/{token}
- [ ] Aceita respostas válidas
- [ ] Valida email obrigatório
- [ ] Invalida token após resposta
- [ ] Muda status para "respondida"
- [ ] Redireciona para sucesso

### 13. GET /avaliacoes/sucesso
- [ ] Página de sucesso é exibida
- [ ] Contém mensagem apropriada

## ✅ Testes de UI/Frontend

### 14. Navbar
- [ ] Botão "Avaliações" aparece para admin/operador
- [ ] Badge mostra número de pendentes
- [ ] Clique leva para /avaliacoes

### 15. Listagem
- [ ] Modal de compartilhamento funciona
- [ ] Botão de copiar copia corretamente
- [ ] Formulário de filtro valida entrada

### 16. Responder
- [ ] Questões de escala mostram 5 opções
- [ ] Questões de texto mostram textarea
- [ ] Validação de email funciona
- [ ] Envio mostra indicador de carregamento
- [ ] Após envio redireciona para sucesso

## ✅ Testes de Segurança

### 17. Autorização
- [ ] Usuário não autenticado não pode acessar /avaliacoes
- [ ] Usuário estagiario não pode acessar /avaliacoes
- [ ] Usuário empresa não pode acessar /avaliacoes
- [ ] Resposta pública via token funciona sem auth

### 18. CSRF
- [ ] Formulários incluem @csrf
- [ ] POST sem CSRF token falha

### 19. Tokens
- [ ] Token é único por avaliação
- [ ] Token invalida após resposta
- [ ] Token não pode ser reutilizado

## ✅ Testes de Performance

### 20. Queries
- [ ] Listagem não tem problema N+1 (carrega com())
- [ ] Índices funcionam para filtros

### 21. Paginação
- [ ] 15 itens por página na listagem
- [ ] Links de paginação funcionam

## ✅ Testes de Agendamento

### 22. Scheduled Task
- [ ] Job está registrado no Kernel.php
- [ ] `php artisan schedule:run` executa sem erro
- [ ] Cria avaliação apenas se não existe pendente
- [ ] Log é registrado em storage/logs

## ✅ Testes Manuais

### 23. Fluxo Completo
1. [ ] Criar avaliação manual para um termo
2. [ ] Gerar link de compartilhamento
3. [ ] Abrir link em navegador anônimo
4. [ ] Preencher avaliação completamente
5. [ ] Enviar avaliação
6. [ ] Confirmar que link expira
7. [ ] Voltar ao operador e visualizar avaliação respondida
8. [ ] Limpar avaliação
9. [ ] Confirmar que novo link pode ser gerado

## 📝 Notas de Teste

- **Ambiente de Teste**: Usar dados fictícios, nunca dados reais
- **Logs**: Verificar `storage/logs/laravel.log` para erros
- **Database**: Usar `php artisan migrate:fresh --seed` para reset
- **Cache**: `php artisan cache:clear` se houver problemas
- **Tokens**: Cada token é único e seguro com 64 caracteres hexadecimais

## 🐛 Problemas Conhecidos

Se encontrar problemas:
1. Verificar se a migration foi executada: `php artisan migrate:status`
2. Verificar se as rotas estão registradas: `php artisan route:list | grep avaliacao`
3. Verificar logs: `tail -f storage/logs/laravel.log`
4. Testar com tinker: `php artisan tinker`

## 🚀 Deploy

Ao fazer deploy:
1. [ ] Executar migrations: `php artisan migrate`
2. [ ] Limpar cache: `php artisan cache:clear`
3. [ ] Registrar scheduled task (cron)
4. [ ] Testar acesso a /avaliacoes
5. [ ] Confirmar que links funcionam

