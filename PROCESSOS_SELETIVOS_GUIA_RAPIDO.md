# Guia Rápido - Processos Seletivos de Estagiários

## 🚀 Como Começar

Este guia mostra como usar o novo módulo de Processos Seletivos.

### Atualização de listagem (23/02/2026)

- As listagens de processos (público, estagiário e gestão) agora abrem **sem filtro de status** por padrão.
- A ordenação padrão passou a ser por **data de abertura/lançamento**, do mais recente para o mais antigo.
- Se necessário, o usuário pode aplicar filtro manual por status na tela.

---

## 👨‍💼 Para Admin/Operador

### 1. Acessar o Módulo

Na navbar superior, clique em **"Processos Públicos"** para acessar a listagem de todos os editais.

### 2. Criar um Novo Processo

Clique em **"Novo Processo"** ou siga: `Processos Públicos > Novo Processo`

#### Campos a preencher:

**Informações Básicas:**
- **Título**: Nome do processo seletivo (ex: "Processo Seletivo 2026 - Analista de Sistemas")
- **Empresa**: Selecione a concedente (autofillado se você for empresa)
- **Status**: Escolha entre rascunho, aberto, inscrições, encerrado, finalizado

**Datas:**
- **Data de Abertura**: Quando o processo começa
- **Data de Fechamento de Inscrições**: Quando as inscrições encerram

**Descrição:**
- **Fases do Processo**: Descreva como será o processo seletivo
- **Cursos Destinados**: Liste os cursos (um por linha)
- **Requisitos**: Quais são os requisitos necessários
- **Observações**: Qualquer informação adicional

**Aviso para Inscrição:**
- Um texto personalizado que aparecerá quando o estagiário clicar em "Se Inscrever"
- Deixe em branco para usar mensagem padrão

**Arquivos do Edital:**
- **Nome para Exibição**: Ex: "Edital", "Retificação 1", etc
- **Tipo**: Escolha entre edital, retificação, resultado, outro
- **Arquivo**: O PDF, Word ou Excel do edital
- Pode adicionar múltiplos arquivos clicando em "Adicionar Arquivo"

### 3. Editar um Processo

Clique no botão ✏️ (editar) na linha do processo. O formulário é idêntico ao de criação.

### 4. Gerenciar Inscrições

Clique no botão 👥 (inscrições) para ver todos os estagiários inscritos.

**Nesta tela você pode:**
- Ver nome, email, telefone, curso do estagiário
- Ver o status (inscrito, deferido, indeferido)
- Marcar como deferido (✅) ou indeferido (❌)
- Exportar a lista em PDF ou Excel (placeholder)

### 5. Publicar Resultados

Clique em 📄 (resultados) para gerenciar os resultados do processo.

**Para publicar:**
1. Clique em "Publicar Resultado"
2. Dê um nome/número para o resultado (ex: "Resultado Final")
3. Faça upload do arquivo (opcional)
4. Clique em "Publicar"

Os estagiários verão os resultados em "Minhas Inscrições"

### 6. Deletar um Processo

Clique no botão 🗑️ (lixo) para deletar. Será solicitado confirmação.

**⚠️ Atenção:** Deletar um processo remove também todas as inscrições e resultados!

---

## 👨‍🎓 Para Estagiário

### 1. Acessar os Processos

Na página inicial, você verá um novo card chamado **"Processos Seletivos"** com dois botões:

- **"Ver Processos Abertos"** - Ver editais disponíveis
- **"Minhas Inscrições"** - Acompanhar suas inscrições

### 2. Buscar um Processo

Clique em "Ver Processos Abertos"

Você verá uma lista de **cards** com as informações principais:
- 🏢 Logo e nome da empresa
- 📋 Título do processo
- 🏷️ Número interno
- 📊 Status (Aberto, Inscrições, Encerrado, etc)
- 🎓 Número de cursos
- 📅 Data de fechamento

Clique no card ou em "Ver Detalhes" para mais informações.

### 3. Ver Detalhes do Processo

Nesta tela você encontra:

**Na esquerda:**
- Todas as informações do processo
- Fases do processo
- Cursos destinados
- Requisitos
- Observações
- Arquivos do edital para download

**Na direita (Sidebar):**
- Se você já está inscrito (com aviso)
- Ou botão "Se Inscrever"
- Link para "Minhas Inscrições"

### 4. Se Inscrever

Clique em **"Se Inscrever"**

Uma janela (modal) aparecerá mostrando:
- Um aviso personalizado (definido pelo operador)
- Botões de "Cancelar" e "Confirmar Inscrição"

Clique em **"Confirmar Inscrição"** para se inscrever.

