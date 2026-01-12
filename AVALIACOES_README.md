# Módulo de Avaliação de Estágio

## Visão Geral

O módulo de Avaliação de Estágio permite que operadores do sistema gerenciem avaliações de desempenho dos estagiários. As avaliações são geradas automaticamente quando os termos completam 6 meses de estágio ou ao serem finalizados (rescisão).

## Funcionalidades Principais

### 1. **Geração Automática de Avaliações**
- Avaliações de 6 meses são geradas automaticamente quando um termo atinge 6 meses de duração
- Avaliações de finalização podem ser geradas manualmente ao rescindir um termo
- Apenas termos ativos (sem rescisão) geram avaliações automáticas
- O sistema executa a geração automática diariamente às 02:00 via scheduled task

### 2. **Geração Manual**
- Operadores podem gerar avaliações manualmente para qualquer termo ativo
- Suporta dois tipos: "6 Meses" ou "Finalização"
- Sistema previne a criação de duplicatas do mesmo tipo em status "pendente"

### 3. **Link de Compartilhamento**
- Cada avaliação recebe um token único de compartilhamento
- Operador clica em "Compartilhar Link" para gerar a URL
- Link é copiado para a área de transferência
- Operador envia o link para o supervisor responder via email ou WhatsApp

### 4. **Resposta de Avaliação**
- Acesso público sem autenticação via token único
- Supervisor preenche questões em escala (1-5) e texto livre
- Validação de email do supervisor para registro
- Link é invalidado após o envio da avaliação
- Feedback de sucesso ao supervisor

### 5. **Gerenciamento de Avaliações**
- Listagem com filtros (busca, tipo de avaliação)
- Visualização de avaliações respondidas
- Limpeza/reset de avaliações respondidas para nova resposta
- Exclusão de avaliações
- Listagem por termo específico

### 6. **Notificações**
- Badge na navbar mostra número de avaliações pendentes
- Atualiza em tempo real

## Estrutura Técnica

### Model: `Avaliacao`
```php
- id_avaliacao (PK)
- fk_id_termo (FK → tb_termos)
- fk_id_supervisor (FK → tb_supervisores)
- tipo_avaliacao (enum: 'seis_meses', 'finalizacao')
- status (enum: 'pendente', 'respondida', 'revisada')
- token_compartilhamento (unique, nullable)
- questoes_respostas (JSON)
- respondida_em (datetime)
- respondida_por (email)
- created_at, updated_at
```

### Controller: `AvaliacaoController`
**Métodos Principais:**
- `index()` - Listagem com filtros
- `show()` - Visualização de uma avaliação
- `porTermo()` - Avaliações de um termo específico
- `gerarLinkCompartilhamento()` - Gera token e retorna URL
- `responder()` - Página de resposta (público)
- `salvarRespostas()` - Persiste respostas
- `gerarManual()` - Criação manual por operador
- `limpar()` - Reset de avaliação respondida
- `destroy()` - Exclusão
- `contadorPendentes()` - Retorna count para navbar

### Service: `AvaliacaoService`
**Responsabilidades:**
- `obterQuestoesBase()` - Retorna array de questões padrão
- `criarAvaliacao()` - Factory para criar nova avaliação
- `termoEstaAtivo()` - Verifica se termo pode gerar avaliações
- `atingiuSeisMeses()` - Valida se termo completou 6 meses
- `gerarAvaliacoesAutomaticas()` - Chamado pelo Job diário

### Job: `GerarAvaliacoesAutomaticasJob`
- Executa diariamente às 02:00
- Itera termos ativos que completaram 6 meses
- Cria avaliação se não existir "pendente" desse tipo
- Log de resultado em `storage/logs`

### Rotas

**Rotas Autenticadas (Admin/Operador):**
```
GET    /avaliacoes                          → avaliacoes.index (listagem)
GET    /avaliacoes/{avaliacao}              → avaliacoes.show (visualização)
POST   /avaliacoes/{avaliacao}/link-compartilhamento → avaliacoes.gerar-link
GET    /avaliacoes/termo/{termo}            → avaliacoes.por-termo
POST   /avaliacoes/gerar-manual             → avaliacoes.gerar-manual
POST   /avaliacoes/{avaliacao}/limpar       → avaliacoes.limpar
DELETE /avaliacoes/{avaliacao}              → avaliacoes.destroy
GET    /avaliacoes/contador/pendentes       → avaliacoes.contador
```

**Rotas Públicas (Sem Autenticação):**
```
GET    /avaliacoes/responder/{token}        → avaliacoes.responder
POST   /avaliacoes/salvar-respostas/{token} → avaliacoes.salvar-respostas
GET    /avaliacoes/sucesso                  → sucesso
```

### Views

1. **`avaliacoes/index.blade.php`**
   - Listagem de avaliações pendentes
   - Filtros: busca, tipo
   - Paginação: 15 por página
   - Botões: Ver, Termo, Link, Limpar, Excluir

