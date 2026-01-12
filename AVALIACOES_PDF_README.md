# Avaliações – Geração de PDF

Este documento descreve como o SIGE gera e armazena o PDF das avaliações após serem respondidas.

## Visão Geral
- Ao salvar as respostas via formulário público (token), o sistema:
  - Atualiza a avaliação para `respondida` e invalida o token.
  - O PDF é gerado sob demanda, sem salvar no servidor.
  - Admin/operador pode baixar o PDF diretamente pela rota protegida.

## Componentes
- Service: `App/Services/AvaliacaoPdfService.php` (usa `barryvdh/laravel-dompdf`).
- View (PDF): `resources/views/avaliacoes/pdf.blade.php`.
- Controller: `AvaliacaoController@pdf()` gera o PDF on-the-fly e devolve o download.
- Rota protegida: `GET /avaliacoes/{avaliacao}/pdf` para download por admin/operador.

## Pré-requisitos
- DomPDF instalado (via `barryvdh/laravel-dompdf`).

## Observações
- O PDF é básico e focado em legibilidade. Se desejar logo/cabeçalho institucional, sugerir assets em `public/images/`.
- Locale: pt-BR. Datas em `d/m/Y H:i`.

## Migração
- Removidos campos de PDF da tabela `tb_avaliacoes` (não armazenamos arquivos).

