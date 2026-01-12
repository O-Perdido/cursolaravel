# 📱 PWA - Progressive Web App - SIGEBR

## 🎯 O que é?

O SIGEBR agora pode ser instalado como um aplicativo no celular ou computador! Funciona exatamente como um app nativo, mas sem precisar baixar da Play Store ou App Store.

## ✨ Benefícios

### Para o Estagiário:
- 📱 **Acesso rápido**: Ícone na tela inicial do celular
- 🚀 **Carregamento instantâneo**: Assets em cache (3x mais rápido)
- 📵 **Funciona parcialmente offline**: Mostra página bonitinha quando perde conexão
- 💾 **Zero espaço**: Não ocupa armazenamento do celular
- 🔄 **Sempre atualizado**: Recebe atualizações automaticamente
- 🎨 **Tela cheia**: Abre sem barra de navegador (parece app nativo)

### Para o Desenvolvimento:
- ✅ **Zero manutenção extra**: Continua sendo Laravel normal
- ✅ **Deploy igual**: Nada muda no workflow
- ✅ **SEO mantido**: Continua sendo um site normal
- ✅ **Analytics funciona**: Google Analytics, Meta Pixel, etc continuam normais

## 📋 Arquivos Criados

```
public/
├── manifest.json              # Configuração do PWA
├── service-worker.js          # Gerenciador de cache
├── offline.html               # Página "sem conexão"
└── images/
    ├── logo_sige_app.png      # Logo original (800x800)
    ├── generate-icons.php     # Script para gerar ícones
    └── icons/
        ├── icon-72x72.png
        ├── icon-96x96.png
        ├── icon-128x128.png
        ├── icon-144x144.png
        ├── icon-152x152.png
        ├── icon-192x192.png
        ├── icon-384x384.png
        └── icon-512x512.png

resources/views/
├── layouts/main.blade.php     # Atualizado com meta tags PWA
├── login.blade.php            # Card de instalação
└── welcome_estagiario.blade.php  # Banner de instalação
```

## 🚀 Como Funciona?

### 1. Detecção Automática

O navegador detecta automaticamente que o site é instalável quando:
- Tem `manifest.json` válido
- Tem service worker registrado
- É acessado via HTTPS (ou localhost)
- Usuário visita pelo menos 2 vezes (30 segundos entre visitas)

### 2. Prompt de Instalação

Quando detectado, mostra botões em 3 lugares:
- **Login**: Card destacado (primeira impressão)
- **Welcome Estagiário**: Banner informativo (lembrete)
- **Rodapé**: Link discreto (sempre disponível)

### 3. Instalação

```
Usuário clica "Instalar" 
  ↓
Navegador mostra prompt nativo
  ↓
Usuário confirma
  ↓
Ícone adicionado à tela inicial
  ↓
App abre em tela cheia
```

## 🔧 Estratégia de Cache

### Network First (HTML/Páginas)
```javascript
1. Tenta buscar da internet
2. Se conseguir → Salva em cache e mostra
3. Se falhar → Busca do cache
4. Se não tiver → Mostra offline.html
```

### Cache First (CSS/JS/Imagens)
```javascript
1. Busca do cache primeiro
2. Mostra imediatamente (rápido!)
3. Em paralelo, atualiza da internet
4. Próxima visita terá versão atualizada
```

### Network Only (API/Formulários)
```javascript
1. Sempre tenta internet
2. Nunca cacheia (dados sempre frescos)
3. Se falhar → Mostra erro
```

## 📱 Compatibilidade

### ✅ Suportado:
- **Android**: Chrome, Edge, Samsung Internet, Opera
- **iOS 16.4+**: Safari
- **Desktop**: Chrome, Edge (Windows/Mac/Linux)

### ⚠️ Limitações iOS:
- Não mostra prompt automático (usuário precisa ir em Safari > Compartilhar > Adicionar à Tela Inicial)
- Botões de instalação não aparecem no iOS (design do Apple)

### ❌ Não suportado:
- iOS < 16.4
- Firefox mobile (ainda)
- Navegadores antigos

## 🛠️ Manutenção

### Deploy Normal (99% dos casos)

**Você NÃO precisa fazer NADA!** 

Quando atualizar o código:
```bash
git add .
git commit -m "Ajustei um botão"
git push
```

O service worker detecta automaticamente e atualiza o cache.

### Forçar Limpeza de Cache

**Apenas se CSS/JS estiver "preso" em versão antiga:**

1. Abra `public/service-worker.js`
2. Altere a versão:
```javascript
const CACHE_NAME = 'sigebr-v1.0.0';  // Mude para v1.0.1
```
3. Commit e deploy normal

### Trocar Logo

1. Substitua `public/images/logo_sige_app.png`
2. Gere novos ícones:
```powershell
php public/images/generate-icons.php
```
3. Commit e deploy

### Alterar Nome do App

Edite `public/manifest.json`:
```json
{
  "name": "NOVO NOME - EBCP",
  "short_name": "NOVO NOME"
}
```

### Alterar Cores

Edite `public/manifest.json`:
```json
{
  "background_color": "#ffffff",
  "theme_color": "#198754"  // Cor da barra de status
}
```

## 🧪 Como Testar

### No Celular (Recomendado):

1. Acesse o site pelo Chrome/Safari
2. Se aparecer botão "Instalar" → Funciona! ✅
3. Clique e instale
4. Verifique ícone na tela inicial
5. Abra o app instalado
6. Deve abrir em tela cheia (sem barra de navegador)

### No Desktop:

1. Chrome → Abra DevTools (F12)
2. Application → Manifest → Verifique se carregou
3. Application → Service Workers → Deve aparecer registrado
4. Lighthouse → Progressive Web App → Deve dar 100%

### Testar Offline:

1. Abra o app/site
2. DevTools (F12) → Network → Offline
3. Navegue entre páginas
4. Deve mostrar página "Sem Conexão" personalizada

## 🐛 Troubleshooting

### Botão de instalação não aparece

**Possíveis causas:**
- Navegador não suporta (iOS < 16.4, Firefox mobile)
- App já está instalado
- Não está em HTTPS (exceto localhost)
- Manifest.json com erro

**Verificação:**
```javascript
// Console do navegador
navigator.serviceWorker.getRegistration().then(reg => console.log(reg));
// Deve mostrar objeto, não undefined
```

### Cache "preso" em versão antiga

```javascript
// Console do navegador
navigator.serviceWorker.getRegistrations().then(regs => {
  regs.forEach(reg => reg.unregister());
});
// Recarregue a página
```

### Ícones não aparecem

Verifique se arquivos existem:
```
public/images/icons/icon-192x192.png
public/images/icons/icon-512x512.png
```

Se não existirem:
```powershell
php public/images/generate-icons.php
```

### Service Worker não registra

1. Verifique console do navegador
2. Certifique-se que `public/service-worker.js` existe
3. Tente limpar cache: Settings → Privacy → Clear browsing data

## 📊 Monitoramento

### Ver instalações (Google Analytics)

Adicione ao GA:
```javascript
window.addEventListener('appinstalled', () => {
  gtag('event', 'pwa_installed');
});
```

### Ver usuários em modo standalone

```javascript
if (window.matchMedia('(display-mode: standalone)').matches) {
  // Usuário está usando como app instalado
  console.log('App mode: Instalado');
} else {
  // Usuário está usando navegador normal
  console.log('App mode: Navegador');
}
```

## 🎓 Para Aprender Mais

- [Web.dev - PWA](https://web.dev/progressive-web-apps/)
- [MDN - Service Workers](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Manifest Generator](https://www.simicart.com/manifest-generator.html/)

## 📝 Changelog

### v1.0.0 (07/11/2025)
- ✅ Implementação inicial do PWA
- ✅ Manifest.json configurado
- ✅ Service worker com cache inteligente
- ✅ Página offline personalizada
- ✅ Ícones em 8 tamanhos
- ✅ Botões de instalação em login e welcome
- ✅ Link no rodapé
- ✅ Atalhos para contratos e perfil
- ✅ Detecção automática de app instalado

---

**Desenvolvido por:** João Pedro & Davi Aguiar  
**Data:** 07/11/2025  
**Status:** ✅ Produção
