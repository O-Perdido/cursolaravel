# Exportação de Inscrições - Processos Seletivos

## Visão Geral
Sistema de exportação personalizada de inscrições de processos seletivos com suporte para filtros avançados e seleção customizável de colunas.

## Funcionalidades

### 1. Formatos de Exportação
- **PDF**: Formato paisagem (landscape) otimizado para tabelas largas ✅
- **Excel**: Formato .xlsx com estilização do sistema SIGE ✅

### 2. Filtros de Status
Permite filtrar inscrições por status antes da exportação:
- **Todos**: Exporta todas as inscrições (inscrito, deferido, indeferido)
- **Apenas Deferidos**: Exporta somente inscrições com status "deferido"
- **Apenas Indeferidos**: Exporta somente inscrições com status "indeferido"
- **Apenas Inscritos**: Exporta somente inscrições pendentes de análise

### 3. Seleção de Colunas
O usuário pode escolher quais colunas incluir na exportação:
- ✅ Nº Inscrição (número único gerado automaticamente)
- ✅ Nome Completo
- ✅ E-mail
- ✅ Telefone
- ❌ CPF (desmarcado por padrão por questões de privacidade)
- ✅ Curso
- ❌ Instituição de Ensino (desmarcado por padrão)
- ✅ Status
- ✅ Data da Inscrição

**Helpers de Seleção:**
- Botão "Selecionar Todas" - marca todas as colunas
- Botão "Limpar Seleção" - desmarca todas as colunas

## Fluxo de Uso

### Para o Usuário (Admin/Operador)
1. Acessar a página de inscrições de um processo seletivo
2. Clicar no botão "Exportar" no cabeçalho da página
3. No modal de exportação:
   - Escolher o formato (PDF ou Excel)
   - Selecionar o filtro de status desejado
   - Marcar/desmarcar as colunas que deseja incluir
4. Clicar em "Exportar"
5. O arquivo será baixado automaticamente

## Estrutura Técnica

### Controller
**Arquivo:** `app/Http/Controllers/ProcessoSeletivoController.php`

#### Método `exportarInscricoes()`
```php
public function exportarInscricoes(Request $request, $id)
```
**Responsabilidades:**
- Receber parâmetros do formulário (formato, filtro de status, colunas)
- Aplicar filtros na query de inscrições
- Direcionar para o método de exportação apropriado (PDF ou Excel)

**Parâmetros aceitos:**
- `format`: 'pdf' ou 'excel' (padrão: 'pdf')
- `status_filter`: 'todos', 'deferido', 'indeferido', 'inscrito' (padrão: 'todos')
- `colunas[]`: array com nomes das colunas selecionadas

#### Método `exportarInscricoesPDF()`
```php
private function exportarInscricoesPDF($processo, $inscricoes, $colunas, $statusFiltro)
```
**Responsabilidades:**
- Preparar dados para a view
- Carregar template Blade
- Configurar papel A4 landscape
- Gerar e retornar PDF para download

**Dados passados para a view:**
- `processo`: Objeto do processo seletivo
- `inscricoes`: Collection de inscrições filtradas
- `colunas`: Array com nomes das colunas selecionadas
- `colunasLabels`: Mapeamento de nomes técnicos para labels legíveis
- `statusFiltro`: Label do filtro aplicado
- `dataExportacao`: Data/hora formatada da exportação

### View PDF
**Arquivo:** `resources/views/processos-seletivos/exports/inscricoes-pdf.blade.php`

#### Características
- **Layout:** HTML/CSS inline para compatibilidade com DomPDF
- **Papel:** A4 Landscape (297mm x 210mm)
- **Fonte:** DejaVu Sans (suporte a caracteres especiais)
- **Tamanhos de Fonte:**
  - Título: 16px
  - Subtítulo: 12px
  - Cabeçalhos da tabela: 8px
  - Conteúdo: 8-9px
  - Rodapé: 7px

#### Seções
1. **Cabeçalho**: Título + nome do processo
2. **Info Box**: Dados do processo, período, filtro aplicado, data de exportação
3. **Tabela**: Colunas dinâmicas baseadas na seleção do usuário
4. **Total**: Box destacado com contagem de inscrições
5. **Rodapé**: Informações de geração automática

#### Estilização
- Cores principais: `#dc3545` (vermelho Bootstrap danger)
- Zebra striping nas linhas da tabela
- Status badges coloridos:
  - Inscrito: amarelo (`#ffc107`)
  - Deferido: verde (`#28a745`)
  - Indeferido: vermelho (`#dc3545`)

### Rota
**Arquivo:** `routes/web.php`
```php
Route::post('/processos-seletivos/{id}/inscricoes/exportar', 
    [ProcessoSeletivoController::class, 'exportarInscricoes'])
    ->name('processos-seletivos.exportar-inscricoes');
```

### View do Modal
**Arquivo:** `resources/views/processos-seletivos/inscricoes.blade.php`

