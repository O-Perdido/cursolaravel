@extends('layouts.main')

@section('title', 'Meus Contratos')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #2d3748; font-weight: 700;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                    class="bi bi-file-earmark-text me-2" viewBox="0 0 16 16">
                    <path
                        d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5" />
                    <path
                        d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                </svg>
                Meus Contratos
            </h2>
            <p class="text-muted mb-0">Visualize seus contratos de estágio e informações relacionadas</p>
        </div>
        <a href="{{ route('welcome.estagiario') }}" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
            </svg>
            Voltar
        </a>
    </div>

    <hr>

    @if($termos->isEmpty())
        <!-- Nenhum contrato encontrado -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body text-center py-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#cbd5e0" class="bi bi-inbox mb-3"
                    viewBox="0 0 16 16">
                    <path
                        d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4zm-1.17-.437A1.5 1.5 0 0 1 4.98 3h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a.5.5 0 0 1 .106.374l-.39 3.124A1.5 1.5 0 0 1 14.117 13H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374z" />
                </svg>
                <h4 class="text-muted">Nenhum contrato encontrado</h4>
                <p class="text-muted mb-0">Você ainda não possui contratos de estágio cadastrados no sistema.</p>
            </div>
        </div>
    @else
        <!-- Lista de Contratos -->
        <div class="row g-4">
            @foreach($termos as $termo)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100"
                        style="border-radius: 15px; overflow: hidden; transition: transform 0.2s;">

                        <!-- Header do Card com Status -->
                        @php
                            $status = $termo->rescisao ? 'Rescindido' : 'Ativo';
                            $bg = $status === 'Ativo'
                                ? 'linear-gradient(135deg, #1f7234 0%, #002e0c 100%)'
                                : 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)';
                        @endphp
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background: {{ $bg }}; color: white; padding: 18px 20px;">
                            <div style="flex: 1; min-width: 0;">
                                <h6 class="mb-0"
                                    style="font-weight: 600; font-size: 1.1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $termo->empresa ? $termo->empresa->nome_empresa : 'Empresa não especificada' }}
                                </h6>
                                <small style="opacity: 0.85; font-size: 1rem;">Termo nº {{ $termo->id_termo }}</small>
                            </div>
                            <span class="badge ms-2"
                                style="background: rgba(255,255,255,0.25); font-size: 0.85rem; padding: 6px 12px; white-space: nowrap;">
                                {{ $status }}
                            </span>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-3 mb-3">

                                <!-- Data de Início -->
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2"
                                            style="width: 36px; height: 36px; background: #e8f5e9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#27ae60"
                                                class="bi bi-calendar-check" viewBox="0 0 16 16">
                                                <path
                                                    d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                                <path
                                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                            </svg>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Início</small>
                                            <strong
                                                style="font-size: 0.9rem;">{{ $termo->data_inicio_estagio ? \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('d/m/Y') : '-' }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data de Término -->
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2"
                                            style="width: 36px; height: 36px; background: #ffebee; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#e74c3c"
                                                class="bi bi-calendar-x" viewBox="0 0 16 16">
                                                <path
                                                    d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708" />
                                                <path
                                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                            </svg>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Término</small>
                                            <strong
                                                style="font-size: 0.9rem;">{{ $termo->data_fim_estagio ? \Carbon\Carbon::parse($termo->data_fim_estagio)->format('d/m/Y') : '-' }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Valor da Bolsa -->
                                <div class="col-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2"
                                            style="width: 36px; height: 36px; background: #e3f2fd; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#102E6C"
                                                class="bi bi-currency-dollar" viewBox="0 0 16 16">
                                                <path
                                                    d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z" />
                                            </svg>
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">Bolsa</small>
                                            <strong style="font-size: 0.9rem;">R$
                                                {{ number_format($termo->valor_bolsa ?? 0, 2, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <hr class="my-3">

                            <!-- Ações -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('termos.gerarPdf', $termo->id_termo) }}" class="btn btn-primary" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-file-pdf me-2" viewBox="0 0 16 16">
                                        <path
                                            d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1" />
                                        <path
                                            d="M4.603 12.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.6 11.6 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.244.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 5.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z" />
                                    </svg>
                                    Baixar Contrato (PDF)
                                </a>

                                <a href="{{ route('estagiario.termo.detalhes', $termo->id_termo) }}"
                                    class="btn btn-outline-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-eye me-2" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                        <path
                                            d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                    </svg>
                                    Ver Todos os Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection