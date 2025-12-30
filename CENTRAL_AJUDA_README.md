# Central de Ajuda - SIGEBR EBCP

## 📖 Visão Geral

A Central de Ajuda é uma página pública de documentação do sistema, criada para orientar usuários sobre as principais funcionalidades e responder dúvidas frequentes.

## 🌐 Acesso

- **URL**: `/ajuda`
- **Acesso**: Público (não requer autenticação)
- **Links disponíveis**:
  - Rodapé do sistema (todas as páginas)
  - Sidebar (usuários admin/operador)

## 🎨 Características

- ✅ Design moderno e responsivo
- ✅ Sumário lateral com navegação suave
- ✅ Suporte para vídeos do YouTube
- ✅ Suporte para imagens/prints
- ✅ Seções com passo a passo
- ✅ Acordeon para FAQs
- ✅ Alertas informativos coloridos
- ✅ Botão "scroll to top"
- ✅ Highlight automático da seção ativa

## 📝 Como Adicionar/Editar Conteúdo

Todo o conteúdo é gerenciado no controller `AjudaController.php`. Para adicionar ou modificar:

### 1. Adicionar Nova Seção

Edite o arquivo `app/Http/Controllers/AjudaController.php` e adicione um novo item no array `$sections`:

```php
[
    'id' => 'minha-secao',                    // ID único (usado na URL #minha-secao)
    'title' => 'Título da Seção',            // Título visível
    'icon' => 'fa-icon-name',                 // Ícone Font Awesome
    'content' => [
        'description' => 'Descrição...',      // Texto principal
        'video' => null,                      // URL do vídeo YouTube embed (opcional)
        'steps' => [],                        // Array de passos (opcional)
        'alert' => [],                        // Alerta (opcional)
        'images' => [],                       // Array de imagens (opcional)
        'faqs' => []                          // Array de FAQs (opcional)
    ]
]
```

### 2. Adicionar Vídeo do YouTube

```php
'video' => 'https://www.youtube.com/embed/VIDEO_ID'
```

**Como pegar o ID do vídeo:**
- URL normal: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`
- ID: `dQw4w9WgXcQ`
- URL embed: `https://www.youtube.com/embed/dQw4w9WgXcQ`

### 3. Adicionar Passo a Passo

```php
'steps' => [
    'Primeiro passo a ser realizado',
    'Segundo passo a ser realizado',
    'Terceiro passo...'
]
```

### 4. Adicionar Alerta

```php
'alert' => [
    'type' => 'info',      // Tipos: info (azul), warning (amarelo), success (verde)
    'text' => 'Texto do alerta importante'
]
```

### 5. Adicionar Imagens

```php
'images' => [
    [
        'url' => asset('images/tutorial/print1.png'),
        'alt' => 'Descrição da imagem'
    ]
]
```

### 6. Adicionar FAQs

```php
'faqs' => [
    [
        'question' => 'Pergunta frequente?',
        'answer' => 'Resposta detalhada para a pergunta.'
    ],
    [
        'question' => 'Outra pergunta?',
        'answer' => 'Outra resposta...'
    ]
]
```

## 📂 Estrutura de Arquivos

```
app/Http/Controllers/
  └── AjudaController.php          # Lógica e conteúdo

resources/views/ajuda/
  └── index.blade.php              # Template da página

routes/web.php                      # Rota pública
```

## 🎨 Personalização Visual

### Cores Principais

As cores seguem o padrão do sistema e estão definidas nas variáveis CSS em `index.blade.php`:

```css
:root {
    --primary-color: #102e6c;      /* Azul principal do sistema */
    --secondary-color: #198754;    /* Verde de destaque */
}
```

### Ícones

Utilize ícones do [Font Awesome](https://fontawesome.com/icons):
- `fa-home` - Casa
- `fa-user-plus` - Usuário novo
- `fa-file-contract` - Contrato
- `fa-question-circle` - Interrogação
- etc.

## 🚀 Exemplo Completo

```php
[
    'id' => 'exemplo-completo',
    'title' => 'Tutorial Completo',
    'icon' => 'fa-graduation-cap',
    'content' => [
        'description' => 'Este é um tutorial completo mostrando todas as possibilidades.',
        'video' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
        'steps' => [
            'Acesse o sistema',
            'Clique no menu',
            'Preencha os dados',
            'Salve as alterações'
        ],
        'alert' => [
            'type' => 'warning',
            'text' => 'Atenção: sempre salve antes de sair!'
        ],
        'images' => [
            [
                'url' => asset('images/tutorial/exemplo.png'),
                'alt' => 'Exemplo de tela'
            ]
        ],
        'faqs' => [
            [
                'question' => 'Como faço isso?',
                'answer' => 'Basta seguir os passos acima!'
            ]
        ]
    ]
]
```

## 📱 Responsividade

A página é totalmente responsiva:
- **Desktop**: Sidebar fixa à esquerda
- **Tablet/Mobile**: Sidebar acima do conteúdo

## 🔧 Manutenção

Para manter a Central de Ajuda atualizada:

1. Revise periodicamente o conteúdo
2. Adicione tutoriais para novas funcionalidades
3. Atualize FAQs baseado em dúvidas recorrentes
4. Grave vídeos tutoriais quando necessário
5. Tire prints atualizados das telas

## 💡 Dicas

- **Vídeos curtos**: Mantenha tutoriais entre 2-5 minutos
- **Linguagem simples**: Evite termos técnicos complexos
- **Prints atualizados**: Capture telas com boa resolução
- **SEO**: Use títulos descritivos e palavras-chave
- **Acessibilidade**: Sempre adicione texto alternativo em imagens

## 🐛 Resolução de Problemas

**Vídeo não carrega:**
- Verifique se a URL está no formato embed (`/embed/VIDEO_ID`)
- Confirme que o vídeo é público no YouTube

**Navegação não funciona:**
- Verifique se os IDs das seções são únicos
- Confirme que não há espaços ou caracteres especiais nos IDs

**Imagem não aparece:**
- Confirme que a imagem está em `public/images/`
- Use `asset('images/nome.png')` para gerar o caminho
- Execute `php artisan storage:link` se usar storage

---

**Desenvolvido para SIGEBR - Sistema de Gestão de Estágios Brasileiros**
