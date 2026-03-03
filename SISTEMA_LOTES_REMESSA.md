# Sistema de Divisão de Remessas Bancárias em Lotes

## 📋 Visão Geral

Este sistema implementa uma solução híbrida (automática + manual) para dividir folhas de pagamento grandes em múltiplos arquivos de remessa bancária, respeitando o limite diário de transações do banco.

## 🎯 Problema Resolvido

**Situação:** Folhas de pagamento com muitos estagiários (ex: mais de 200) podem ultrapassar o limite diário de valor permitido pelo banco, causando rejeição do arquivo de remessa.

**Solução:** Divisão automática da folha em múltiplos lotes, com possibilidade de ajuste manual antes de gerar os arquivos.

## 🚀 Como Usar

### 1. Configurar o Limite Diário (Admin)

1. Acesse o menu **Configurações** (disponível apenas para administradores)
2. Defina o **Limite Diário para Remessas** (ex: R$ 50.000,00)
3. Clique em **Salvar Configurações**

**Caminho:** `/configuracoes`

### 2. Preparar Remessa de uma Folha

1. Acesse **Folhas de Pagamento**
2. Na linha da folha desejada, clique no botão verde **📄 Preparar Remessa**
3. O sistema irá:
   - Buscar o limite configurado
   - Dividir automaticamente os pagamentos em lotes
   - Mostrar um preview dos lotes

### 3. Visualizar e Gerar Lotes

Na tela de **Preparar Remessa**, você verá:

- **Informações Gerais:**
  - Valor total da folha
  - Limite diário configurado
  - Total de estagiários

- **Status:**
  - ✅ Verde: folha dentro do limite (1 arquivo)
  - ⚠️ Amarelo: folha excede o limite (múltiplos arquivos)

- **Lotes:**
  - Cada lote mostra: número, quantidade de estagiários e valor total
  - Clique em "Ver detalhes" para expandir a lista completa
  - Botão **💾 Gerar Arquivo** para download do arquivo `.REM`

### 4. Gerar Arquivos de Remessa

1. Clique em **💾 Gerar Arquivo 1**, **💾 Gerar Arquivo 2**, etc.
2. Cada arquivo será baixado com nomenclatura sequencial:
   - `CI240_001_0000001.REM`
   - `CI240_002_0000002.REM`
   - `CI240_003_0000003.REM`

3. Envie cada arquivo para o banco em dias diferentes (se necessário)

## 📊 Lógica de Divisão

O sistema divide os pagamentos seguindo estas regras:

1. **Ordenação:** Mantém a ordem original dos estagiários
2. **Acumulação:** Soma os valores até atingir o limite
3. **Quebra:** Quando adicionar o próximo pagamento ultrapassar o limite, cria um novo lote
4. **Último lote:** Pode ter valor inferior ao limite (resto dos pagamentos)

### Exemplo

**Limite:** R$ 50.000,00  
**Folha total:** R$ 120.000,00 (200 estagiários)

**Resultado:**
- Lote 1: R$ 49.850,00 (83 estagiários)
- Lote 2: R$ 49.920,00 (84 estagiários)
- Lote 3: R$ 20.230,00 (33 estagiários)

## 🔧 Arquivos Criados/Modificados

### 1. **Migration** - Tabela de Configurações
`database/migrations/2025_11_10_000000_create_configuracoes_table.php`
- Cria tabela `configuracoes`
- Insere configuração padrão do limite (R$ 50.000,00)

### 2. **Model** - Configuração
`app/Models/Configuracao.php`
- Métodos para obter/definir configurações
- `obterLimiteDiarioRemessa()`: retorna o limite configurado

### 3. **Controller** - Configurações
`app/Http/Controllers/ConfiguracaoController.php`
- `index()`: exibe formulário de configurações
- `update()`: salva alterações (apenas admin)

### 4. **Controller** - Folha de Pagamento (modificado)
`app/Http/Controllers/FolhaPagamentoController.php`

**Novos métodos:**
- `prepararRemessa($id_folha_pagamento)`: divide em lotes e mostra preview
- `dividirEmLotes($items, $limiteValor)`: algoritmo de divisão
- `gerarRemessaLote(Request $request, $id_folha_pagamento)`: gera arquivo de um lote específico
- `gerarRemessaComItens($folha, $conteudoFolha, $numeroLote)`: método auxiliar para gerar remessa

### 5. **Views**

**Configurações:**
`resources/views/configuracoes/index.blade.php`
- Interface para admin configurar limite diário

