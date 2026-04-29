# Melhoria: Status Ativa/Inativa para Instituicoes de Ensino

## Objetivo
Controlar se uma instituicao de ensino aparece na selecao de geracao de termo por meio de status Ativa/Inativa.

## Regras aplicadas
- Novo atributo: `ativo` em `tb_escolas`.
- Valor padrao para base existente: `ativo = true`.
- Cadastro/edicao de IE com seletor intuitivo `Ativa/Inativa`.
- Na geracao de novo termo: somente IEs ativas aparecem.
- Na edicao de termo existente: IEs ativas aparecem e a IE atualmente vinculada ao termo permanece visivel mesmo se estiver inativa.

## Critérios de aceite cobertos
- IE inativa nao aparece para selecao em novo termo.
- IE ativa volta a aparecer na selecao.
- Registros antigos nao sao afetados (default ativo na migration).

## Arquivos alterados
- `database/migrations/2026_04_29_000003_add_ativo_to_tb_escolas.php`
- `app/Models/Escola.php`
- `app/Http/Controllers/EscolaController.php`
- `app/Http/Controllers/TermoController.php`
- `resources/views/escolas/create.blade.php`
- `resources/views/escolas/edit.blade.php`

## Observacao
- Nenhuma alteracao foi feita em PDFs para esta melhoria.
