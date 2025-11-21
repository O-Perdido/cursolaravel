@extends('layouts.main')

@section('title', 'Verificar e-mail')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-white" style="background-color: #102E6C">
                        <h5 class="mb-0"><i class="fas fa-envelope-open-text mr-2"></i> Confirme seu e-mail</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="alert alert-info">{{ session('status') }}</div>
                        @endif

                        <p>Enviamos um código de 6 dígitos para <strong>{{ $maskedEmail }}</strong>.</p>

                        <form method="POST" action="{{ route('verification.verify') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="form-group">
                                <label for="code">Código de verificação</label>
                                <input type="text" class="form-control" id="code" name="code" maxlength="6" required
                                    autocomplete="one-time-code">
                                <small class="form-text text-muted">O código expira em 15 minutos.</small>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">Confirmar e-mail</button>
                        </form>

                        <hr>
                        <form method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-link p-0">Reenviar código</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection