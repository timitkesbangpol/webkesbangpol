@extends('auth.auth-page')

@section('title', 'Login')

@section('auth_body')
    <link rel="stylesheet" href="{{ asset('assets/css/login-form.css') }}">

    @php
        $loginUrl = route('login');
        $registerUrl = Route::has('register') ? route('register') : null;
        $passResetUrl = Route::has('password.request') ? route('password.request') : null;
    @endphp

    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <div class="login-card">
            {{-- Logo Kesbangpol --}}
            <div class="login-logo">
                <img src="{{ asset('images/component/logo1-2.png') }}" alt="Logo Kesbangpol" class="logo-img me-2">
                <h2 class="logo-text m-0">BAKESBANGPOL</h2>
            </div>
            <h3 class="text-center mb-0">Sign in to start your session</h3>
            <form action="{{ $loginUrl }}" method="POST" class="login-form">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="Masukkan email" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Masukkan password" required autocomplete="off">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- CAPTCHA --}}
                <div class="form-group mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="captcha" class="mb-0">Kode Verifikasi (Captcha)</label>
                        <span class="captcha-badge-hint" title="Klik gambar atau tombol untuk ganti kode">Klik untuk ganti</span>
                    </div>
                    <div class="captcha-group-wrapper">
                        <div class="captcha-img-box" id="captcha-img-box" title="Klik untuk muat ulang captcha" onclick="reloadCaptcha()">
                            <img src="{{ route('captcha') }}" alt="Captcha" class="captcha-image" id="captcha-image">
                        </div>
                        <button type="button" class="btn-captcha-refresh" id="btn-reload-captcha" title="Muat ulang kode captcha" onclick="reloadCaptcha()">
                            <svg class="refresh-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                        </button>
                        <input type="text" id="captcha" name="captcha"
                            class="form-control captcha-input-field @error('captcha') is-invalid @enderror"
                            placeholder="5 kode" maxlength="5" required autocomplete="off">
                    </div>
                    @error('captcha')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember Me
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-login w-100">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 17l5-5-5-5v10zM4 4h2v16H4V4z"/>
                        </svg>
                    </span>
                </button>

                {{-- Forgot Password --}}
                @if($passResetUrl)
                    <div class="text-center mt-3">
                        <a href="{{ $passResetUrl }}" class="link-secondary">Lupa password?</a>
                    </div>
                @endif

            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
    function reloadCaptcha() {
        const btn = document.getElementById('btn-reload-captcha');
        const img = document.getElementById('captcha-image');
        const input = document.getElementById('captcha');

        if (btn) btn.classList.add('is-spinning');

        fetch('{{ route("captcha.reload") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Gagal memuat captcha');
            return response.json();
        })
        .then(data => {
            if (data.data_url) {
                img.src = data.data_url;
            } else if (data.url) {
                img.src = data.url;
            }
            if (input) {
                input.value = '';
                input.focus();
            }
        })
        .catch(err => {
            img.src = '{{ route("captcha") }}?t=' + Date.now();
            if (input) {
                input.value = '';
                input.focus();
            }
        })
        .finally(() => {
            setTimeout(() => {
                if (btn) btn.classList.remove('is-spinning');
            }, 600);
        });
    }

    // Auto-uppercase saat pengguna mengetik captcha
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('captcha');
        if (input) {
            input.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        }
    });
</script>
@endpush
