# 🎨 Guia de Imagens para Landing Page

## 📍 Onde Adicionar as Imagens

Crie as imagens no Canva e salve na pasta: `public/images/`

---

## 🖼️ Imagens Necessárias

### 1. Hero Section (Topo da Página)
- **Nome do arquivo:** `hero-estagiario.png`
- **Dimensões ideais:** 400x400px (quadrado) ou 500x350px (retângulo)
- **Sugestão Canva:**
  - Ilustração de estudante/estagiário trabalhando com notebook
  - Cores vibrantes (roxo, azul, rosa)
  - Estilo moderno e jovem
  - Pode usar templates "Student Working" ou "Young Professional"
- **Local no código:** `landing.blade.php` - linha ~37

**Como substituir:**
```blade
<!-- Procure por esta linha: -->
<i class="fas fa-graduation-cap text-white" style="font-size: 120px; opacity: 0.3;"></i>

<!-- Substitua por: -->
<img src="{{ asset('images/hero-estagiario.png') }}" alt="Estagiário" class="img-fluid rounded-3" style="max-height: 280px;">
```

---

### 2. Banner de Dicas
- **Nome do arquivo:** `tips-icon.png`
- **Dimensões ideais:** 300x300px (quadrado)
- **Sugestão Canva:**
  - Ícone de lâmpada/checklist
  - Pessoa estudando ou fazendo anotações
  - Cores claras (amarelo, laranja, branco)
  - Template "Lightbulb Idea" ou "Study Tips"
- **Local no código:** `landing.blade.php` - linha ~162

**Como substituir:**
```blade
<!-- Procure por esta linha: -->
<i class="fas fa-lightbulb text-white" style="font-size: 80px;"></i>

<!-- Substitua por: -->
<img src="{{ asset('images/tips-icon.png') }}" alt="Dicas" class="img-fluid rounded-circle" style="max-width: 200px;">
```

---

## 🎨 Ideias de Design no Canva

### Template 1: Hero Image
1. Abra Canva → "Criar um design" → Personalizado 400x400px
2. Busque templates: "Student Illustration", "Work from Home", "Young Professional"
3. Escolha um com cores roxas/azuis (combina com o gradiente da landing)
4. Customize com elementos de estágio: laptop, livros, diploma
5. Baixe como PNG (fundo transparente se possível)

### Template 2: Tips Icon
1. Abra Canva → "Criar um design" → Personalizado 300x300px
2. Busque templates: "Lightbulb", "Ideas Icon", "Study Tips"
3. Use cores claras (amarelo, branco, laranja)
4. Pode ser apenas um ícone grande ou mini ilustração
5. Baixe como PNG com fundo transparente

---

## 📂 Estrutura de Pastas

```
public/
├── images/
│   ├── hero-estagiario.png    (400x400px ou 500x350px)
│   └── tips-icon.png           (300x300px)
```

---

## ✅ Checklist de Implementação

- [ ] Criar pasta `public/images/` (se não existir)
- [ ] Criar no Canva: **hero-estagiario.png** (400x400px)
- [ ] Criar no Canva: **tips-icon.png** (300x300px)
- [ ] Salvar ambas em `public/images/`
- [ ] Editar `landing.blade.php` linha ~37 (hero)
- [ ] Editar `landing.blade.php` linha ~162 (tips)
- [ ] Testar no navegador: `http://localhost:8000/`

---

## 🎯 Dicas de Design

### Cores Sugeridas
- **Hero Image:** Roxo (#667eea), Azul (#764ba2), Rosa (#f093fb)
- **Tips Icon:** Amarelo (#ffd700), Laranja (#ff9500), Branco (#ffffff)

### Estilos
- **Moderno:** Ilustrações flat, cores sólidas
- **Jovem:** Gradientes, formas arredondadas
- **Profissional:** Limpo, minimalista

---

## 📱 Responsividade

As imagens se ajustam automaticamente em dispositivos móveis graças às classes:
- `img-fluid` - Imagem responsiva
- `rounded-3` / `rounded-circle` - Bordas arredondadas
- `max-height` / `max-width` - Limitam tamanho máximo

---

## 🔍 Exemplos de Busca no Canva

Para Hero Image:
- "student working illustration"
- "young professional laptop"
- "online learning modern"
- "internship illustration"

Para Tips Icon:
- "lightbulb idea icon"
- "checklist illustration"
- "study tips icon"
- "productivity illustration"

---

## 💡 Alternativa: Usar Ícones Temporários

Se quiser testar sem criar imagens ainda, pode usar ícones do Font Awesome (já está configurado):

**Hero:**
```blade
<i class="fas fa-user-graduate text-white" style="font-size: 120px; opacity: 0.5;"></i>
```

**Tips:**
```blade
<i class="fas fa-tasks text-white" style="font-size: 80px;"></i>
```

---

## 📞 Ajuda Rápida

**Problema:** Imagem não aparece  
**Solução:** Verifique se o arquivo está em `public/images/` e o nome está correto

**Problema:** Imagem muito grande  
**Solução:** Use `style="max-height: 280px;"` ou `max-width: 200px;"`

**Problema:** Imagem pixelada  
**Solução:** Use imagens maiores (pelo menos 400x400px) e deixe o navegador redimensionar

---

Pronto! Basta criar as imagens no Canva e adicionar na pasta `public/images/` 🎉