#### Elementos do Formulário
```html
<form action="{{ route('processos-seletivos.exportar-inscricoes', $processo->id_processo) }}" method="POST">
    @csrf
    <!-- Radio buttons para formato -->
    <input type="radio" name="format" value="pdf" checked>
    <input type="radio" name="format" value="excel">
    
    <!-- Select para filtro de status -->
    <select name="status_filter">
        <option value="todos">Todos</option>
        <option value="deferido">Deferidos</option>
        <option value="indeferido">Indeferidos</option>
        <option value="inscrito">Inscritos</option>
    </select>
    
    <!-- Checkboxes para colunas -->
    <input type="checkbox" name="colunas[]" value="numero_inscricao" checked>
    <input type="checkbox" name="colunas[]" value="nome" checked>
    <!-- ... demais colunas ... -->
</form>
```

#### JavaScript Helpers
```javascript
function selecionarTodas() {
    document.querySelectorAll('input[name="colunas[]"]').forEach(cb => cb.checked = true);
}

function desselecionarTodas() {
    document.querySelectorAll('input[name="colunas[]"]').forEach(cb => cb.checked = false);
}
```

## Dependências

### Composer (PHP)
- **barryvdh/laravel-dompdf** `^3.1`: Geração de PDFs
  - Status: ✅ Instalado e configurado
  - Documentação: https://github.com/barryvdh/laravel-dompdf

### Futuras (Excel)
- **maatwebsite/excel** `^3.1`: Exportação Excel
  - Status: ⏳ A ser implementado
  - Documentação: https://docs.laravel-excel.com/

## Exemplo de Uso

### Cenário 1: Exportar todos os deferidos com dados básicos
1. Formato: PDF
2. Filtro: Apenas Deferidos
3. Colunas: Nº Inscrição, Nome, E-mail, Status, Data Inscrição
4. Resultado: PDF com 5 colunas contendo apenas inscritos deferidos

### Cenário 2: Exportar relatório completo
1. Formato: PDF
2. Filtro: Todos
3. Colunas: Todas selecionadas
4. Resultado: PDF com 9 colunas contendo todas as inscrições

### Cenário 3: Lista de contato dos indeferidos
1. Formato: PDF
2. Filtro: Apenas Indeferidos
3. Colunas: Nome, E-mail, Telefone
4. Resultado: PDF compacto com dados de contato

## Padrões de Nomenclatura

### Arquivos Exportados
Formato: `inscricoes_{titulo-processo}_{data-hora}.pdf`

Exemplo: `inscricoes_processo-trainee-2025_20250126_153045.pdf`

### Colunas (nomes técnicos)
- `numero_inscricao`
- `nome`
- `email`
- `telefone`
- `cpf`
- `curso`
- `instituicao`
- `status`
- `data_inscricao`

## Validações e Tratamento de Erros

### Backend
- ✅ Validação de processo existente (404 se não encontrado)
- ✅ Default para 'pdf' se formato não especificado
- ✅ Default para 'todos' se filtro não especificado
- ✅ Default para todas as colunas se nenhuma selecionada
- ✅ Query otimizada com eager loading (`->with(['estagiario'])`)

### Frontend
- ✅ Campo status_filter marcado como required
- ✅ Radio buttons com valor padrão (PDF)
- ✅ Checkboxes principais marcados por padrão
- ✅ Visual claro com ícones e cores

### Tratamento de Dados Ausentes
```php
{{ $inscricao->estagiario->nome_completo ?? 'N/A' }}
```
Todas as colunas tratam dados nulos com `?? 'N/A'` para evitar erros.

## Performance

### Otimizações Aplicadas
1. **Eager Loading**: `.with(['estagiario'])` previne N+1 queries
2. **Filtro no Banco**: Query já retorna apenas registros necessários
3. **Fontes Web-Safe**: DejaVu Sans é rápida para renderizar
4. **CSS Inline**: Evita lookups externos no PDF

### Estimativas
- Até 100 inscrições: < 1 segundo
- 100-500 inscrições: 1-3 segundos
- 500+ inscrições: 3-5 segundos

### Futuras Melhorias
- Adicionar gráficos no Excel
- Exportação em CSV
- Agendamento de exportações automáticas

## Troubleshooting

### Problema: PDF com layout quebrado
**Solução:** Verificar se há caracteres especiais não suportados pela fonte DejaVu Sans. Considerar usar a fonte padrão do DomPDF.

### Problema: Timeout em processos com muitas inscrições
**Solução:** Aumentar `max_execution_time` no php.ini ou adicionar chunk processing.

### Problema: Colunas vazias (N/A em todos os registros)
**Solução:** Verificar relacionamento `estagiario` no model e eager loading no controller.

### Problema: Botão de exportação não responde
**Solução:** Verificar se o formulário está dentro do modal e se a rota POST está correta.

## Manutenção

### Adicionar Nova Coluna
1. Atualizar array `$colunasLabels` no controller
2. Adicionar checkbox no modal da view
3. Adicionar `@if` na view do PDF
4. Atualizar este README

### Modificar Estilo do PDF
1. Editar CSS inline em `inscricoes-pdf.blade.php`
2. Testar com diferentes quantidades de dados
3. Validar em landscape e portrait se necessário

## Referências
- [Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)
- [DomPDF Documentation](https://github.com/dompdf/dompdf)
- [Bootstrap 5 Icons](https://icons.getbootstrap.com/)
