# Melhorias na Geração Manual de Avaliações

**Data:** 14 de janeiro de 2026

## Mudanças Implementadas

### 1. Mensagens de Erro Específicas

Agora quando você tenta gerar uma avaliação manualmente e não é permitido, o sistema exibe mensagens claras e específicas:

#### Termo Rescindido (para avaliação de 6 meses)
- **Mensagem:** "Este termo foi rescindido. Apenas avaliações de finalização podem ser criadas para termos rescindidos."
- **Quando ocorre:** Ao tentar criar avaliação de 6 meses em termo rescindido

#### Termo Finalizado/Expirado
- **Mensagem:** "Este termo já foi finalizado em DD/MM/YYYY. Não é possível criar avaliação de [tipo]."
- **Quando ocorre:** Termo passou da data de fim

#### Avaliação Pendente Duplicada
- **Mensagem:** "Já existe uma avaliação de [tipo] pendente para este termo. Por favor, finalize ou exclua a avaliação existente antes de criar uma nova."
- **Quando ocorre:** Já existe avaliação do mesmo tipo pendente

#### Avaliação Respondida Duplicada (Aviso)
- **Mensagem:** "Atenção: Já existe(m) X avaliação(ões) de 6 meses respondida(s) para este termo."
- **Tipo:** Aviso (warning) - permite criar, mas alerta
- **Quando ocorre:** Já existem avaliações de 6 meses respondidas

### 2. Permissão para Avaliação de Finalização em Termos Rescindidos

**Regra Anterior:**
- Não era possível criar nenhum tipo de avaliação em termos rescindidos

**Nova Regra:**
- ✅ **Avaliações de Finalização:** Podem ser criadas em termos rescindidos
- ❌ **Avaliações de 6 Meses:** Continuam bloqueadas em termos rescindidos

**Justificativa:**
Quando um termo é rescindido, é importante poder avaliar o desempenho do estagiário durante o período que trabalhou, mesmo que o contrato tenha sido encerrado antes do previsto.

## Exemplos de Uso

### Cenário 1: Termo Rescindido - Criar Avaliação de Finalização
```
1. Acessar termo rescindido
2. Clicar em "Gerar Avaliação Manual"
3. Selecionar "Finalização"
4. ✅ Avaliação criada com sucesso
```

### Cenário 2: Termo Rescindido - Tentar Criar Avaliação de 6 Meses
```
1. Acessar termo rescindido
2. Clicar em "Gerar Avaliação Manual"
3. Selecionar "6 Meses"
4. ❌ Erro: "Este termo foi rescindido. Apenas avaliações de finalização..."
```

### Cenário 3: Termo com Avaliação Pendente
```
1. Acessar termo que já tem avaliação pendente
2. Clicar em "Gerar Avaliação Manual"
3. Selecionar mesmo tipo
4. ❌ Erro: "Já existe uma avaliação de [tipo] pendente..."
```

### Cenário 4: Criar Segunda Avaliação de 6 Meses
```
1. Acessar termo com avaliação de 6 meses já respondida
2. Clicar em "Gerar Avaliação Manual"
3. Selecionar "6 Meses"
4. ⚠️ Aviso: "Atenção: Já existe(m) X avaliação(ões)..."
5. ✅ Permite criar, mas alerta sobre duplicação
```

## Tipos de Mensagens

### Success (Verde)
- Ícone: ✓ (check-circle)
- Quando: Ação realizada com sucesso

### Error (Vermelho)
- Ícone: ⊗ (exclamation-circle)
- Quando: Ação bloqueada/impedida

### Warning (Amarelo)
- Ícone: ⚠ (exclamation-triangle)
- Quando: Ação permitida mas requer atenção

## Arquivos Modificados

1. **AvaliacaoController.php**
   - Método `gerarManual()`: Lógica de validação melhorada
   - Mensagens de erro específicas para cada cenário
   - Exceção para avaliações de finalização em termos rescindidos

2. **por-termo.blade.php**
   - Adicionado suporte para mensagens de warning
   - Ícones nos alertas para melhor visualização

3. **index.blade.php**
   - Adicionado suporte para mensagens de warning
   - Ícones nos alertas para melhor visualização

## Fluxo de Validação

```
┌─────────────────────────────┐
│ Solicitar Nova Avaliação    │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────┐
│ Tipo = Finalização?         │
└──────────┬──────────────────┘
           │
     ┌─────┴─────┐
     │           │
    Sim         Não
     │           │
     │           ▼
     │     ┌─────────────────┐
     │     │ Termo Ativo?    │
     │     └────┬────────────┘
     │          │
     │     ┌────┴────┐
     │    Sim       Não
     │     │         │
     │     │         └──► ERRO: Termo Inativo
     │     │
     │     ▼
     └────►┌─────────────────┐
           │ Já tem Pendente?│
           └────┬────────────┘
                │
           ┌────┴────┐
          Sim       Não
           │         │
           │         └──► ┌─────────────────┐
           │              │ Já tem Respondida│
           │              └────┬────────────┘
           │                   │
           │              ┌────┴────┐
           │             Sim       Não
           │              │         │
           │              ▼         ▼
           │         WARNING     SUCCESS
           │              │         │
           ▼              └─────────┘
      ERRO: Já              │
      Pendente              ▼
                    ┌───────────────┐
                    │ Criar Avaliação│
                    └───────────────┘
```

## Testes Recomendados

- [ ] Criar avaliação de finalização em termo rescindido
- [ ] Tentar criar avaliação de 6 meses em termo rescindido
- [ ] Tentar criar avaliação duplicada (pendente)
- [ ] Criar segunda avaliação de 6 meses (após primeira respondida)
- [ ] Tentar criar avaliação em termo expirado
- [ ] Verificar se mensagens aparecem corretamente
- [ ] Verificar se ícones estão sendo exibidos

## Notas Técnicas

- Validações ocorrem no controller antes de chamar o service
- Mensagens são passadas via sessão usando `with()`
- Suporte para 3 tipos de alertas: success, error, warning
- Ícones FontAwesome utilizados para melhor UX
