# Guia Rápido - Módulo de Avaliação

## 📋 Resumo

Implementação completa de um sistema de avaliação de desempenho para estagiários. Avaliações são geradas automaticamente quando termos completam 6 meses ou ao serem finalizados.

## 🚀 Início Rápido

### 1. Executar Migration
```bash
php artisan migrate
```

### 2. Testar Geração Manual (Operador)
1. Acesse `/avaliacoes` como admin ou operador
2. Clique em "Gerar Avaliação Manual"
3. Selecione o tipo (6 Meses ou Finalização)
4. Avaliação será criada

### 3. Compartilhar Avaliação
1. Na listagem, clique em "Link"
2. Copie a URL gerada
3. Envie para supervisor por email/WhatsApp

### 4. Supervisor Responde
1. Supervisor clica no link
2. Preenche o formulário
3. Envia a avaliação
4. Link expira automaticamente

## 📁 Arquivos Criados

```
app/
├── Models/
│   └── Avaliacao.php
├── Http/Controllers/
│   └── AvaliacaoController.php
├── Services/
│   └── AvaliacaoService.php
├── Jobs/
│   └── GerarAvaliacoesAutomaticasJob.php
└── Console/
    ├── Kernel.php
    └── Commands/
        └── GerarAvaliacoesAutomaticasCommand.php

database/
└── migrations/
    └── 2026_01_12_000000_create_tb_avaliacoes_table.php

database/seeders/
└── AvaliacaoSeeder.php

resources/views/avaliacoes/
├── index.blade.php (listagem)
├── por-termo.blade.php (por termo específico)
├── show.blade.php (visualização)
├── responder.blade.php (formulário público)
├── acesso-negado.blade.php
└── sucesso.blade.php

routes/
└── web.php (rotas adicionadas)

documentação/
├── AVALIACOES_README.md (completo)
└── AVALIACOES_CHECKLIST_TESTES.md (testes)
```

## 🔧 Configuração

### Agendamento Automático
Já está configurado no `app/Console/Kernel.php`:
```php
$schedule->job(new GerarAvaliacoesAutomaticasJob())->dailyAt('02:00');
```

Para testar manualmente:
```bash
php artisan avaliacoes:gerar-automaticas
```

### Customizar Questões
Editar `app/Services/AvaliacaoService.php` método `obterQuestoesBase()`:

```php
public function obterQuestoesBase(): array
{
    return [
        [
            'questao' => 'Sua pergunta aqui',
            'tipo' => 'texto_longo', // ou 'escala_1_5'
            // ...
        ],
    ];
}
```

## 📊 Campos da Avaliação

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id_avaliacao` | int | PK |
| `fk_id_termo` | int | FK para termo |
| `fk_id_supervisor` | int | FK para supervisor |
| `tipo_avaliacao` | enum | 'seis_meses' \| 'finalizacao' |
| `status` | enum | 'pendente' \| 'respondida' \| 'revisada' |
| `token_compartilhamento` | string | Único, 64 chars hex |
| `questoes_respostas` | json | Array de questões |
| `respondida_em` | datetime | Quando foi respondida |
| `respondida_por` | string | Email de quem respondeu |
| `created_at` | timestamp | Criação |
| `updated_at` | timestamp | Última atualização |

## 🔑 Endpoints Principais

### Protegidos (Auth + Admin/Operador)
- `GET /avaliacoes` - Listagem
- `GET /avaliacoes/{avaliacao}` - Ver avaliação
- `GET /avaliacoes/termo/{termo}` - Por termo
- `POST /avaliacoes/gerar-manual` - Criar manual
- `POST /avaliacoes/{avaliacao}/link-compartilhamento` - Gerar link
- `POST /avaliacoes/{avaliacao}/limpar` - Resetar avaliação
- `DELETE /avaliacoes/{avaliacao}` - Excluir

### Públicos (Sem Auth)
- `GET /avaliacoes/responder/{token}` - Formulário de resposta
- `POST /avaliacoes/salvar-respostas/{token}` - Enviar respostas
- `GET /avaliacoes/sucesso` - Página de sucesso

## 🎯 Casos de Uso

### Caso 1: Avaliação Automática
```
Termo criado em 01/01/2025
→ Job rodas diariamente às 02:00
→ Em 01/07/2025 (6 meses) cria avaliação pendente
→ Operador vê nova avaliação pendente
```

### Caso 2: Compartilhamento
```
Operador clica "Link"
→ Token gerado (ex: c6d23b48...)
→ URL gerada: /avaliacoes/responder/c6d23b48...
→ Copia e envia para supervisor
→ Supervisor acessa por 24h (até responder)
```

### Caso 3: Resposta
```
Supervisor acessa link
→ Preenche 9 questões
→ Envia com email
→ Sistema invalida token
→ Avaliação marcada como "respondida"
→ Link não funciona mais
```

### Caso 4: Revisão
```
Operador visualiza avaliação respondida
→ Vê todas as respostas
→ Pode "limpar" para nova resposta
→ Novo token gerado
→ Supervisor pode responder de novo
```

## 🔐 Segurança

✅ **Protegido contra:**
- Acesso não autenticado (rotas protegidas com middleware)
- CSRF (tokens @csrf em formulários)
- Múltiplas respostas (token invalida após resposta)
- Reutilização de token (único por avaliação)
- Força bruta (token é 64 caracteres aleatórios)

## 📈 Performance

✅ **Otimizações:**
- Índices em colunas de busca
- Eager loading com `with()`
- Paginação (15 itens por página)
- Sem problema N+1
- JSON para flexibilidade

## 🐛 Troubleshooting

### Avaliações não aparecem
```bash
php artisan migrate:status
php artisan migrate
```

### Links não funcionam
```bash
# Verificar se token existe
SELECT * FROM tb_avaliacoes WHERE status = 'pendente' LIMIT 1;
```

### Agendamento não funciona
```bash
# Verificar se scheduler rodando
php artisan schedule:work

# Testar manualmente
php artisan avaliacoes:gerar-automaticas
```

## 📚 Documentação Completa

- **AVALIACOES_README.md** - Documentação técnica completa
- **AVALIACOES_CHECKLIST_TESTES.md** - Casos de teste
- **REGISTRO_DE_ALTERAÇÕES.txt** - Histórico de mudanças

## 💡 Próximas Ideias

1. **Email Automático**: Enviar link por email automaticamente
2. **Lembretes**: Notificar se supervisor não responder em 7 dias
3. **Relatórios**: Gráficos de desempenho por estagiário
4. **Versionamento**: Manter histórico de alterações
5. **Aprovação**: Etapa de revisão por RH antes de finalizar