2. **`avaliacoes/por-termo.blade.php`**
   - Avaliações de um termo específico
   - Informações do termo em card
   - Grid de avaliações
   - Modal para gerar avaliação manual

3. **`avaliacoes/show.blade.php`**
   - Visualização completa de uma avaliação
   - Dados do termo, estagiário, supervisor
   - Questões e respostas
   - Botões: Compartilhar, Limpar, Ver Outras, Excluir

4. **`avaliacoes/responder.blade.php`**
   - Formulário de resposta (público)
   - Design responsivo
   - Questões em escala ou texto
   - Validação no cliente/servidor

5. **`avaliacoes/acesso-negado.blade.php`**
   - Página de acesso negado
   - Motivos possíveis

6. **`avaliacoes/sucesso.blade.php`**
   - Confirmação após envio
   - Próximos passos

## Questões da Avaliação

As questões são armazenadas em JSON e incluem:

```json
[
  {
    "id": 1,
    "questao": "Como você avalia o desempenho geral do estagiário?",
    "tipo": "texto_longo",
    "ordem": 1,
    "resposta": ""
  },
  {
    "id": 2,
    "questao": "O estagiário demonstra conhecimento técnico adequado?",
    "tipo": "escala_1_5",
    "ordem": 2,
    "resposta": ""
  },
  // ... mais questões
]
```

**Tipos de Questões:**
- `texto_longo` - textarea, resposta livre
- `escala_1_5` - radio buttons (Insuficiente até Excelente)

## Fluxo de Uso

### Fluxo do Operador

1. Acessa `/avaliacoes`
2. Vê avaliações pendentes
3. Clica em "Link" para compartilhar
4. Copia URL e envia para supervisor

### Fluxo do Supervisor (Público)

1. Recebe email/WhatsApp com link
2. Clica no link
3. Visualiza `avaliacoes/responder`
4. Preenche questões
5. Clica "Enviar Avaliação"
6. Vê página de sucesso
7. Link expira automaticamente

### Fluxo de Limpeza

1. Operador visualiza avaliação respondida
2. Clica "Limpar para Nova Resposta"
3. Status volta a "pendente"
4. Novo token gerado
5. Pode compartilhar novamente

## Autorização

- Apenas `admin` e `operador` podem acessar seções protegidas
- Resposta de avaliação é pública (via token)
- Middleware `nivel:admin,operador` protege rotas

## Segurança

1. **Token Seguro**: gerado com `bin2hex(random_bytes(32))`
2. **Expiração Automática**: link invalida após resposta
3. **CSRF**: formulários incluem `@csrf`
4. **Email de Validação**: armazena quem respondeu
5. **Sem Login Necessário**: acesso seguro via token único

## Migration

Executar:
```bash
php artisan migrate
```

Isso criará a tabela `tb_avaliacoes` com índices otimizados.

## Scheduled Task

O Job `GerarAvaliacoesAutomaticasJob` é agendado no `Kernel.php`:

```php
$schedule->job(new GerarAvaliacoesAutomaticasJob())->dailyAt('02:00');
```

Para testar manualmente:
```bash
php artisan schedule:run
```

## Customização de Questões

Para alterar as questões padrão, edite o método `obterQuestoesBase()` em `AvaliacaoService`:

```php
public function obterQuestoesBase(): array
{
    return [
        [
            'id' => 1,
            'questao' => 'Sua pergunta aqui',
            'tipo' => 'texto_longo', // ou 'escala_1_5'
            'ordem' => 1,
            'resposta' => '',
        ],
        // ... adicione mais
    ];
}
```

## Troubleshooting

### Avaliações não são geradas automaticamente
- Verificar se `php artisan schedule:work` está rodando
- Verificar logs em `storage/logs/laravel.log`
- Confirmar que termos têm `data_inicio_estagio` preenchida

### Link de compartilhamento não funciona
- Verificar se `token_compartilhamento` está preenchido no BD
- Confirmar se status é "pendente"
- Checar se URL está correta

### Contador de avaliações não atualiza
- Limpar cache: `php artisan cache:clear`
- Verificar se middleware está correto

## Performance

- Índices em: `fk_id_termo`, `fk_id_supervisor`, `status`, `token_compartilhamento`, `tipo_avaliacao`
- Paginação: 15 por página
- JSON para questões mantém flexibilidade
- Query: com `with(['termo', 'supervisor'])` para evitar N+1

## Melhorias Futuras

1. **Email Automático**: enviar link por email automaticamente
2. **Relatórios**: gráficos de avaliações por estagiário/empresa
3. **Versionamento**: manter histórico de alterações
4. **Aprovação**: adicionar etapa de revisão (status "revisada")
5. **Lembretes**: notificar supervisor se não responder em X dias
