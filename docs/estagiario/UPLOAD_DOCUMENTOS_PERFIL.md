# Upload de documentos no perfil do estagiário

## Contexto
Foi reportado comportamento intermitente no envio de documentos em **Meu Perfil** (falha durante anexação, queda/recarregamento e sucesso apenas após múltiplas tentativas).

## Rota e fluxo
- Rota: `POST /meu-perfil/documento`
- Controller: `EstagiarioController@atualizarDocumento`
- Campos suportados: `foto_documento`, `comprovante_residencia`, `comprovante_escolar`
- Limite atual de arquivo: 5MB (`max:5120`)

## Melhorias aplicadas (27/02/2026)
1. **Backend mais resiliente no upload**
   - Verificação de arquivo realmente recebido (`hasFile`) e válido (`isValid`).
   - Tratamento de exceções com `try/catch` para falhas de upload/armazenamento.
   - Uso de transação no update do banco para manter consistência.
   - Limpeza de arquivo novo em caso de falha de persistência.
   - Exclusão do arquivo antigo somente após sucesso completo da atualização.

2. **Feedback melhor na interface**
   - Validação client-side de tamanho máximo (5MB) antes do submit.
   - Mensagem imediata para ausência de arquivo.
   - Mensagem quando navegador estiver sem conexão (`navigator.onLine === false`).
   - Área de erro visual dentro do modal de atualização.

## Diagnóstico operacional recomendado
Quando houver nova reclamação de lentidão/falha:
1. Confirmar tamanho e formato do arquivo enviado (PDF/JPG/JPEG/PNG, até 5MB).
2. Reproduzir com rede estável (idealmente cabo/Wi-Fi confiável).
3. Conferir logs do Laravel para o erro `Falha ao atualizar documento do estagiário`.
4. Validar configuração de ambiente PHP/servidor (principalmente `upload_max_filesize`, `post_max_size`, `max_execution_time` e disponibilidade de disco).

## Observações
- O fluxo continua substituindo o documento anterior automaticamente após sucesso.
- Em caso de instabilidade de internet, o usuário agora recebe retorno mais claro para tentar novamente.
