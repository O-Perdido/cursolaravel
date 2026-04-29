# Remocao de Campos de Seguro no Cadastro de Instituicao de Ensino

## Objetivo
Remover os campos "numero da apolice" e "nome da seguradora" do fluxo de cadastro/edicao de instituicao de ensino, incluindo a camada de persistencia usada pelo formulario.

## Escopo aplicado
- Removido da UI de cadastro da instituicao.
- Removido da validacao no backend (store).
- Removido do mapeamento de persistencia no backend (store).
- Removido de fillable do model Escola para impedir escrita via mass assignment.
- Removido da tela de detalhes da instituicao.

## Fora de escopo (mantido)
- Texto fixo no PDF de termo que cita apolice/seguro foi mantido sem alteracao.
- Estrutura da tabela no banco (colunas historicas) nao foi alterada nesta entrega.

## Arquivos alterados
- app/Models/Escola.php
- app/Http/Controllers/EscolaController.php
- resources/views/escolas/create.blade.php
- resources/views/escolas/show.blade.php

## Resultado esperado
- Instituicao salva normalmente sem informar os campos de seguro.
- Edicao e listagem continuam funcionando sem dependencias desses campos.
