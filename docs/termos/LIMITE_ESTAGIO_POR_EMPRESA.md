# Limite de Permanência de Estágio por Empresa

## Objetivo
Impedir cadastro/edição de termo que faça o estagiário ultrapassar o tempo máximo acumulado de estágio na mesma unidade concedente (empresa/CNPJ), conforme configuração global do sistema.

## Escopo da Regra
- Aplica para criação e edição de termo.
- Aplica também na criação de alteração de termo quando houver mudança em `data_fim_estagio_alteracao`.
- Considera somente termos do mesmo estagiário e da mesma empresa (`fk_id_empresa`).
- Não depende de local (`fk_id_local`).
- Bloqueia somente quando o novo total **excede** o limite configurado.

## Onde é Configurado
Tela: `Configurações do Sistema` > aba `Limite de Estágio`.

Chaves globais utilizadas na tabela `configuracoes`:
- `estagio_limite_empresa_modo` (`anos` ou `dias`)
- `estagio_limite_empresa_anos` (inteiro, padrão: 2)
- `estagio_limite_empresa_dias` (inteiro, padrão: 730)

## Modos de Cálculo
### 1) Modo `anos`
- Usa o valor configurado em anos.
- Converte para dias por base fixa de calendário no serviço (`Carbon`), para padronizar o cálculo.

### 2) Modo `dias`
- Usa diretamente o valor configurado em dias.

## Regra de Acúmulo
1. Busca termos com mesmo estagiário + mesma empresa.
2. Lê intervalos `[data_inicio_estagio, data_fim_estagio]` válidos.
3. Adiciona o intervalo do termo em criação/edição.
4. Consolida intervalos sobrepostos/contíguos para evitar dupla contagem.
5. Soma os dias acumulados e compara com o limite configurado.

## Pontos de Código
- Serviço de domínio:
  - `app/Services/LimiteEstagioPorEmpresaService.php`
- Rule de validação:
  - `app/Rules/LimiteEstagioPorEmpresaRule.php`
- Integração no fluxo de termo:
  - `app/Http/Controllers/TermoController.php`
- Integração no fluxo de alteração de termo:
  - `app/Http/Controllers/AlteracaoTermoController.php`
- Configurações globais:
  - `app/Models/Configuracao.php`
  - `app/Http/Controllers/ConfiguracaoController.php`
  - `resources/views/configuracoes/index.blade.php`

## Mensagem de Validação
Quando excede, o backend retorna erro informando:
- total acumulado em dias,
- limite configurado (valor e modo),
- equivalente em dias usado na comparação.