✅ Se der sucesso, você verá uma mensagem de sucesso!

### 5. Acompanhar suas Inscrições

Clique em **"Minhas Inscrições"** para ver:

**Cada inscrição mostra:**
- Nome do processo
- Empresa
- Status da inscrição:
  - 🔵 **Inscrito** - Aguardando resultado
  - 🟢 **Deferido** - Você passou!
  - 🔴 **Indeferido** - Infelizmente não passou
- Data da inscrição
- Data de fechamento
- Botões para:
  - Ver detalhes do processo novamente
  - Baixar resultados (se publicados)

---

## 📊 Exemplo de Fluxo Completo

### Criando um Edital:

1. **Admin clica** em "Processos Públicos"
2. **Clica** em "Novo Processo"
3. **Preenche:**
   - Título: "Processo Seletivo 2026 - Desenvolvimento"
   - Empresa: "Acme Corp"
   - Status: "rascunho"
   - Data de Abertura: 20/01/2026 10:00
   - Data de Fechamento: 31/01/2026 23:59
   - Fases: "Fase 1: Avaliação de CV, Fase 2: Dinâmica, Fase 3: Entrevista"
   - Cursos: "Análise e Desenvolvimento de Sistemas"
   - Requisitos: "Estar cursando, conhecimento em Java"
   - Aviso: "Leia com atenção as fases do processo antes de se inscrever"
4. **Faz upload** de 2 arquivos:
   - "Edital" (edital.pdf)
   - "Retificação 1" (retificacao1.pdf)
5. **Clica** em "Salvar Processo"

### Estagiário Se Inscreve:

1. **Estagiário** vê novo processo nos cards
2. **Clica** em "Ver Detalhes"
3. **Lê** todas as informações e fases
4. **Baixa** o edital para ler em casa
5. **Clica** em "Se Inscrever"
6. **Lê** o aviso: "Leia com atenção as fases do processo antes de se inscrever"
7. **Clica** em "Confirmar Inscrição"
8. ✅ **Inscrição realizada!**

### Admin Publica Resultado:

1. **Admin** clica em "Processos Públicos"
2. **Clica** em "Resultados" no processo
3. **Clica** em "Publicar Resultado"
4. **Preenche:**
   - Número: "Resultado Final"
   - Arquivo: resultado_final.pdf
5. **Clica** em "Publicar"

### Estagiário Vê Resultado:

1. **Estagiário** clica em "Minhas Inscrições"
2. **Vê** o processo com status "Deferido" (com ícone verde)
3. **Clica** em "Baixar Resultados"
4. **Vê** o arquivo "Resultado Final"
5. **Baixa** o arquivo

---

## ⚙️ Configurações Importantes

### Status do Processo:

- **Rascunho**: Processo não visível para estagiários
- **Aberto**: Visível mas sem inscrições
- **Inscrições**: Visível e com inscrições ativas
- **Encerrado**: Visível mas inscrições fechadas
- **Finalizado**: Visível com resultados publicados

### Datas:

As datas são fundamentais! Se a data de fechamento de inscrições passar:
- Estagiários não conseguem mais se inscrever
- Mas podem ver o processo e os resultados

### Arquivos:

- Pode adicionar múltiplos arquivos
- Cada arquivo tem um "nome para exibição" que aparece na tela
- Útil para: edital, retificações, normas, etc

---

## 💡 Dicas

1. **Sempre defina datas**: Sem datas, o sistema não consegue validar o período
2. **Use avisos personalizados**: Assim o estagiário já sabe o que esperar
3. **Organize os arquivos**: Use nomes claros como "Edital", "Retificação 1"
4. **Revise antes de publicar**: Mude para "rascunho" se precisar editar
5. **Exporte inscrições regularmente**: Para fazer análises e filtros

---

## ❓ Troubleshooting

### Estagiário não consegue se inscrever

**Possíveis causas:**
- Já está inscrito no processo
- O período de inscrições fechou
- O processo está em status "rascunho"

**Solução:** Verifique a data de fechamento e o status do processo

### Arquivo não faz upload

**Possíveis causas:**
- Arquivo muito grande (máximo 10MB)
- Formato não suportado

**Solução:** Tente com um arquivo menor em PDF ou DOC

### Modal de inscrição não aparece

**Possível causa:**
- JavaScript desativado no navegador

**Solução:** Habilite JavaScript nas configurações do navegador

---

## 📞 Suporte

Para dúvidas ou problemas, consulte:
- Documentação técnica: `PROCESSOS_SELETIVOS_DESIGN.md`
- Implementação: `PROCESSOS_SELETIVOS_IMPLEMENTACAO.md`
- Admin do sistema
