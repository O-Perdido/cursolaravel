# 📸 Screenshots para Play Store

## Instruções para Capturar Screenshots

### Requisitos:
- Navegador Chrome no modo Mobile (F12 → Toggle Device)
- Resolução: **1080x1920 pixels** (portrait) ou **1920x1080** (landscape)
- Mínimo: 2 screenshots
- Recomendado: 4-8 screenshots
- Formato: PNG ou JPG

### Passo a Passo:

#### 1. Abrir Ferramentas de Desenvolvedor
```
1. Pressione F12 (ou Ctrl+Shift+I)
2. Clique no ícone "Toggle Device Toolbar" (Ctrl+Shift+M)
3. Selecione "Responsive" e defina manualmente 1080x1920
```

#### 2. Telas a Capturar (Recomendado)

**Tela 1: Login**
- URL: `https://cursolaravel.test/login`
- Descrição: "Acesso seguro com autenticação"
- Capturar: Formulário completo de login

**Tela 2: Dashboard Estagiário**
- URL: `https://cursolaravel.test/dashboard/estagiario` (após login)
- Descrição: "Visualize seus contratos e informações"
- Capturar: Cards com contratos e dados principais

**Tela 3: Meus Contratos**
- URL: `https://cursolaravel.test/meus-contratos`
- Descrição: "Acompanhe seus termos de estágio"
- Capturar: Lista de contratos com status

**Tela 4: Perfil/Documentos**
- URL: `https://cursolaravel.test/meu-perfil`
- Descrição: "Gerenciar dados e documentos"
- Capturar: Seção de documentos carregados

**Tela 5: Folha de Pagamento**
- URL: `https://cursolaravel.test/folhas-pagamento` (admin)
- Descrição: "Manage payroll and bonuses"
- Capturar: Tabela de folhas com status

**Tela 6: Mobile-friendly**
- URL: Qualquer tela
- Descrição: "Funciona em qualquer dispositivo"
- Capturar: Layout responsivo mostrando menu mobile

#### 3. Capturar com Chrome DevTools

**Opção A: Print to PDF (Mais Fácil)**
```
1. F12 → Console
2. Copie e cole:
```
```javascript
// Capturar viewport atual
const canvas = await html2canvas(document.body);
const link = document.createElement('a');
link.href = canvas.toDataURL('image/png');
link.download = 'screenshot-1.png';
link.click();
```
**Nota:** Pode precisar incluir `html2canvas` library primeiro.

**Opção B: Screenshot nativo do SO**
```
1. Deixar página carregada
2. Usar Print Screen ou ferramenta de screenshot (Captura + Anotação do Windows)
3. Colar em editor de imagem (Paint, Photoshop, etc)
4. Redimensionar para 1080x1920
5. Salvar como PNG
```

**Opção C: Emulador Android (Mais Realista)**
```
1. Abrir Android Studio
2. Rodar emulador Pixel 4 (1080x2280)
3. Abrir app PWA Builder
4. Tirar screenshot com Ctrl+S no emulador
```

#### 4. Edição e Otimização

Depois de capturar, adicionar texto:

```
1. Abrir em Photoshop/GIMP/Canva
2. Adicionar texto em grande (36-48pt)
3. Descrição: "Gerenciar Contratos", "Assinatura Digital", etc
4. Exportar como PNG (máx 1MB por imagem)
```

### Arquivo de Exemplo:

**screenshot-1-login.png** (1080x1920)
- Mostra formulário de login
- Texto overlay: "Login Seguro"

**screenshot-2-dashboard.png** (1080x1920)
- Mostra dashboard do estagiário
- Texto overlay: "Acompanhe seus Contratos"

**screenshot-3-perfil.png** (1080x1920)
- Mostra perfil e documentos
- Texto overlay: "Gerenciar Documentos"

---

## Salvando Screenshots

### 📱 Seus Screenshots Atuais → Nomes Corretos:

Renomeie suas 6 imagens para:

**Print 1 (Login):**
```
screenshot-1-login.png
```
*Descrição para Play Store: "Login seguro com autenticação de usuário"*

**Print 2 (Dashboard/Bem-vindo):**
```
screenshot-2-dashboard.png
```
*Descrição para Play Store: "Dashboard personalizado por perfil de acesso"*

**Print 3 (Meus Contratos - Lista):**
```
screenshot-3-contratos.png
```
*Descrição para Play Store: "Visualize seus contratos de estágio em tempo real"*

**Print 4 (Meus Documentos):**
```
screenshot-4-documentos.png
```
*Descrição para Play Store: "Gerencie e atualize seus documentos"*

**Print 5 (Dashboard Estagiário - Cards):**
```
screenshot-5-perfil.png
```
*Descrição para Play Store: "Acesso rápido a dados pessoais e contratos"*

**Print 6 (Histórico Recessos / Folha):**
```
screenshot-6-folha.png
```
*Descrição para Play Store: "Controle de recesso e recibos de pagamento"*

### ✅ Checklist Final:

- [ ] Todos os 6 arquivos renomeados
- [ ] Formato: PNG ou JPG ✓
- [ ] Resolução: 1080x1920 (ou similar mobile) ✓
- [ ] Tamanho arquivo: < 1MB cada ✓
- [ ] Salvos em: `public/screenshots/` ✓

### 🎯 Resultado:

```
public/screenshots/
├── screenshot-1-login.png
├── screenshot-2-dashboard.png
├── screenshot-3-contratos.png
├── screenshot-4-documentos.png
├── screenshot-5-perfil.png
└── screenshot-6-folha.png
```

3. Estas imagens serão enviadas na Play Store durante upload do APK

---

**Próximo passo:** Fazer upload desses screenshots na Play Console quando publicar.

**Dúvida?** Se não conseguir capturar, pode usar Figma/Canva para criar mockups profissionais.
