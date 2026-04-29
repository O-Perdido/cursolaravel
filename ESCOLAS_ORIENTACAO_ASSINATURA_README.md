# Melhoria: Orientacao de Assinatura na Instituicao de Ensino

## Objetivo
Adicionar um campo textual de orientacao de assinatura no cadastro de instituicao de ensino (Escola), com foco em casos em que a instituicao nao assina via ZapSign.

## Regra de negocio
- Campo: `orientacao_assinatura`
- Obrigatoriedade: obrigatorio somente quando `nao_assina_zapsign = true`
- Quando `nao_assina_zapsign = false`, o campo permanece opcional

## Entrega tecnica
- Banco:
  - Migration adicionando `orientacao_assinatura` (text nullable) em `tb_escolas`
- Back-end:
  - `Escola` com campo em `fillable`
  - `EscolaController@store` e `EscolaController@update` com validacao condicional (`required_if`)
  - Persistencia do valor no create/update
- Front-end:
  - Campo no create/edit de instituicoes
  - Exibicao no show de instituicao
  - Aviso discreto na parte de ZapSign para:
    - Termo (TCE)
    - Alteracao de Termo (TAE)
    - Rescisao (TRE)

## Arquivos alterados
- `database/migrations/2026_04_29_000002_add_orientacao_assinatura_to_tb_escolas.php`
- `app/Models/Escola.php`
- `app/Http/Controllers/EscolaController.php`
- `resources/views/escolas/create.blade.php`
- `resources/views/escolas/edit.blade.php`
- `resources/views/escolas/show.blade.php`
- `resources/views/termos/index.blade.php`
- `resources/views/termos/show.blade.php`
- `resources/views/termos/alteracoes/index.blade.php`

## Observacoes
- O campo usa `textarea` para permitir instrucoes mais completas.
- A exibicao no ZapSign foi feita de forma nao invasiva (bloco pequeno `alert-info` com texto da orientacao).
