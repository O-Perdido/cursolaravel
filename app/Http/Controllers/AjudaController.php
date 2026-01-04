<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjudaController extends Controller
{
    public function index()
    {
        // Estrutura de conteúdo da documentação
        $sections = [
            [
                'id' => 'introducao',
                'title' => 'Introdução ao Sistema',
                'icon' => 'fa-home',
                'content' => [
                    'description' => 'Bem-vindo à Central de Ajuda do SIGEBR - EBCP. Aqui você encontrará tutoriais, guias e respostas para as principais dúvidas sobre o sistema de gestão de estágios. SIGEBR é uma solução exclusiva da EBCP, criada para facilitar o processo de gestão de estágios.',
                    'video' => null, // Exemplo de vídeo introdutório
                    'images' => [
                        [
                            'url' => asset('images/tutorial/painel_sige_ebcp.png'),
                            'alt' => 'Descrição da imagem'   
                        ]                        
                    ]
                ]
            ],
            [
                'id' => 'cadastro-estagiarios',
                'title' => 'Cadastro de Estagiários',
                'icon' => 'fa-user-plus',
                'content' => [
                    'description' => 'Como realizar o cadastro de estagiários no sistema.',
                    'video' => 'https://www.youtube.com/embed/G0Q9xe4rdNI',
                    'steps' => [
                        'Acesse o link de cadastro que foi enviado para você.',
                        'Preencha todos os campos obrigatórios com suas informações pessoais.',
                        'Crie uma senha segura para sua conta.',
                        'Leia e aceite os termos de uso e a política de privacidade.',
                        'Clique no botão "Cadastrar" para finalizar o processo.',
                        'Após o cadastro, você receberá um e-mail com um código de ativação. Use esse código para ativar sua conta.',
                        'Depois de ativar sua conta, faça login no sistema com seu e-mail e senha cadastrados.',
                        'Caso tenha esquecido sua senha, utilize a opção "Redefinir senha" na tela de login para redefini-la.',
                        'Caso seu e-mail de acesso esteja incorreto ou desatualizado, entre em contato com sua unidade concedente para abrir um chamado no sistema, solicitando a alteração.'
                    ],                                         
                ]                
            ],
            [
                'id' => 'primeiro-acesso-estagiarios',
                'title' => 'Primeiro Acesso - Estagiários',
                'icon' => 'fa-user-plus',
                'content' => [
                    'description' => 'Aprenda como fazer seu primeiro acesso ao sistema. Se
                    você é estagiário e já realizou seu cadastro em nossa plataforma, siga os passos abaixo para acessar o sistema pela primeira vez.',
                    'video' => null, // Exemplo: 'https://www.youtube.com/embed/x9QIa5JF72o?si=ZGZ6E3fPCsMJbvAX'
                    'steps' => [
                        'Após fazer o seu cadastro, você receberá um e-mail de confirmação com um código de verificação.',
                        'Caso ainda não tenha realizado a confirmação do código, realize o login normalmente com seu e-mail e senha cadastrados. Você será redirecionado para a tela de confirmação do código.',
                        'Após a confirmação do código você poderá acessar o sistema normalmente.',
                        'Caso tenha esquecido sua senha, utilize a opção "Redefinir senha" na tela de login para redefini-la.',
                        'Caso seu e-mail de acesso esteja incorreto ou desatualizado, entre em contato com sua unidade concedente para abrir um chamado no sistema, solicitando a alteração.',                        
                    ],
                    'images' => [
                        [
                            'url' => asset('images/tutorial/pagina_login_mobile.png'),
                            'alt' => 'Página de Login do Sistema'   
                        ]  
                    ]
                ]
            ],            
            [
                'id' => 'atualizar-dados-pessoais-estagiarios',
                'title' => 'Atualizar Dados Pessoais - Estagiários',
                'icon' => 'fa-id-card',
                'content' => [
                    'description' => 'Como estagiários podem atualizar seus dados pessoais no sistema.',
                    'video' => 'https://www.youtube.com/embed/F9j9b4n76fw', //https://youtu.be/F9j9b4n76fw
                    'steps' => [
                        'Faça login no sistema SIGE utilizando seu e-mail e senha',
                        'Na página inicial, clique em "Ver Perfil Completo" na seção de Dados Pessoais',
                        'Para documentos, vá até "Meus Documentos" e clique em "Atualizar"',
                        'Para dados cadastrais, clique no botão "Editar Dados" no topo da página',
                        'Atualize as informações no formulário (como endereço ou Chave PIX)',
                        'Clique em "Salvar Alterações" para confirmar a atualização dos dados'
                    ],
                    'images' => []
                ]
            ],
            [
                'id' => 'vagas',
                'title' => 'Sistema de Vagas',
                'icon' => 'fa-briefcase',
                'content' => [
                    'description' => 'Como empresas podem publicar vagas de estágio.',
                    'video' => null,
                    'steps' => [
                        'Acesse o menu "Vagas de Estágio"',
                        'Clique em "Publicar Nova Vaga"',
                        'Preencha as informações da vaga',
                        'Defina requisitos e benefícios',
                        'Escolha a validade da publicação',
                        'Salve e publique a vaga'
                    ],
                    'images' => [
                        
                    ]                    
                ]
            ],
            [
                'id' => 'chamados',
                'title' => 'Abertura de Chamados',
                'icon' => 'fa-headset',
                'content' => [
                    'description' => 'Como abrir chamados de suporte técnico.',
                    'video' => null,
                    'steps' => [
                        'Acesse "Suporte" no menu lateral',
                        'Clique em "Abrir Chamado"',
                        'Selecione a categoria do problema',
                        'Descreva detalhadamente sua dúvida ou problema',
                        'Anexe prints ou arquivos se necessário',
                        'Envie o chamado',
                        'Acompanhe o status através do painel'
                    ],
                    'alert' => [
                        'type' => 'success',
                        'text' => 'Nossa equipe responde chamados em até 24 horas úteis.'
                    ]
                ]
            ],
            [
                'id' => 'faq',
                'title' => 'Perguntas Frequentes',
                'icon' => 'fa-question-circle',
                'content' => [
                    'description' => 'Respostas rápidas para as dúvidas mais comuns.',
                    'faqs' => [
                        [
                            'question' => 'Como recuperar minha senha?',
                            'answer' => 'Na tela de login, clique em "Esqueci minha senha". Informe seu e-mail cadastrado e siga as instruções enviadas.'
                        ],
                        [
                            'question' => 'Posso alterar dados de um termo já assinado?',
                            'answer' => 'Sim, através do recurso de "Alteração de Termo". O sistema gerará um termo aditivo que também precisará ser assinado.'
                        ],
                        [
                            'question' => 'Como fazer rescisão de um termo?',
                            'answer' => 'Acesse o termo e clique em "Rescindir Termo". Informe a data de rescisão e o motivo. Um documento de rescisão será gerado automaticamente.'
                        ],
                        [
                            'question' => 'O sistema funciona offline?',
                            'answer' => 'Sim! O sistema é uma PWA (Progressive Web App) e permite consultas básicas offline. Porém, para salvar dados é necessário estar conectado.'
                        ],
                        [
                            'question' => 'Posso instalar o sistema no celular?',
                            'answer' => 'Sim! Acesse o sistema pelo navegador do seu celular e procure a opção "Adicionar à tela inicial" ou "Instalar app".'
                        ],
                        [
                            'question' => 'Quais navegadores são suportados?',
                            'answer' => 'Recomendamos o uso do Google Chrome, Mozilla Firefox, Microsoft Edge ou Safari para melhor experiência.'
                        ],
                        [
                            'question' => 'Como atualizar o sistema?',
                            'answer' => 'Acesse o menu "Suporte" e clique em "Atualizar".'
                        ],
                        [
                            'question' => 'Como entrar em contato com a equipe?',
                            'answer' => 'Acesse o menu "Suporte" e clique em "Contato".'
                        ]                
                    ]
                ]
            ]
        ];

        return view('ajuda.index', compact('sections'));
    }
}
