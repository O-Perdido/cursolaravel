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
# Segredo do Webhook (opcional para validação de segurança HMAC-SHA256)
NOTAAS_WEBHOOK_SECRET=INSIRA_SEU_WEBHOOK_SECRET_AQUI
```

Substitua `INSIRA_SUA_API_KEY_AQUI` pela chave copiada no Passo 1.

---

## Passo 3: Configurar o Webhook no Painel do Notaas

Para atualizar automaticamente o status das notas fiscais sem precisar clicar em "Sincronizar Status":

1. Acesse o painel da Notaas: [https://platform.notaas.com.br](https://platform.notaas.com.br)
2. Vá em **Configurações / Integrações / Webhooks** ou seção equivalente.
3. Cadastre um novo endpoint de webhook apontando para:
   `https://seu-dominio.com/webhooks/notaas`
4. Selecione os eventos que deseja receber (recomendado selecionar todos os eventos de NFS-e, especialmente `nfse.issued`, `nfse.documents_ready`, `nfse.error` e `nfse.cancelled`).
5. Copie o **Segredo/Secret** gerado e insira no seu arquivo `.env` no campo `NOTAAS_WEBHOOK_SECRET`.

---

## Passo 4: Limpar o cache de configuração (Se necessário)

Se o seu sistema Laravel estiver rodando em produção ou com cache de configuração ativo, execute o comando abaixo no terminal da raiz do projeto para aplicar as novas variáveis do `.env`:

```bash
php artisan config:clear
```

---

## Funcionamento no Sistema

Ao acessar o **Painel de Notas Fiscais** ou os detalhes de qualquer **Folha de Pagamento**:

1. Você pode emitir notas (ligadas a folhas ou avulsas).
2. O webhook atualiza automaticamente o banco de dados (status, PDF, XML, erros) de forma assíncrona assim que a SEFAZ ou prefeitura retornar.
3. A sincronização manual através do botão **Sincronizar Status** continua disponível como um método de segurança.
