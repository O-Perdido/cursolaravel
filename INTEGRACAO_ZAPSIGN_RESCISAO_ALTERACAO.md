# Integração ZapSign - Rescisões e Alterações de Termos

## 📅 Data da Implementação
8 de dezembro de 2025

## ✅ Funcionalidades Implementadas

### 1. Estrutura de Banco de Dados
**Migration:** `2025_12_08_000000_add_zapsign_fields_to_rescisao_and_alteracao_termo.php`

Adicionados campos nas tabelas:
- `tb_rescisao`:
  - `zapsign_doc_token` (varchar, nullable)
  - `zapsign_status` (varchar, nullable)
  - `zapsign_enviado_em` (timestamp, nullable)

- `tb_alteracao_termo`:
  - `zapsign_doc_token` (varchar, nullable)
  - `zapsign_status` (varchar, nullable)
  - `zapsign_enviado_em` (timestamp, nullable)

### 2. Models Atualizados
- **Rescisao**: Campos ZapSign adicionados ao `$fillable`
- **AlteracaoTermo**: Campos ZapSign adicionados ao `$fillable`

### 3. Controllers

#### RescisaoController
**Novo método:** `enviarParaZapSign($id)`
- Coleta termo relacionado à rescisão
- Prepara signatários completos:
  - Representantes da Empresa (Concedente)
  - Representantes da Escola (Instituição de Ensino)
  - Estagiário
  - Agente de Integração EBCP (Moacir Aguiar)
- Gera PDF com DomPDF usando view `gerarPdfRescisao`
- Converte PDF para base64 (seguro)
- Envia para ZapSign via `criarDocumentoBase64()`
- Posiciona assinaturas automaticamente na última página
- Persiste `doc_token`, `status` e `enviado_em` no banco

#### AlteracaoTermoController
**Novo método:** `enviarParaZapSign($id, $id_alteracao)`
- Mesma estrutura do RescisaoController
- Usa view `alteracoes.gerarPdfAlteracao`
- Signatários completos idênticos

### 4. Rotas Criadas
```php
// Rescisão
POST /rescisao/{id}/enviar-zapsign
Route name: rescisoes.enviarZapSign

// Alteração de Termo
POST /termos/{id}/alteracoes/{id_alteracao}/enviar-zapsign
Route name: alteracao.enviarZapSign
```

### 5. Webhook Adaptado
**Arquivo:** `ZapSignWebhookController.php`

Lógica atualizada para identificar 3 tipos de documentos:
1. Termo (original)
2. Rescisão
3. Alteração de Termo

O webhook busca o `doc_token` e atualiza o status automaticamente no registro correto.

### 6. Views Atualizadas

#### termos/show.blade.php
Adicionado ao lado do botão "PDF Rescisão":
- Botão "Enviar para ZapSign" (se não foi enviado)
- Badge com status ZapSign (se já foi enviado)

#### termos/alteracoes/index.blade.php
Adicionado na coluna de ações:
- Botão "ZapSign" para enviar (se não foi enviado)
- Badge com status (se já foi enviado)

#### PDFs com Suporte ZapSign

**gerarPdfRescisao.blade.php:**
- Verifica se `$paraZapSign` está ativo
- Exibe lista de signatários quando for para ZapSign
- Mantém assinaturas estáticas para download normal

**alteracoes/gerarPdfAlteracao.blade.php:**
- Mesma lógica de signatários dinâmicos
- Layout adaptado para 4 signatários

## 🎯 Padrões Seguidos

✅ **IDs:** Mantidos como `int(11)` (não unsigned) - consistente com banco existente  
✅ **Nomenclatura:** Padrão `tb_`, `id_`, `fk_id_` preservado  
✅ **Envio:** Via base64 (mais seguro que URL pública)  
✅ **Signatários:** Conjunto completo para validade jurídica  
✅ **Fluxos:** Independentes - rescisão/alteração não dependem do termo original  
✅ **Webhook:** Único endpoint reutilizado com lógica condicional  
✅ **Locale:** pt-BR mantido em todas as mensagens  

## 🚀 Como Usar

### Enviar Rescisão para ZapSign
1. Acesse a visualização do termo (`/termos/{id}/show`)
2. Se houver rescisão cadastrada, verá o botão "Enviar para ZapSign"
3. Clique e confirme o envio
4. Status será atualizado automaticamente pelo webhook

### Enviar Alteração para ZapSign
1. Acesse a lista de alterações do termo
2. Clique no botão "ZapSign" ao lado da alteração desejada
3. Confirme o envio
4. Status será atualizado via webhook

### Verificar Status
- O status aparece automaticamente nas views após envio
- Possíveis status: `enviado`, `link_aberto`, `assinado`, `concluido`, etc.

## 🔧 Arquivos Modificados

### Backend
- `database/migrations/2025_12_08_000000_add_zapsign_fields_to_rescisao_and_alteracao_termo.php` (novo)
- `app/Models/Rescisao.php`
- `app/Models/AlteracaoTermo.php`
- `app/Http/Controllers/RescisaoController.php`
- `app/Http/Controllers/AlteracaoTermoController.php`
- `app/Http/Controllers/ZapSignWebhookController.php`
- `routes/web.php`

### Frontend
- `resources/views/termos/show.blade.php`
- `resources/views/termos/alteracoes/index.blade.php`
- `resources/views/termos/gerarPdfRescisao.blade.php`
- `resources/views/termos/alteracoes/gerarPdfAlteracao.blade.php`

## 📝 Observações Importantes

1. **Signatários Fixos:**
   - EBCP: Moacir Aguiar (moacirecetista@hotmail.com)
   - Sempre incluído como Agente de Integração

2. **Posicionamento de Assinaturas:**
   - Layout em 2 colunas na última página
   - Espaçamento otimizado para evitar sobreposição
   - Gap horizontal de 30px entre colunas

3. **Webhook:**
   - Rota pública: `/webhooks/zapsign`
   - Identifica automaticamente o tipo de documento
   - Logs de auditoria em `ZapSignWebhookLog`

4. **Segurança:**
   - PDFs enviados via base64 (não expõe arquivos publicamente)
   - Token ZapSign protegido no `.env`
   - Webhook com verificação de secret opcional

## 🧪 Testado

✅ Migration executada sem erros  
✅ Models atualizados corretamente  
✅ Controllers sem erros de sintaxe  
✅ Rotas registradas  
✅ Views renderizando corretamente  

## 📚 Referências

- [ZapSign API Documentation](https://docs.zapsign.com.br)
- Lei nº 14.063/2020 (Assinaturas Digitais)
- Documentação interna: `ZAPSIGN_README.md`, `CONFIGURACAO_ZAPSIGN.md`
