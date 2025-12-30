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
                    'description' => 'Bem-vindo à Central de Ajuda do SIGEBR - EBCP. Aqui você encontrará tutoriais, guias e respostas para as principais dúvidas sobre o sistema de gestão de estágios.',
                    'video' => null,
                    'images' => []
                ]
            ],
            [
                'id' => 'primeiro-acesso',
                'title' => 'Primeiro Acesso',
                'icon' => 'fa-user-plus',
                'content' => [
                    'description' => 'Aprenda como fazer seu primeiro acesso ao sistema, criar sua senha e configurar seu perfil.',
                    'video' => null, // Exemplo: 'https://www.youtube.com/embed/x9QIa5JF72o?si=ZGZ6E3fPCsMJbvAX'
                    'steps' => [
                        'Acesse o sistema através do link enviado por e-mail',
                        'Clique em "Primeiro Acesso" na tela de login',
                        'Insira seu e-mail cadastrado no sistema',
                        'Verifique seu e-mail e insira o código de verificação',
                        'Crie uma senha segura (mínimo 8 caracteres)',
                        'Complete seu cadastro com as informações solicitadas'
                    ],
                    'images' => []
                ]
            ],
            [
                'id' => 'cadastro-termos',
                'title' => 'Cadastro de Termos de Estágio',
                'icon' => 'fa-file-contract',
                'content' => [
                    'description' => 'Como cadastrar e gerenciar termos de estágio no sistema.',
                    'video' => null,
                    'steps' => [
                        'Acesse o menu "Termos de Contrato"',
                        'Clique no botão "Novo Termo"',
                        'Preencha os dados do estagiário',
                        'Informe os dados da empresa concedente',
                        'Configure valores de bolsa e auxílios',
                        'Defina as datas de início e término',
                        'Revise todas as informações',
                        'Clique em "Salvar" para gerar o termo'
                    ],
                    'alert' => [
                        'type' => 'info',
                        'text' => 'Após salvar, o termo será enviado automaticamente para assinatura digital via ZapSign.'
                    ]
                ]
            ],
            [
                'id' => 'assinatura-digital',
                'title' => 'Assinatura Digital de Termos',
                'icon' => 'fa-pen-fancy',
                'content' => [
                    'description' => 'Entenda o processo de assinatura digital dos termos de estágio.',
                    'video' => null,
                    'steps' => [
                        'Após o cadastro, o termo é enviado automaticamente para assinatura',
                        'Todos os signatários recebem um e-mail com o link',
                        'Acesse o link e visualize o documento',
                        'Clique em "Assinar Documento"',
                        'Confirme sua assinatura eletrônica',
                        'Aguarde as demais assinaturas'
                    ],
                    'alert' => [
                        'type' => 'warning',
                        'text' => 'O termo só terá validade após todas as assinaturas necessárias.'
                    ]
                ]
            ],
            [
                'id' => 'folha-pagamento',
                'title' => 'Folha de Pagamento',
                'icon' => 'fa-money-check-alt',
                'content' => [
                    'description' => 'Como criar e processar folhas de pagamento dos estagiários.',
                    'video' => null,
                    'steps' => [
                        'Acesse "Folhas de Pagamento" no menu',
                        'Clique em "Nova Folha"',
                        'Selecione o mês de referência',
                        'Configure os dias úteis do mês',
                        'Defina o tipo de cálculo do auxílio transporte',
                        'Adicione os estagiários à folha',
                        'Informe os dias trabalhados de cada um',
                        'Revise os cálculos automáticos',
                        'Finalize a folha para gerar a remessa bancária'
                    ],
                    'alert' => [
                        'type' => 'info',
                        'text' => 'O sistema calcula automaticamente os valores de bolsa, auxílios e taxa administrativa.'
                    ]
                ]
            ],
            [
                'id' => 'recesso',
                'title' => 'Concessão de Recesso',
                'icon' => 'fa-umbrella-beach',
                'content' => [
                    'description' => 'Como conceder recesso remunerado para estagiários.',
                    'video' => null,
                    'steps' => [
                        'Acesse a tela de detalhes do termo',
                        'Clique na aba "Recesso"',
                        'Verifique o saldo disponível de dias',
                        'Clique em "Conceder Recesso"',
                        'Informe as datas de início e fim',
                        'O sistema valida automaticamente o saldo',
                        'Confirme a concessão',
                        'Um PDF será gerado para registro'
                    ],
                    'alert' => [
                        'type' => 'warning',
                        'text' => 'O saldo de recesso é proporcional ao tempo de estágio. Verifique sempre antes de conceder.'
                    ]
                ]
            ],
            [
                'id' => 'relatorios',
                'title' => 'Relatórios e Exportações',
                'icon' => 'fa-file-excel',
                'content' => [
                    'description' => 'Como gerar relatórios e exportar dados do sistema.',
                    'video' => null,
                    'steps' => [
                        'Navegue até a listagem desejada (Termos, Estagiários, etc.)',
                        'Use os filtros para refinar os dados',
                        'Clique no botão "Exportar"',
                        'Escolha o formato (Excel ou PDF)',
                        'O arquivo será baixado automaticamente'
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
