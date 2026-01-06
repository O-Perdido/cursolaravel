@extends('layouts.main')

@section('title', 'Página Inicial')

@section('content')

    @if(session('error'))
        <div class="alert alert-danger mt-2">{{ session('error') }}</div>
    @endif

    <div class="container"
        style="background-color:rgb(242, 242, 242); border-radius: 15px; margin-top: -30px; padding-top: 5px; padding-left: 15px; margin-bottom: 20px;">
        <div class="row">
            <div class="col">
                <h3>Bem-vindo</h3>
                <h3>{{ Auth::user()->name }}!</h3>
            </div>
            <div class="col-md-7">
                <p class="lead">Este é o painel principal do sistema de gestão. Aqui você pode acessar as principais
                    funcionalidades de acordo com o seu nível de acesso.</p>
            </div>
        </div>
    </div>
    <hr style="margin-top: -10px; background-color: #102e6c;">
    <div class="row" style="margin-top: -5px;">
        <p>Utilize os botões abaixo ou o menu de navegação para explorar as opções disponíveis.</p>
    </div>

    <div class="row">
        <div class="col-8">
            <div class="row row-cols-2">
                <div class="col">
                    <div class="card text-center" style="margin-bottom: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">Instituições de Ensino</h5>
                            <p class="card-text">Visualize a lista de instituições de ensino.</p>
                            <a href="{{ route('escolas.index') }}" class="btn btn-primary">Ver Instituições de Ensino</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center" style="margin-bottom: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">Unidades Concedentes</h5>
                            <p class="card-text">Visualize a lista de empresas concedentes.</p>
                            <a href="{{ route('empresas.index') }}" class="btn btn-primary">Ver Unidades Concedentes</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center" style="margin-bottom: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">Estagiários</h5>
                            <p class="card-text">Visualize a lista de estagiários.</p>
                            <a href="{{ route('estagiarios.index') }}" class="btn btn-primary">Ver Estagiários</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center" style="margin-bottom: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">Supervisores</h5>
                            <p class="card-text">Visualize a lista de supervisores.</p>
                            <a href="{{ route('supervisores.index') }}" class="btn btn-primary">Ver Supervisores</a>
                        </div>
                    </div>
                </div>
                {{-- <div class="col">
                    <div class="card text-center" style="margin-bottom: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">Vagas de Estágio</h5>
                            <p class="card-text">Visualize e gerencie as vagas.</p>
                            <a href="{{ route('vagas.index') }}" class="btn btn-primary">Ver Vagas</a>
                        </div>
                    </div>
                </div> --}}
                <div class="col">
                    <div class="card text-center" style="margin-bottom: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">Termos de Contrato</h5>
                            <p class="card-text">Visualize a lista de termos de contrato.</p>
                            <a href="{{ route('termos.index') }}" class="btn btn-primary">Ver Termos</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center" style="margin-bottom: 10px;">
                        <div class="card-body">
                            <h5 class="card-title">Folhas de Pagamento</h5>
                            <p class="card-text">Visualize a lista de folhas de pagamento.</p>
                            <a href="{{ route('folhas.index') }}" class="btn btn-primary">Ver Folhas</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card" style="max-height: 480px;">
                <div class="card-header">
                    <div class="row row-cols-2 align-items-center">
                        <div class="col">
                            Termos A Vencer
                        </div>
                        <div class="row align-items-center">
                            <select id="diasVencimento" class="form-select mb-3" style="font-size: 0.8em;"
                                title="Dias até o vencimento">
                                <option value="15" selected>Próximos 15 dias</option>
                                <option value="10">Próximos 10 dias</option>
                                <option value="5">Próximos 5 dias</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text">Selecione o período para visualizar os termos que estão próximos do vencimento.</p>
                    <ul id="termosList" class="list-group" style="max-height: 320px; overflow-y: auto;">
                        <!-- A lista será preenchida via JS -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const termosList = document.getElementById('termosList');
            const diasVencimento = document.getElementById('diasVencimento');

            // Array de termos vindo do backend
            const termos = [
                @foreach ($termos as $termo)
                                                {
                        id: {{ $termo->id_termo }},
                        numero: '{{ $termo->numero_termo }}',
                        ano: '{{ $termo->ano_termo }}',
                        estagiario: '{{ isset($termo->estagiario) ? e($termo->estagiario->nome_estagiario) : "N/A" }}',
                        empresa: '{{ isset($termo->empresa) ? e($termo->empresa->nome_empresa) : "N/A" }}',
                        data_fim: '{{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('Y-m-d') }}',
                        data_fim_formatada: '{{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('d/m/Y') }}',
                        url: '{{ route('termos.show', $termo->id_termo) }}'
                    },
                @endforeach
                            ];

            function filtrarTermos() {
                const dias = parseInt(diasVencimento.value);
                const hoje = new Date();
                const dataLimite = new Date();
                dataLimite.setDate(hoje.getDate() + dias);

                termosList.innerHTML = '';

                const filtrados = termos.filter(termo => {
                    const dataFim = new Date(termo.data_fim + 'T23:59:59');
                    return dataFim >= hoje && dataFim <= dataLimite;
                }).sort((a, b) => {
                    // Ordena do mais próximo para o mais distante
                    return new Date(a.data_fim) - new Date(b.data_fim);
                });

                if (filtrados.length === 0) {
                    termosList.innerHTML = '<li class="list-group-item text-center text-muted"><i class="bi bi-info-circle"></i> Nenhum termo perto de vencer neste período.</li>';
                } else {
                    filtrados.forEach(termo => {
                        const li = document.createElement('a');
                        li.href = termo.url;
                        li.className = 'list-group-item d-flex justify-content-between align-items-center flex-wrap';
                        li.style.textDecoration = 'none';
                        li.style.color = 'inherit';
                        li.style.transition = 'all 0.2s ease';
                        li.innerHTML = `
                                            <div class="d-flex align-items-center" style="width: 100%;">
                                                <div>
                                                    <div class="fw-bold text-muted">${termo.empresa}</div>
                                                    <div class="small text-secondary mb-1">Vencimento: <span class="badge bg-danger">${termo.data_fim_formatada}</span></div>
                                                    <div class="small text-muted" style="font-size: 0.85em;"><i class="bi bi-person"></i> ${termo.estagiario}</div>
                                                </div>
                                            </div>                                            
                                        `;
                        
                        // Animação de hover
                        li.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#f0f0f0';
                            this.style.transform = 'translateX(5px)';
                            this.style.borderLeft = '4px solid #102e6c';
                        });
                        
                        li.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = '';
                            this.style.transform = '';
                            this.style.borderLeft = '';
                        });
                        
                        termosList.appendChild(li);
                    });
                }
            }

            diasVencimento.addEventListener('change', filtrarTermos);

            // Filtra ao carregar a página
            filtrarTermos();
        });
    </script>
    <!-- Adicione o Bootstrap Icons no seu layout principal, se ainda não tiver: -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection