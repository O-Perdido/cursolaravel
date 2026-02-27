# Atualização de documentos por Admin/Operador

## Problema relatado
Ao editar estagiário pela tela administrativa (`/estagiario/{id}/edit`), os documentos enviados nem sempre eram atualizados corretamente.

## Causa identificada
No método `EstagiarioController@update`:
- Limite de arquivo estava baixo (2MB), causando falhas frequentes em PDFs digitalizados.
- Fluxo de substituição de arquivo era sensível a falhas (ordem de operações sem proteção transacional adequada).
- Em falhas de armazenamento/persistência, faltava rollback completo e limpeza consistente dos novos arquivos.

## Correções aplicadas (27/02/2026)
1. Aumentado limite de upload dos 3 documentos para **5MB** (`max:5120`).
2. Implementado fluxo seguro:
   - upload dos novos arquivos em memória de paths temporários;
   - transação de banco para update de dados + paths;
   - exclusão de arquivos antigos somente após commit;
   - rollback e limpeza dos novos arquivos em exceções.
3. Adicionado log de erro específico para facilitar diagnóstico:
   - mensagem: `Falha ao atualizar estagiário com documentos (admin/operator)`.

## Arquivos impactados
- `app/Http/Controllers/EstagiarioController.php` (método `update`)

## Resultado esperado
Admin/operador consegue atualizar dados e documentos do estagiário de forma confiável, com menor incidência de falhas silenciosas e sem risco de perder o arquivo antigo em caso de erro no meio do processo.
