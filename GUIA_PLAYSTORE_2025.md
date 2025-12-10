# ✅ GUIA PRÁTICO: Publicar SIGEBR na Play Store (2025)

## 📋 Checklist de Preparação

### ✅ Fase 1: Validação PWA (Hoje)

- [x] Description do manifest.json melhorada (132+ chars)
- [x] Privacy policy criada em `public/privacy.html`
- [x] Manifest.json com ícones (já possui 8 tamanhos)
- [x] Service Worker implementado
- [x] App funcionando em HTTPS (Laragon)
- [x] ID único adicionado ao manifest (`br.com.ebcp.sigebr`)
- [x] Screenshots configurados no manifest

**Próximo:** Validar novamente em https://www.pwabuilder.com/

---

## 🚀 Próximas Etapas (Ordem Exata)

### ETAPA 1️⃣: Validar PWA com PWA Builder (30 min)

1. Abra https://www.pwabuilder.com/
2. Cole URL: `https://cursolaravel.test/` (seu Laragon local)
3. Clique em "Start"
4. **Resultado esperado:**
   - ✅ Manifest: 100%
   - ✅ Service Worker: 100%
   - ✅ HTTPS: ✓
   - ✅ Icons: ✓
   - ✅ Screenshots: ✓ (adicionados ao manifest)
   - ✅ ID: ✓ (br.com.ebcp.sigebr)
   
5. Se alguma validação falhar, me avise para corrigir

---

### ETAPA 2️⃣: Capturar Screenshots (1-2 horas)

Seguir `public/screenshots/README.md`:

1. Abrir Laragon em HTTPS
2. Logar com usuário de teste (estagiário, admin, empresa)
3. Capturar 6 telas:
   - Login
   - Dashboard
   - Meus Contratos
   - Perfil
   - Folha de Pagamento
   - Mobile-friendly

4. Salvar em: `public/screenshots/screenshot-{n}-{nome}.png`
5. Validar: 1080x1920, PNG, < 1MB cada

**Dica:** Se achar trabalhoso, pode usar Figma/Canva para criar mockups profissionais.

---

### ETAPA 3️⃣: Gerar APK de Teste (30 min)

1. Voltando ao PWA Builder (após validação)
2. Clique "Package for Stores"
3. Escolha "Android"
4. Preencha formulário:
   - **Package ID:** `br.com.ebcp.sigebr`
   - **App Name:** SIGEBR - EBCP
   - **Launcher Name:** SIGEBR
   - **Theme Color:** #198754
   - **Background Color:** #ffffff
   - **Icon:** URL do icon-512x512.png

5. Signing Key: Escolha "Generate"
6. **IMPORTANTE:** Salve `.keystore` e senha em local seguro
7. Clique "Generate"
8. Download `signed-apk.zip`

---

### ETAPA 4️⃣: Testar em Emulador/Dispositivo (1-2 horas)

**Opção A: Emulador Android Studio**

```powershell
# Instalar Android Studio (se não tiver)
# Criar emulador Pixel 4 com Android 12+

# Extrair APK do zip e instalar
adb install app-release-signed.apk

# Abrir app
# Testar: Login, Contratos, Upload de documento
```

**Opção B: Dispositivo Real**

```powershell
# Transferir APK para celular
# Abrir gerenciador de arquivos
# Instalar APK

# Ou via ADB:
adb install -r app-release-signed.apk
```

**Testes Críticos:**
- [ ] Login funciona
- [ ] Dashboard carrega
- [ ] Contratos aparecem
- [ ] Upload de documento funciona (modal + câmera/galeria)
- [ ] Download de documento funciona
- [ ] Funciona offline (mostrar página offline.html)

**Esperado:** Tudo funciona igual ao site normal, mas em forma de app.

---

### ETAPA 5️⃣: Criar Conta Google Play Developer ($25) (15 min)

1. Acesse https://play.google.com/console
2. Crie conta Google (se não tiver)
3. **Pague $25** (taxa única)
4. Preencha perfil (nome, país, endereço)
5. Aguarde aprovação: 1-2 dias

---

### ETAPA 6️⃣: Publicar APK (2-3 horas)

**No Play Console:**

1. "Criar novo app"
2. Nome: **SIGEBR - EBCP Sistema de Estágios**
3. Categoria: **Negócios**
4. Idioma padrão: **Português (Brasil)**
5. Gratuito: **Sim**

