@extends('layouts.main')

@section('title', 'Redefinir senha')

@section('content')
    <div class="container mt-5 col-md-5">
        <h3 class="mb-3">Defina uma nova senha</h3>
        <div class="card card-body bg-light">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="mb-3">
                    <label for="password" class="form-label">Nova senha</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirme a nova senha</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Redefinir senha</button>
                </div>
            </form>
        </div>
    </div>
@endsection