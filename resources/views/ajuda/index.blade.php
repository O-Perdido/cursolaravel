<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central de Ajuda - SIGEBR EBCP</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#102e6c">
    <meta name="description" content="Central de Ajuda do Sistema de Gestão de Estágios Brasileiros da EBCP">

    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/464848a8f8.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --primary-color: #102e6c;
            --secondary-color: #198754;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
            --content-max-width: 900px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        /* Header */
        .help-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a4599 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .help-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .help-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .help-header .btn-home {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }

        .help-header .btn-home:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Layout Principal */
        .help-container {
            display: flex;
            max-width: 1400px;
            margin: 2rem auto;
            gap: 2rem;
            padding: 0 1rem;
        }

        /* Sidebar */
        .help-sidebar {
            width: var(--sidebar-width);
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 120px;
            height: fit-content;
            max-height: calc(100vh - 140px);
            overflow-y: auto;
        }

        .help-sidebar h4 {
            color: var(--primary-color);
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 3px solid var(--secondary-color);
        }

        .help-sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .help-sidebar nav ul li {
            margin-bottom: 0.5rem;
        }

        .help-sidebar nav ul li a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: #4a5568;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .help-sidebar nav ul li a i {
            width: 20px;
            text-align: center;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .help-sidebar nav ul li a:hover,
        .help-sidebar nav ul li a.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a4599 100%);
            color: white;
            transform: translateX(5px);
        }

        .help-sidebar nav ul li a:hover i,
        .help-sidebar nav ul li a.active i {
            color: white;
        }

        /* Conteúdo */
        .help-content {
            flex: 1;
            max-width: var(--content-max-width);
        }

        .content-section {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            scroll-margin-top: 120px;
        }

        .content-section h2 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 3px solid var(--secondary-color);
            padding-bottom: 1rem;
        }

        .content-section h2 i {
            color: var(--secondary-color);
        }

        .content-section p {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 1.5rem;
        }

        /* Video Embed */
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
            margin: 1.5rem 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Steps */
        .steps-list {
            background: #f8f9fa;
            border-left: 4px solid var(--secondary-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .steps-list h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .steps-list ol {
            padding-left: 1.5rem;
            margin: 0;
        }

        .steps-list ol li {
            font-size: 1rem;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 0.8rem;
            padding-left: 0.5rem;
        }

        .steps-list ol li::marker {
            color: var(--secondary-color);
            font-weight: 700;
        }

        /* Alerts */
        .help-alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .help-alert i {
            font-size: 1.3rem;
            margin-top: 0.2rem;
        }

        .help-alert.alert-info {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            color: #004a99;
        }

        .help-alert.alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        .help-alert.alert-success {
            background: #d1f2eb;
            border-left: 4px solid var(--secondary-color);
            color: #0f5132;
        }

        /* FAQs */
        .faq-container {
            margin-top: 1.5rem;
        }

        .faq-item {
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s;
        }

        .faq-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .faq-question {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.2rem 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--primary-color);
            user-select: none;
        }

        .faq-question i {
            transition: transform 0.3s;
            color: var(--secondary-color);
        }

        .faq-question:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 1.5rem;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 1.2rem 1.5rem;
        }

        .faq-answer p {
            margin: 0;
            color: #4a5568;
            line-height: 1.7;
        }

        /* Images */
        .help-image {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            height: auto;
            margin: 1.5rem 0;
        }

        /* Scroll to top button */
        .scroll-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            background: var(--secondary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 999;
        }

        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            background: var(--primary-color);
            transform: translateY(-5px);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .help-container {
                flex-direction: column;
            }

            .help-sidebar {
                width: 100%;
                position: static;
                max-height: none;
            }

            .help-content {
                max-width: 100%;
            }

            .content-section {
                padding: 1.5rem;
            }

            .content-section h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .help-header h1 {
                font-size: 1.3rem;
            }

            .content-section {
                padding: 1rem;
            }

            .content-section h2 {
                font-size: 1.3rem;
            }

            .scroll-to-top {
                bottom: 1rem;
                right: 1rem;
            }
        }

        /* Scrollbar customizado */
        .help-sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .help-sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .help-sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .help-sidebar::-webkit-scrollbar-thumb:hover {
            background: #0d2452;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="help-header">
        <div class="container">
            <img src="{{ asset('images/logo_branca_sem_fundo.png') }}" alt="Logo" height="45"
                class="d-inline-block align-center">
            <h1>
                <i class="fas fa-question-circle"></i>
                Central de Ajuda
            </h1>
            <a href="/" class="btn-home">
                <span class="d-none d-sm-inline" style="margin-left: 5px;">
                    @guest
                        <img src="{{ asset('images/sige_logo_branco.png') }}" alt="" height="35"
                            style="margin-right: 15px;">
                    @endguest
                    @auth
                        <img src="{{ asset('images/sige_logo_branco.png') }}" alt="" height="35"
                            style="margin-right: 15px;">
                    @endauth
                </span>
                <i class="fas fa-home me-2"></i>
                Voltar ao Sistema
            </a>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <div class="help-container">
        <!-- Sidebar -->
        <aside class="help-sidebar">
            <h4><i class="fas fa-list me-2"></i>Sumário</h4>
            <nav>
                <ul>
                    @foreach ($sections as $section)
                        <li>
                            <a href="#{{ $section['id'] }}" class="nav-link-item">
                                <i class="fas {{ $section['icon'] }}"></i>
                                <span>{{ $section['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <!-- Conteúdo -->
        <main class="help-content">
            @foreach ($sections as $section)
                <section id="{{ $section['id'] }}" class="content-section">
                    <h2>
                        <i class="fas {{ $section['icon'] }}"></i>
                        {{ $section['title'] }}
                    </h2>

                    <p>{{ $section['content']['description'] }}</p>

                    @if (isset($section['content']['video']) && $section['content']['video'])
                        <div class="video-container">
                            <iframe src="{{ $section['content']['video'] }}" allowfullscreen></iframe>
                        </div>
                    @endif

                    @if (isset($section['content']['steps']))
                        <div class="steps-list">
                            <h5><i class="fas fa-tasks me-2"></i>Passo a Passo</h5>
                            <ol>
                                @foreach ($section['content']['steps'] as $step)
                                    <li>{{ $step }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    @if (isset($section['content']['alert']))
                        <div class="help-alert alert-{{ $section['content']['alert']['type'] }}">
                            <i class="fas 
                                                                                                                @if($section['content']['alert']['type'] == 'info') fa-info-circle
                                                                                                                @elseif($section['content']['alert']['type'] == 'warning') fa-exclamation-triangle
                                                                                                                @elseif($section['content']['alert']['type'] == 'success') fa-check-circle
                                                                                                                @endif
                                                                                                            "></i>
                            <span>{{ $section['content']['alert']['text'] }}</span>
                        </div>
                    @endif

                    @if (isset($section['content']['faqs']))
                        <div class="faq-container">
                            @foreach ($section['content']['faqs'] as $faq)
                                <div class="faq-item">
                                    <div class="faq-question">
                                        <span>{{ $faq['question'] }}</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div class="faq-answer">
                                        <p>{{ $faq['answer'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (isset($section['content']['images']) && count($section['content']['images']) > 0)
                        @foreach ($section['content']['images'] as $image)
                            <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?? '' }}" class="help-image">
                        @endforeach
                    @endif
                </section>
            @endforeach
        </main>
    </div>

    <!-- Scroll to top button -->
    <div class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Smooth scroll para links internos
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        // Atualizar link ativo
                        document.querySelectorAll('.nav-link-item').forEach(link => {
                            link.classList.remove('active');
                        });
                        this.classList.add('active');
                    }
                });
            });

            // FAQ accordion
            document.querySelectorAll('.faq-question').forEach(question => {
                question.addEventListener('click', function () {
                    const faqItem = this.closest('.faq-item');
                    const isActive = faqItem.classList.contains('active');

                    // Fechar todas as FAQs
                    document.querySelectorAll('.faq-item').forEach(item => {
                        item.classList.remove('active');
                    });

                    // Abrir a FAQ clicada se não estava ativa
                    if (!isActive) {
                        faqItem.classList.add('active');
                    }
                });
            });

            // Scroll to top button
            const scrollToTopBtn = document.getElementById('scrollToTop');

            window.addEventListener('scroll', function () {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.classList.add('visible');
                } else {
                    scrollToTopBtn.classList.remove('visible');
                }
            });

            scrollToTopBtn.addEventListener('click', function () {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Highlight ativo baseado no scroll
            const sections = document.querySelectorAll('.content-section');
            const navLinks = document.querySelectorAll('.nav-link-item');

            window.addEventListener('scroll', function () {
                let current = '';

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;

                    if (pageYOffset >= sectionTop - 150) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.add('active');
                    }
                });
            });

            // Ativar primeiro link por padrão
            if (navLinks.length > 0) {
                navLinks[0].classList.add('active');
            }
        });
    </script>
</body>

</html>