**Preparar Remessa:**
`resources/views/folhas_pagamento/preparar_remessa.blade.php`
- Preview dos lotes
- Detalhes expansíveis de cada lote
- Botões para gerar arquivos individuais

**Index (modificado):**
`resources/views/folhas_pagamento/index.blade.php`
- Adicionado botão "Preparar Remessa" na coluna de ações

### 6. **Rotas**
`routes/web.php`

**Novas rotas:**
```php
// Configurações (admin)
Route::get('/configuracoes', [ConfiguracaoController::class, 'index']);
Route::post('/configuracoes', [ConfiguracaoController::class, 'update']);

// Preparar e gerar remessa em lotes
Route::get('/folha-pagamento/preparar-remessa/{id}', [FolhaPagamentoController::class, 'prepararRemessa']);
Route::post('/folha-pagamento/remessa-lote/{id}', [FolhaPagamentoController::class, 'gerarRemessaLote']);
```

## 💡 Dicas de Uso

### Para Administradores
- Configure o limite baseado nas regras do seu banco
- Monitore se o limite está adequado (não gera lotes demais)
- Ajuste conforme necessário em Configurações

### Para Operadores
- Sempre use "Preparar Remessa" em vez do botão antigo
- Verifique o preview antes de gerar os arquivos
- Baixe todos os lotes necessários de uma vez
- Anote quais arquivos foram enviados e em quais datas

### Para Folhas Grandes
- Considere enviar os lotes em dias diferentes
- Organize por ordem alfabética ou prioridade se necessário
- Mantenha registro de qual lote contém quais estagiários

## 🔐 Permissões

| Ação | Admin | Operador | Empresa |
|------|-------|----------|---------|
| Configurar limite | ✅ | ❌ | ❌ |
| Preparar remessa | ✅ | ✅ | ❌ |
| Gerar arquivos | ✅ | ✅ | ❌ |

## ⚠️ Observações Importantes

1. **Validações mantidas:** O sistema continua validando chaves PIX antes de gerar os arquivos
2. **Formato CNAB240:** Mantido o padrão do Banco Inter
3. **Numeração sequencial:** Cada lote recebe um número único no nome do arquivo
4. **Compatibilidade:** O método antigo `gerarRemessa()` permanece funcional

### Validação PIX (atualização 03/03/2026)

- O sistema não gera mais chave de telefone com preenchimento em zeros (ex.: `+5500000000000`).
- Em caso de chave PIX inválida (telefone com quantidade incorreta de dígitos, e-mail inválido, CPF inválido, chave aleatória vazia), a remessa é bloqueada.
- A tela retorna uma lista com os estagiários pendentes para correção antes de novo download do `.REM`.
- O campo **Forma de Iniciação** do Segmento B foi ajustado para os códigos do manual (`01`, `02`, `03`, `04`) em campo alfa de 3 posições.
- Para tipo `03` (CPF), a **Chave Pix (pos. 128-226)** permanece em branco, e o CPF/CNPJ vai no campo próprio (pos. 19-32).
- O final do Segmento B passou a ser preenchido explicitamente com: `227-232` em branco e `233-240` (ISPB) numérico.
- Foi adicionada validação preventiva para e-mail PIX que, ao extrair apenas dígitos, resulte em zeros (`^0+$`), padrão que pode ser rejeitado pelo validador do banco.

### Retorno de erro detalhado para o usuário

- Quando houver pendências de chave PIX, a geração da remessa é bloqueada com uma mensagem explicativa.
- O retorno mostra tabela com: número da ocorrência, ID do registro, nome do estagiário e problema identificado.
- Para casos de incompatibilidade bancária (ex.: e-mail PIX com dígitos somente zero), a mensagem orienta que o cadastro precisa ser ajustado antes de nova geração do arquivo.

## 🛠️ Instalação

Para instalar este recurso em produção:

```bash
# 1. Rodar a migration
php artisan migrate

# 2. Verificar se a configuração foi criada
# Acesse /configuracoes e confirme o valor padrão

# 3. Limpar cache (opcional)
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📞 Suporte

Em caso de dúvidas ou problemas:
1. Verifique se a migration foi executada
2. Confirme se o limite está configurado em Configurações
3. Teste com uma folha pequena primeiro
4. Verifique os logs em `storage/logs/laravel.log`

---

**Versão:** 1.0.0  
**Data:** 10/11/2025  
**Desenvolvido por:** Sistema de Gestão de Estágios
