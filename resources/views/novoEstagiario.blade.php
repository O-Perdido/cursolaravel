@extends('layouts.main')

@section('title', 'Cadastrar Estagiário')

@section('content')

    <style>
        /* From Uiverse.io by Nawsome */
        .typewriter {
            --blue: #5C86FF;
            --blue-dark: #275EFE;
            --key: #fff;
            --paper: #EEF0FD;
            --text: #D3D4EC;
            --tool: #FBC56C;
            --duration: 3s;
            position: relative;
            -webkit-animation: bounce05 var(--duration) linear infinite;
            animation: bounce05 var(--duration) linear infinite;
        }

        .typewriter .slide {
            width: 92px;
            height: 20px;
            border-radius: 3px;
            margin-left: 14px;
            transform: translateX(14px);
            background: linear-gradient(var(--blue), var(--blue-dark));
            -webkit-animation: slide05 var(--duration) ease infinite;
            animation: slide05 var(--duration) ease infinite;
        }

        .typewriter .slide:before,
        .typewriter .slide:after,
        .typewriter .slide i:before {
            content: "";
            position: absolute;
            background: var(--tool);
        }

        .typewriter .slide:before {
            width: 2px;
            height: 8px;
            top: 6px;
            left: 100%;
        }

        .typewriter .slide:after {
            left: 94px;
            top: 3px;
            height: 14px;
            width: 6px;
            border-radius: 3px;
        }

        .typewriter .slide i {
            display: block;
            position: absolute;
            right: 100%;
            width: 6px;
            height: 4px;
            top: 4px;
            background: var(--tool);
        }

        .typewriter .slide i:before {
            right: 100%;
            top: -2px;
            width: 4px;
            border-radius: 2px;
            height: 14px;
        }

        .typewriter .paper {
            position: absolute;
            left: 24px;
            top: -26px;
            width: 40px;
            height: 46px;
            border-radius: 5px;
            background: var(--paper);
            transform: translateY(46px);
            -webkit-animation: paper05 var(--duration) linear infinite;
            animation: paper05 var(--duration) linear infinite;
        }

        .typewriter .paper:before {
            content: "";
            position: absolute;
            left: 6px;
            right: 6px;
            top: 7px;
            border-radius: 2px;
            height: 4px;
            transform: scaleY(0.8);
            background: var(--text);
            box-shadow: 0 12px 0 var(--text), 0 24px 0 var(--text), 0 36px 0 var(--text);
        }

        .typewriter .keyboard {
            width: 120px;
            height: 56px;
            margin-top: -10px;
            z-index: 1;
            position: relative;
        }

        .typewriter .keyboard:before,
        .typewriter .keyboard:after {
            content: "";
            position: absolute;
        }

        .typewriter .keyboard:before {
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 7px;
            background: linear-gradient(135deg, var(--blue), var(--blue-dark));
            transform: perspective(10px) rotateX(2deg);
            transform-origin: 50% 100%;
        }

        .typewriter .keyboard:after {
            left: 2px;
            top: 25px;
            width: 11px;
            height: 4px;
            border-radius: 2px;
            box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            -webkit-animation: keyboard05 var(--duration) linear infinite;
            animation: keyboard05 var(--duration) linear infinite;
        }

        @keyframes bounce05 {

            85%,
            92%,
            100% {
                transform: translateY(0);
            }

            89% {
                transform: translateY(-4px);
            }

            95% {
                transform: translateY(2px);
            }
        }

        @keyframes slide05 {
            5% {
                transform: translateX(14px);
            }

            15%,
            30% {
                transform: translateX(6px);
            }

            40%,
            55% {
                transform: translateX(0);
            }

            65%,
            70% {
                transform: translateX(-4px);
            }

            80%,
            89% {
                transform: translateX(-12px);
            }

            100% {
                transform: translateX(14px);
            }
        }

        @keyframes paper05 {
            5% {
                transform: translateY(46px);
            }

            20%,
            30% {
                transform: translateY(34px);
            }

            40%,
            55% {
                transform: translateY(22px);
            }

            65%,
            70% {
                transform: translateY(10px);
            }

            80%,
            85% {
                transform: translateY(0);
            }

            92%,
            100% {
                transform: translateY(46px);
            }
        }

        @keyframes keyboard05 {

            5%,
            12%,
            21%,
            30%,
            39%,
            48%,
            57%,
            66%,
            75%,
            84% {
                box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            }

            9% {
                box-shadow: 15px 2px 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            }

            18% {
                box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 2px 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            }

            27% {
                box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 12px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            }

            36% {
                box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 12px 0 var(--key), 60px 12px 0 var(--key), 68px 12px 0 var(--key), 83px 10px 0 var(--key);
            }

            45% {
                box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 2px 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            }

            54% {
                box-shadow: 15px 0 0 var(--key), 30px 2px 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            }

            63% {
                box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 12px 0 var(--key);
            }

            72% {
                box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 2px 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            }

            81% {
                box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 12px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
            }
        }
    </style>

    <style>
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
                text-align: center;

            }
        }
    </style>

    <h1 style="margin-top: -30px;">Fomulário de Cadastro de Estagiário</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @php
            $dataNascimentoValor = old('data_nascimento');
            if (empty($dataNascimentoValor) && isset($estagiario) && !empty($estagiario->data_nascimento)) {
                if (strpos($estagiario->data_nascimento, '/') !== false) {
                    try {
                        $dataNascimentoValor = \Carbon\Carbon::createFromFormat('d/m/Y', $estagiario->data_nascimento)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $dataNascimentoValor = null;
                    }
                } else {
                    try {
                        $dataNascimentoValor = \Carbon\Carbon::parse($estagiario->data_nascimento)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $dataNascimentoValor = null;
                    }
                }
            }
            $dataNascimentoObj = null;

            if (!empty($dataNascimentoValor)) {
                try {
                    $dataNascimentoObj = \Carbon\Carbon::parse($dataNascimentoValor);
                } catch (\Throwable $exception) {
                    $dataNascimentoObj = null;
                }
            }

            $diaSelecionado = $dataNascimentoObj ? (int) $dataNascimentoObj->day : null;
            $mesSelecionado = $dataNascimentoObj ? (int) $dataNascimentoObj->month : null;
            $anoSelecionado = $dataNascimentoObj ? (int) $dataNascimentoObj->year : null;
            
            $meses = [
                1 => 'Janeiro',
                2 => 'Fevereiro',
                3 => 'Março',
                4 => 'Abril',
                5 => 'Maio',
                6 => 'Junho',
                7 => 'Julho',
                8 => 'Agosto',
                9 => 'Setembro',
                10 => 'Outubro',
                11 => 'Novembro',
                12 => 'Dezembro',
            ];
        @endphp
        <form action="{{ route('novo-estagiario-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div class="row">
                <!-- Coluna 1 -->
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="possui_nome_social" name="possui_nome_social" value="1">
                        <label class="form-check-label" for="possui_nome_social">Possui nome social?</label>
                    </div>
                    <div class="form-group">
                        <label for="nome_estagiario" id="label_nome_estagiario">Nome do Estagiário</label>
                        <input type="text" class="form-control" id="nome_estagiario" name="nome_estagiario" required
                            oninput="this.value = this.value.toUpperCase()">
                    </div>                    
                    <div class="form-group" id="grupo_nome_secundario" style="display: none;">
                        <label for="nome_secundario">Nome Civil</label>
                        <input type="text" class="form-control" id="nome_secundario" name="nome_secundario"
                            oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label for="nome_mae">Nome da Mãe</label>
                        <input type="text" class="form-control" id="nome_mae" name="nome_mae">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                                <input type="hidden" id="data_nascimento" name="data_nascimento" value="{{ $dataNascimentoValor }}" required>
                                <div class="row g-1">
                                    <div class="col-4" style="padding-right: 2px;">
                                        <select class="form-control" id="data_nascimento_dia" required>
                                            <option value="">Dia</option>
                                            @for ($dia = 1; $dia <= 31; $dia++)
                                                <option value="{{ $dia }}" {{ $diaSelecionado === $dia ? 'selected' : '' }}>
                                                    {{ str_pad((string) $dia, 2, '0', STR_PAD_LEFT) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-4" style="padding-left: 2px; padding-right: 2px;">
                                        <select class="form-control" id="data_nascimento_mes" required>
                                            <option value="">Mês</option>
                                            @foreach ($meses as $numeroMes => $nomeMes)
                                                <option value="{{ $numeroMes }}" {{ $mesSelecionado === $numeroMes ? 'selected' : '' }}>
                                                    {{ $nomeMes }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4" style="padding-left: 2px;">
                                        <select class="form-control" id="data_nascimento_ano" required>
                                            <option value="">Ano</option>
                                            @for ($ano = now()->year; $ano >= 1900; $ano--)
                                                <option value="{{ $ano }}" {{ $anoSelecionado === $ano ? 'selected' : '' }}>
                                                    {{ $ano }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            (function() {
                                const diaSel = document.getElementById('data_nascimento_dia');
                                const mesSel = document.getElementById('data_nascimento_mes');
                                const anoSel = document.getElementById('data_nascimento_ano');
                                const hiddenInput = document.getElementById('data_nascimento');

                                function updateHiddenInput() {
                                    const dia = diaSel.value;
                                    const mes = mesSel.value;
                                    const ano = anoSel.value;

                                    if (dia && mes && ano) {
                                        const padDia = dia.toString().padStart(2, '0');
                                        const padMes = mes.toString().padStart(2, '0');
                                        hiddenInput.value = `${ano}-${padMes}-${padDia}`;
                                    } else {
                                        hiddenInput.value = '';
                                    }
                                    hiddenInput.dispatchEvent(new Event('change'));
                                }

                                diaSel.addEventListener('change', updateHiddenInput);
                                mesSel.addEventListener('change', updateHiddenInput);
                                anoSel.addEventListener('change', updateHiddenInput);
                            })();
                        </script>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_cpf">CPF</label>
                                <input type="text" class="form-control" id="numero_cpf" name="numero_cpf" required>
                                <div class="invalid-feedback" id="cpfError" style="display: none;">CPF inválido.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_telefone">Número de Telefone</label>
                                <input type="text" class="form-control" id="numero_telefone" name="numero_telefone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_celular">Número de Celular</label>
                                <input type="text" class="form-control" id="numero_celular" name="numero_celular">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback" id="emailError" style="display: none;">Por favor, insira um e-mail válido.
                        </div>
                    </div>
                    <div class="row" style="padding-top: 5px;">
                        <div class="col-md-4">
                            <div class="form-group text-center">
                                <label class="font-weight-bold mb-2" style="margin-top: 5px">
                                    <i class="fas fa-id-card fa-lg mr-1" style="margin-right: 5px"></i> Documento de Identidade
                                </label>
                                <input type="file" class="d-none" id="foto_documento" name="foto_documento">
                                <label class="btn btn-outline-primary w-100" for="foto_documento"
                                    style="cursor:pointer; white-space:normal; word-break:break-all;">
                                    <i class="fas fa-upload mr-1"></i>
                                    <span id="label_foto_documento"
                                        style="display:inline-block; max-width:100%; white-space:normal; word-break:break-all;">Escolher
                                        arquivo</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group text-center">
                                <label class="font-weight-bold mb-2" style="margin-top: 5px">
                                    <i class="fas fa-home fa-lg mr-1" style="margin-right: 5px"></i> Comprovante de Residência
                                </label>
                                <input type="file" class="d-none" id="comprovante_residencia" name="comprovante_residencia">
                                <label class="btn btn-outline-primary w-100" for="comprovante_residencia"
                                    style="cursor:pointer; white-space:normal; word-break:break-all;">
                                    <i class="fas fa-upload mr-1"></i>
                                    <span id="label_comprovante_residencia"
                                        style="display:inline-block; max-width:100%; white-space:normal; word-break:break-all;">Escolher
                                        arquivo</span>
                                </label>
                            </div>
                        </div>
                        <style>
                            /* Contêiner principal do tooltip */
                            .tooltip-container {
                                position: relative;
                                display: inline-block;
                                margin-left: 10px;
                                vertical-align: middle;
                            }

                            /* Estilo do ícone */
                            .icon {
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                cursor: pointer;
                                transition: transform 0.3s ease;
                            }

                            /* Animação do ícone ao passar o mouse */
                            .icon:hover {
                                transform: scale(1.2);
                            }

                            /* Estilo da caixa de texto do tooltip */
                            .tooltip {
                                visibility: hidden;
                                width: 220px;
                                background-color: #333;
                                color: #fff;
                                text-align: center;
                                border-radius: 6px;
                                padding: 10px;
                                position: absolute;
                                z-index: 1;
                                bottom: 150%;
                                left: 100%;
                                margin-left: 10px;
                                transform: translateX(0);
                                opacity: 0;
                                transition: opacity 0.4s;
                            }

                            /* Setinha para baixo no tooltip */
                            .tooltip::after {
                                content: "";
                                position: absolute;
                                top: 100%;
                                left: 10px;
                                border-width: 5px;
                                border-style: solid;
                                border-color: #333 transparent transparent transparent;
                            }

                            /* Mostra o tooltip quando o mouse passa por cima do contêiner */
                            .tooltip-container:hover .tooltip {
                                visibility: visible;
                                opacity: 1;
                            }
                        </style>

                        <div class="col-md-4">
                            <div class="form-group text-center">

                                <label class="font-weight-bold mb-2 d-flex justify-content-center align-items-center"
                                    style="margin-top: 5px">
                                    <i class="fas fa-graduation-cap fa-lg mr-2" style="margin-right: 5px"></i> <span>Comprovante
                                        de Matrícula</span>

                                    <div class="tooltip-container ml-2">
                                        <div class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                                fill="#007bff">
                                                <path
                                                    d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22c-5.518 0-10-4.482-10-10s4.482-10 10-10 10 4.482 10 10-4.482 10-10 10zm-1-16h2v6h-2zm0 8h2v2h-2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="tooltip">
                                            <p>Anexe um documento atual que comprove seu vínculo com a instituição de ensino,
                                                como o <strong>atestado de matrícula</strong> ou a <strong>declaração de
                                                    frequência</strong>. Válido para ensino médio, técnico e superior.</p>
                                        </div>
                                    </div>
                                </label>
                                <input type="file" class="d-none" id="comprovante_escolar" name="comprovante_escolar">
                                <label class="btn btn-outline-primary w-100" for="comprovante_escolar"
                                    style="cursor:pointer; white-space:normal; word-break:break-all;">
                                    <i class="fas fa-upload mr-1"></i>
                                    <span id="label_comprovante_escolar"
                                        style="display:inline-block; max-width:100%; white-space:normal; word-break:break-all;">
                                        Escolher arquivo
                                    </span>
                                </label>

                            </div>
                        </div>
                    </div>
                    <script>
                        // Atualiza o nome do arquivo selecionado no label
                        document.getElementById('foto_documento').addEventListener('change', function (e) {
                            let fileName = e.target.files[0] ? e.target.files[0].name : 'Escolher arquivo';
                            document.getElementById('label_foto_documento').textContent = fileName;
                        });
                        document.getElementById('comprovante_residencia').addEventListener('change', function (e) {
                            let fileName = e.target.files[0] ? e.target.files[0].name : 'Escolher arquivo';
                            document.getElementById('label_comprovante_residencia').textContent = fileName;
                        });
                        document.getElementById('comprovante_escolar').addEventListener('change', function (e) {
                            let fileName = e.target.files[0] ? e.target.files[0].name : 'Escolher arquivo';
                            document.getElementById('label_comprovante_escolar').textContent = fileName;
                        });
                    </script>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-top: 5px;">
                                <label for="numero_pis" class="d-flex align-items-center">
                                    Número PIS
                                    <div class="tooltip-container ml-2">
                                        <div class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                                fill="#007bff">
                                                <path
                                                    d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22c-5.518 0-10-4.482-10-10s4.482-10 10-10 10 4.482 10 10-4.482 10-10 10zm-1-16h2v6h-2zm0 8h2v2h-2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="tooltip">
                                            <p>Você pode localizar o número do PIS no app <strong>"Meu INSS"</strong>, clicando
                                                no <strong>"Perfil"</strong>, vai estar na parte cadastral como
                                                <strong>"NIT"</strong>.
                                            </p>
                                        </div>
                                    </div>
                                </label>
                                <input type="text" class="form-control" id="numero_pis" name="numero_pis">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="margin-top: 5px;">
                                <label for="chave_pix">Chave PIX</label>
                                <input type="text" class="form-control" id="chave_pix" name="chave_pix"
                                    value="{{ old('chave_pix', $estagiario->chave_pix ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-top: 5px;">
                                <label for="tipo_chave_pix">Tipo da Chave PIX</label>
                                <select class="form-control" id="tipo_chave_pix" name="tipo_chave_pix">
                                    <option value="">Selecione o tipo</option>
                                    <option value="CPF" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix ?? '') == 'CPF' ? 'selected' : '' }}>CPF</option>
                                    <option value="EMAIL" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix ?? '') == 'EMAIL' ? 'selected' : '' }}>EMAIL</option>
                                    <option value="TELEFONE" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix ?? '') == 'TELEFONE' ? 'selected' : '' }}>TELEFONE</option>
                                    <option value="ALEATORIA" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix ?? '') == 'ALEATORIA' ? 'selected' : '' }}>ALEATÓRIA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna 2 -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero_endereco">Número do Endereço</label>
                            <input type="text" class="form-control" id="numero_endereco" name="numero_endereco">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero_cep">CEP</label>
                            <input type="text" class="form-control" id="numero_cep" name="numero_cep" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="complemento_endereco">Complemento</label>
                            <input type="text" class="form-control" id="complemento_endereco" name="complemento_endereco">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bairro">Bairro</label>
                            <input type="text" class="form-control" id="bairro" name="bairro">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fk_id_estado">Estado</label>
                            <select class="form-control" id="fk_id_estado" name="fk_id_estado" required>
                                <option value="">Escolha um estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}">{{ $estado->nm_estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fk_id_cidade">Cidade</label>
                            <select class="form-control" id="fk_id_cidade" name="fk_id_cidade" required>
                                <option value="">Escolha uma cidade</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="instituicao_ensino">Instituição de Ensino</label>
                    <input type="text" class="form-control" id="instituicao_ensino" name="instituicao_ensino" required
                        value="{{ old('instituicao_ensino', $estagiario->instituicao_ensino ?? '') }}">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nivel_curso">Nível do Curso</label>
                            <select class="form-control" id="nivel_curso" name="nivel_curso" required>
                                <option value="">Escolha um nível</option>
                                <option value="Ensino Médio" {{ old('nivel_curso') == 'Ensino Médio' ? 'selected' : '' }}>Ensino Médio</option>
                                <option value="Técnico" {{ old('nivel_curso') == 'Técnico' ? 'selected' : '' }}>Técnico</option>
                                <option value="Graduação" {{ old('nivel_curso') == 'Graduação' ? 'selected' : '' }}>Graduação</option>
                                <option value="Pós Graduação" {{ old('nivel_curso') == 'Pós Graduação' ? 'selected' : '' }}>Pós Graduação</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="curso">Curso</label>
                            <input type="text" class="form-control" id="curso" name="curso" required value="{{ old('curso') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">

                        <!-- From Uiverse.io by SelfMadeSystem -->
                        <style>
                            /* From Uiverse.io by SelfMadeSystem */
                            .containercheckbox {
                                cursor: pointer;
                            }

                            .containercheckbox input {
                                display: none;
                            }

                            .containercheckbox svg {
                                overflow: visible;
                            }

                            .path {
                                fill: none;
                                stroke: #102e6c;
                                stroke-width: 11;
                                stroke-linecap: round;
                                stroke-linejoin: round;
                                transition: stroke-dasharray 0.5s ease, stroke-dashoffset 0.5s ease;
                                stroke-dasharray: 241 9999999;
                                stroke-dashoffset: 0;
                            }

                            .containercheckbox input:checked~svg .path {
                                stroke-dasharray: 70.5096664428711 9999999;
                                stroke-dashoffset: -262.2723388671875;
                            }
                        </style>
                        <div class="row">
                            <div class="form-group d-flex justify-content-center" style="margin-top: 8px; margin-bottom: 8px;">
                                <table>
                                    <tr>
                                        <td>
                                            <label class="containercheckbox" style="margin-right: 10px;">
                                                <input type="checkbox" id="aceitacao_termos" name="aceitacao_termos">
                                                <svg viewBox="0 0 64 64" height="1.7em" width="1.7em">
                                                    <path
                                                        d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                        pathLength="575.0541381835938" class="path"></path>
                                                </svg>
                                            </label>
                                        </td>
                                        <td>
                                            Aceito os Termos/Condições.
                                            <a style="text-decoration: none" href="#" id="abrirModal" data-toggle="modal"
                                                data-target="#exampleModal">
                                                <br>(Ler termos)
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div style="padding-top: 8px; padding-bottom: 8px;">
                            <div class="d-flex justify-content-center">
                                <button style="width: 100%;" type="submit" class="btn btn-primary" id="cadastrarBtn"
                                    disabled>Realizar Cadastro</button>
                            </div>
                        </div>
                    </div>

                    <div id="form-loading"
                        style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(255,255,255,0.95); z-index:9999; justify-content:center; align-items:center; flex-direction:column;">
                        <div class="typewriter">
                            <div class="slide"><i></i></div>
                            <div class="paper"></div>
                            <div class="keyboard"></div>
                        </div>
                        <div style="margin-top:20px; text-align:center;">
                            <strong>Carregando...</strong><br>
                            <span>Não feche ou recarregue a página</span>
                        </div>
                    </div>

                </div>
            </div>
            </div>
        </form>

        <script>

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form'); // Selecione o formulário correto se houver mais de um
                const possuiNomeSocialCheckbox = document.getElementById('possui_nome_social');
                const grupoNomeSecundario = document.getElementById('grupo_nome_secundario');
                const nomeSecundarioInput = document.getElementById('nome_secundario');
                const labelNomeEstagiario = document.getElementById('label_nome_estagiario');

                function atualizarCamposNomeSocial() {
                    const ativo = possuiNomeSocialCheckbox.checked;
                    grupoNomeSecundario.style.display = ativo ? 'block' : 'none';
                    nomeSecundarioInput.required = ativo;
                    labelNomeEstagiario.textContent = ativo ? 'Nome Social' : 'Nome do Estagiário';

                    if (!ativo) {
                        nomeSecundarioInput.value = '';
                    }
                }

                possuiNomeSocialCheckbox.addEventListener('change', atualizarCamposNomeSocial);
                atualizarCamposNomeSocial();

                if (form) {
                    form.addEventListener('submit', function () {
                        document.getElementById('form-loading').style.display = 'flex';
                    });
                }
            });

            document.getElementById('fk_id_estado').addEventListener('change', function () {
                const estadoId = this.value;
                const cidadesSelect = document.getElementById('fk_id_cidade');

                // Limpar opções de cidades
                cidadesSelect.innerHTML = '<option value="">Selecione uma cidade</option>';

                if (estadoId) {
                    fetch(`/estados/${estadoId}/cidades`)
                        .then(response => response.json())
                        .then(cidades => {
                            cidades.forEach(cidade => {
                                const option = document.createElement('option');
                                option.value = cidade.id_cidade;
                                option.text = cidade.nm_cidade;
                                cidadesSelect.appendChild(option);
                            });
                        });
                }
            });

            function validarCPF(cpf) {
                cpf = cpf.replace(/[^\d]+/g, ''); // Remove caracteres não numéricos

                if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
                    return false; // Verifica se o CPF tem 11 dígitos e não é uma sequência repetida
                }

                let soma = 0;
                let resto;

                // Validação do primeiro dígito verificador
                for (let i = 1; i <= 9; i++) {
                    soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
                }
                resto = (soma * 10) % 11;
                if (resto === 10 || resto === 11) resto = 0;
                if (resto !== parseInt(cpf.substring(9, 10))) return false;

                soma = 0;

                // Validação do segundo dígito verificador
                for (let i = 1; i <= 10; i++) {
                    soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
                }
                resto = (soma * 10) % 11;
                if (resto === 10 || resto === 11) resto = 0;
                if (resto !== parseInt(cpf.substring(10, 11))) return false;

                return true;
            }

            function checarCamposValidos() {
                const cpfValido = validarCPF(document.getElementById('numero_cpf').value);
                const termosAceitos = document.getElementById('aceitacao_termos').checked;
                document.getElementById('cadastrarBtn').disabled = !(cpfValido && termosAceitos);
            }

            document.getElementById('numero_cpf').addEventListener('input', function () {
                const cpfValido = validarCPF(this.value);
                const cpfError = document.getElementById('cpfError');
                if (!cpfValido && this.value.length > 0) {
                    this.classList.add('is-invalid');
                    cpfError.style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    cpfError.style.display = 'none';
                }
                checarCamposValidos();
            });

            document.getElementById('aceitacao_termos').addEventListener('change', function () {
                checarCamposValidos();
            });

            window.addEventListener('DOMContentLoaded', function () {
                checarCamposValidos();
            });
        </script>
    @endif


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Termos e Condições</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" style="border: none; background: none;">
                        <span aria-hidden="true" style="font-weight: bold; font-size: 150%;">✕</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p style="text-align: justify;">&nbsp;Ao submeter este formulário, você está
                        concedendo autorização à EBCP Consultoria LTDA para acessar e
                        utilizar seus dados pessoais com finalidades específicas, relacionadas ao processo de
                        cadastro
                        para
                        vagas de estágio. Essa permissão se destina à efetivação do seu registro nas oportunidades
                        de
                        estágio disponíveis, bem como à elaboração do termo de compromisso de estágio, documento
                        formal
                        que
                        oficializa a ocupação da respectiva vaga de estágio. Essas medidas visam assegurar a
                        transparência e
                        a conformidade legal em todo o processo, garantindo a adequada gestão e documentação
                        relacionadas à
                        sua participação no programa de estágio oferecido pela EBCP Consultoria LTDA.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection