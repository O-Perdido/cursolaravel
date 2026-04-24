# Filtro de Assinatura ZapSign na Listagem de Termos

## Objetivo
Adicionar um filtro na tela de listagem de termos para separar rapidamente contratos por situação da assinatura eletrônica (ZapSign).

## Onde foi aplicado
- Listagem de termos para `admin` e `operador`.
- Listagem de termos para `empresa`.
- Relatório em PDF da listagem de termos (mantém o mesmo filtro).
- Exportação Excel da listagem de termos (mantém o mesmo filtro).

## Novo parâmetro de filtro
Parâmetro GET: `status_assinatura`

Valores suportados:
- `nao_enviado`: termos sem envio para assinatura (sem `zapsign_doc_token`).
- `pendente`: termos enviados/aguardando assinatura.
- `assinado`: termos concluídos/assinados.
- `nao_assinado`: qualquer termo que ainda não esteja assinado.

## Regras de mapeamento de status
### Assinado
Considera `zapsign_status` em:
- `finished`
- `signed`
- `concluded`
- `completed`

### Pendente
Considera `zapsign_status` em:
- `enviado`
- `pending`
- `waiting`
- `waiting_signature`
- `processing`
- `partially_signed`
- `partial`

## Arquivos alterados
- `app/Http/Controllers/TermoController.php`
- `app/Exports/TermosExport.php`
- `resources/views/termos/index.blade.php`

## Observação
O filtro de assinatura é independente do filtro de status do contrato (`ativos`, `rescindidos`, `vencidos`), podendo ser usado em conjunto.