**Aba: Produção**
1. "Criar nova versão"
2. Upload: `app-release-signed.apk`
3. Versão code: 1
4. Release notes:
   ```
   Versão inicial do SIGEBR App
   - Gestão de contratos de estágio
   - Assinatura digital integrada
   - Folhas de pagamento
   - Funciona offline
   ```

**Aba: Ficha da Loja**
1. Descrição curta (80 chars):
   ```
   Gerencie estágios, contratos e folhas de pagamento da EBCP
   ```

2. Descrição completa (4000 chars):
   ```
   O SIGEBR é o sistema oficial da EBCP para gestão completa de estágios.

   FUNCIONALIDADES:
   • Contratos digitais com assinatura eletrônica (ZapSign)
   • Folhas de pagamento automatizadas
   • Controle de recesso de estagiários
   • Acompanhamento em tempo real
   • Relatórios e exportações em Excel
   • Funciona online e offline

   PARA QUEM:
   • Empresas parceiras da EBCP
   • Estagiários cadastrados
   • Instituições de ensino
   • Gestores de RH

   SEGURANÇA:
   • Dados protegidos com criptografia
   • Autenticação de usuários
   • Assinatura eletrônica certificada
   • Conformidade LGPD

   SUPORTE:
   Dúvidas? Entre em contato em suporte@ebcp.com.br
   ```

3. Screenshots: Upload dos 6 arquivos
4. Ícone: `public/images/icons/icon-512x512.png`
5. Imagem destaque (1024x500): Criar banner com logo
6. Classificação: Preencher questionário (marcar "Coleta de dados pessoais")
7. Privacy policy: `https://seudominio.com.br/privacy.html`
8. Público: Adultos 18+

**Enviar para revisão**

---

### ETAPA 7️⃣: Aguardar Aprovação (3-7 dias)

- Google revisa seu app
- Play Console mostra status
- Se rejeitar, corrigir e reenviar
- Se aprovar, 🎉 está publicado!

---

## 🎯 Linha do Tempo Estimada

| Etapa | Tempo | Quando Começar |
|-------|-------|----------------|
| 1. Validação PWA | 30 min | Hoje |
| 2. Screenshots | 1-2h | Hoje ou amanhã |
| 3. Gerar APK | 30 min | Após screenshots |
| 4. Testar | 1-2h | Após APK |
| 5. Conta Play ($25) | 15 min | Após testes OK |
| 6. Publicar | 2-3h | Após conta criada |
| 7. Aprovação Google | 3-7 dias | Automático |
| **TOTAL** | **~2 semanas** | **Começar agora** |

---

## ⚠️ Pontos Críticos

### Documentar antes de fazer upload:
1. **Package ID:** `br.com.ebcp.sigebr` (não pode mudar depois!)
2. **Keystore + Senha:** Guardar em local seguro (precisará para updates)
3. **Screenshots:** Confirmar tamanho 1080x1920 e < 1MB
4. **Privacy Policy:** URL acessível publicamente
5. **HTTPS:** Certificado válido em produção

### Se algo der errado:
- ❌ Manifest inválido → Corrigir e regenerar APK
- ❌ Screenshot com tamanho errado → Recapturar
- ❌ APK não instala → Testar em emulador primeiro
- ❌ Google rejeita → Ler feedback e ajustar (geralmente é permission/privacy)

---

## 📱 Para iOS (Futuro)

**Situação Atual:**
- ✅ PWA funciona em iOS 16.4+
- ✅ Usuários podem instalar via Safari → Compartilhar → Adicionar
- ❌ Não será publicado na App Store ($99/ano + manutenção)

**Se decidir publicar iOS depois:**
1. PWA Builder gera projeto Xcode
2. Precisaria Mac + Xcode
3. Pagar $99/ano para Apple Developer
4. Segue processo similar ao Android

---

## ✅ Próxima Ação

**Agora você deve:**

1. Acessar https://www.pwabuilder.com/
2. Validar seu PWA (colar URL do Laragon)
3. Me confirmar se passou na validação
4. Começar a capturar screenshots

Me avise quando tiver os screenshots prontos! 🎉

---

**Dúvidas?** Qualquer dúvida sobre o processo, é só chamar!
