@extends('layouts.main')

@section('title', 'Esqueci minha senha')

@section('content')
    <div class="container mt-5 col-md-5">
        <h3 class="mb-3">Redefinir senha</h3>
        <div class="card card-body bg-light">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                        value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">Voltar</a>
                    <button type="submit" class="btn btn-primary">Enviar link</button>
                </div>
            </form>
        </div>
    </div>
@endsection