# 📸 Guia: Como Adicionar Imagens de Fundo nos Slides do Carrossel

## 🎯 Visão Geral
Os 3 slides do carrossel da landing page foram preparados para receber imagens de fundo opcionais, que criarão um visual ainda mais profissional e atrativo.

## 📐 Especificações Técnicas

### Tamanho Recomendado
- **Dimensões**: 1920x650 pixels (landscape)
- **Formato**: JPG ou PNG
- **Peso**: Máximo 500KB (otimizado para web)
- **Proporção**: 2.95:1 (mesma proporção do hero section)

### Qualidade da Imagem
- **Resolução**: 72 DPI (suficiente para web)
- **Compressão**: Use ferramentas como TinyPNG ou ImageOptim
- **Foco**: Certifique-se que o ponto focal está no centro/esquerda

---

## 📁 Onde Salvar as Imagens

Salve as imagens na pasta:
```
public/images/
```

**Sugestões de nomes:**
- `hero-slide1.jpg` - Slide de boas-vindas
- `hero-slide2.jpg` - Slide sobre o SIGE
- `hero-slide3.jpg` - Slide CTA final

---

## 🖼️ Como Adicionar as Imagens

### Slide 1: Bem-vindo

**Localização no código:**
```html
<!-- Slide 1: Bem-vindo -->
<!-- 📸 IMAGEM DE FUNDO (OPCIONAL): Adicione background-image abaixo -->
<div class="carousel-item active" style="height: 100%;">
```

**Adicione o background-image:**
```html
<div class="carousel-item active" 
     style="background: url('{{ asset('images/hero-slide1.jpg') }}') center/cover no-repeat; height: 100%;">
```

---

### Slide 2: Sobre

**Localização no código:**
```html
<!-- Slide 2: Sobre -->
<!-- 📸 IMAGEM DE FUNDO (OPCIONAL): Adicione background-image abaixo -->
<div class="carousel-item" style="height: 100%;">
```

**Adicione o background-image:**
```html
<div class="carousel-item" 
     style="background: url('{{ asset('images/hero-slide2.jpg') }}') center/cover no-repeat; height: 100%;">
```

---

### Slide 3: CTA Final

**Localização no código:**
```html
<!-- Slide 3: CTA Final -->
<!-- 📸 IMAGEM DE FUNDO (OPCIONAL): Adicione background-image abaixo -->
<div class="carousel-item" style="height: 100%;">
```

**Adicione o background-image:**
```html
<div class="carousel-item" 
     style="background: url('{{ asset('images/hero-slide3.jpg') }}') center/cover no-repeat; height: 100%;">
```

---

## 🎨 Ideias de Imagens por Slide

### Slide 1: Bem-vindo 🎓
**Tema:** Estudante confiante e empolgado

**Sugestões:**
- Jovem estudante segurando notebook/livros com sorriso
- Grupo de estudantes universitários em ambiente moderno
- Pessoa trabalhando em laptop com expressão feliz
- Ambiente de escritório moderno e acolhedor

**Cores predominantes:** Azul, branco, cinza
**Estilo:** Profissional, inspirador, clean

**Fontes gratuitas:**
- [Unsplash](https://unsplash.com/s/photos/student-laptop)
- [Pexels](https://pexels.com/search/professional student)
- [Freepik](https://freepik.com) (free tier)

**Palavras-chave de busca:**
```
"professional student", "intern working", "young professional laptop"
"university student confident", "workplace intern"
```

---

### Slide 2: Conectando Talentos 📊
**Tema:** Trabalho em equipe e conexão

**Sugestões:**
- Grupo diverso trabalhando em projeto colaborativo
- Jovens profissionais em reunião/brainstorming
- Colegas de trabalho interagindo positivamente
- Ambiente corporativo moderno com pessoas

**Cores predominantes:** Azul escuro, verde, branco
**Estilo:** Corporativo, dinâmico, colaborativo

**Palavras-chave de busca:**
```
"team collaboration office", "diverse workplace", "business teamwork"
"professionals meeting", "corporate team"
```

---

### Slide 3: Comece Agora 🚀
**Tema:** Conquista, sucesso e início de jornada

**Sugestões:**
- Pessoa comemorando conquista profissional
- Workspace moderno e organizado (visão de cima)
- Estudante feliz com documentos/contrato
- Jovem profissional em pose confiante

**Cores predominantes:** Roxo, azul royal, dourado
**Estilo:** Motivacional, aspiracional, vitorioso

**Palavras-chave de busca:**
```
"career success young", "professional achievement", "intern hired"
"workspace modern top view", "young professional confident"
```

---

## 🔧 Ajustando o Overlay

Cada slide tem um **overlay escuro** (fundo semi-transparente) sobre a imagem para garantir que o texto branco seja legível.

**Localização:**
```html
<div class="carousel-overlay" 
     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(16, 46, 108, 0.85); z-index: 1;">
</div>
```

### Ajustar Opacidade do Overlay

Se a imagem estiver **muito escura**, diminua a opacidade:
```css
background: rgba(16, 46, 108, 0.60); /* Mais transparente */
```

Se a imagem estiver **muito clara**, aumente a opacidade:
```css
background: rgba(16, 46, 108, 0.90); /* Mais escuro */
```

### Alterar Cor do Overlay

Slide 1 (Azul escuro):
```css
background: rgba(16, 46, 108, 0.85);
```

Slide 2 (Azul médio):
```css
background: rgba(10, 31, 77, 0.88);
```

Slide 3 (Azul royal):
```css
background: rgba(26, 58, 138, 0.85);
```

---

## ✅ Checklist de Implementação

- [ ] Baixar/criar 3 imagens (1920x650px)
- [ ] Otimizar imagens (TinyPNG ou similar)
- [ ] Renomear para `hero-slide1.jpg`, `hero-slide2.jpg`, `hero-slide3.jpg`
- [ ] Salvar em `public/images/`
- [ ] Adicionar `background: url(...)` em cada slide
- [ ] Testar responsividade (mobile, tablet, desktop)
- [ ] Ajustar opacidade do overlay se necessário
- [ ] Verificar tempo de carregamento da página

---

## 🎯 Exemplo Completo de Slide com Imagem

```html
<!-- Slide 1: Bem-vindo -->
<div class="carousel-item active" 
     style="background: url('{{ asset('images/hero-slide1.jpg') }}') center/cover no-repeat; height: 100%;">
    <!-- Overlay escuro -->
    <div class="carousel-overlay" 
         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                background: rgba(16, 46, 108, 0.85); z-index: 1;">
    </div>
    
    <!-- Conteúdo do slide (z-index: 2 garante que fica acima do overlay) -->
    <div class="d-flex align-items-center justify-content-center" 
         style="height: 100%; position: relative; z-index: 2;">
        <div class="container px-4">
            <!-- ... conteúdo ... -->
        </div>
    </div>
</div>
```

---

## 🚨 Troubleshooting

### Imagem não aparece
- Verifique se o caminho está correto: `public/images/hero-slide1.jpg`
- Execute `php artisan cache:clear` e `php artisan config:clear`
- Verifique permissões da pasta `public/images/`

### Imagem está pixelada
- Use imagens de pelo menos 1920x650px
- Não use imagens muito pequenas esticadas

### Texto não está legível
- Aumente a opacidade do overlay (0.90 ou 0.95)
- Use imagens com áreas claras/escuras adequadas

### Página carrega lento
- Otimize as imagens (máximo 500KB cada)
- Use formato JPG para fotos (menor que PNG)
- Considere usar WebP para browsers modernos

---

## 💡 Dicas Extras

1. **Consistência visual**: Use imagens com paleta de cores similar
2. **Foco no texto**: Garanta que há espaço "vazio" onde o texto aparece
3. **Testes**: Sempre teste em diferentes dispositivos
4. **Alternativas**: Se não encontrar imagens boas, deixe sem - o gradiente já é bonito!

---

## 📚 Recursos Recomendados

### Bancos de Imagens Gratuitos
- [Unsplash](https://unsplash.com) - Alta qualidade, sem atribuição
- [Pexels](https://pexels.com) - Diverso e gratuito
- [Pixabay](https://pixabay.com) - Livre de direitos autorais

### Ferramentas de Otimização
- [TinyPNG](https://tinypng.com) - Compressão sem perda de qualidade
- [Squoosh](https://squoosh.app) - Google's image optimizer
- [ImageOptim](https://imageoptim.com) - Mac app gratuito

### Ferramentas de Edição
- [Canva](https://canva.com) - Design fácil e intuitivo
- [Photopea](https://photopea.com) - Photoshop online grátis
- [Remove.bg](https://remove.bg) - Remover fundo de imagens

---

**Última atualização:** 29/01/2026
**Versão:** 1.0
