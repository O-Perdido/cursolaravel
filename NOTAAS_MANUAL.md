# Manual de Configuração - NFS-e Notaas

Este manual explica como configurar as credenciais da API **Notaas** no sistema para habilitar a emissão de notas fiscais eletrônicas de serviço (NFS-e) nas folhas de pagamento.

---

## Passo 1: Obter a API Key no Painel do Notaas

1. Acesse sua conta na plataforma Notaas: [https://platform.notaas.com.br](https://platform.notaas.com.br)
2. No menu lateral ou configurações da organização, navegue até a seção de **API Keys**.
3. Crie ou copie a sua API Key (ex: `pk_live_...` ou `pk_test_...`).
   - *Nota: Em ambiente de testes, utilize a chave de homologação (sandbox).*

---

## Passo 2: Configurar o arquivo `.env`

Abra o arquivo [`.env`](file:///c:/Users/Vinicius - Contratos/Documents/GitHub/cursolaravel/.env) do projeto na raiz e adicione/atualize as seguintes linhas ao final do arquivo:

```env
# Notaas API Configuration
NOTAAS_API_KEY=INSIRA_SUA_API_KEY_AQUI
NOTAAS_API_URL=https://platform.notaas.com.br/api/v1
```

Substitua `INSIRA_SUA_API_KEY_AQUI` pela chave copiada no Passo 1.

---

## Passo 3: Limpar o cache de configuração (Se necessário)

Se o seu sistema Laravel estiver rodando em produção ou com cache de configuração ativo, execute o comando abaixo no terminal da raiz do projeto para aplicar as novas variáveis do `.env`:

```bash
php artisan config:clear
```

---

## Funcionamento no Sistema

Ao acessar a tela de detalhes de qualquer **Folha de Pagamento** ([show.blade.php](file:///c:/Users/Vinicius - Contratos/Documents/GitHub/cursolaravel/resources/views/folhas_pagamento/show.blade.php)):

1. Um novo painel **Nota Fiscal Eletrônica (NFS-e via Notaas)** estará disponível.
2. Caso a nota não tenha sido emitida, o botão **Emitir NFS-e via Notaas** abrirá um modal interativo.
3. No modal, selecione a opção de valor desejada:
   - **Apenas Taxa Administrativa** (padrão preenchido).
   - **Total da Folha**.
   - **Ambos (Taxa Adm + Total Folha)**.
   - **Valor Customizado**.
4. A nota será enfileirada. Clique em **Sincronizar Status** para consultar o processamento pela Notaas e liberar os links para download do **PDF** e do **XML** autorizados da NFS-e.
