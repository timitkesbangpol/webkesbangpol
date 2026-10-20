@extends('auth.auth-page')

@section('title', 'Login')

@section('auth_body')
    <link rel="stylesheet" href="{{ asset('assets/css/login-form.css') }}?v={{ filemtime(public_path('assets/css/login-form.css')) }}">
    <style>
        .captcha-group-wrapper {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            margin-top: 6px !important;
            width: 100% !important;
        }
        .captcha-img-box {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 1.5px solid #d8d8d8 !important;
            border-radius: 10px !important;
            background: #f8fafc !important;
            overflow: hidden !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
            height: 46px !important;
            transition: all 0.2s ease !important;
        }
        .captcha-img-box:hover {
            border-color: #8C0A0F !important;
            box-shadow: 0 2px 8px rgba(140, 10, 15, 0.12) !important;
        }
        .captcha-image {
            display: block !important;
            width: 145px !important;
            height: 42px !important;
            object-fit: contain !important;
        }
        .btn-captcha-refresh {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 46px !important;
            height: 46px !important;
            background: #ffffff !important;
            border: 1.5px solid #d8d8d8 !important;
            border-radius: 10px !important;
            color: #8C0A0F !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
            padding: 0 !important;
            transition: all 0.2s ease !important;
        }
        .btn-captcha-refresh:hover {
            background: #fff5f5 !important;
            border-color: #8C0A0F !important;
            color: #a90e18 !important;
        }
        .btn-captcha-refresh:active {
            transform: scale(0.95) !important;
        }
        .btn-captcha-refresh .refresh-icon {
            display: inline-block !important;
            width: 18px !important;
            height: 18px !important;
            transition: transform 0.4s ease-in-out !important;
        }
        @keyframes spinCaptcha {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn-captcha-refresh.is-spinning .refresh-icon {
            animation: spinCaptcha 0.5s linear infinite !important;
        }
        .captcha-input-field {
            flex: 1 !important;
            min-width: 0 !important;
            height: 46px !important;
            text-transform: uppercase !important;
            font-weight: 700 !important;
            letter-spacing: 2px !important;
            font-size: 0.95rem !important;
            padding: 8px 10px !important;
            text-align: center !important;
        }
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            15%, 55% { transform: translateX(-6px); }
            35%, 75% { transform: translateX(6px); }
        }
    </style>

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

            {{-- Alert Box Peringatan Kesalahan Login --}}
            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-center mt-3 mb-2 p-3 rounded-3 shadow-sm" role="alert" style="background-color: #fee2e2; border: 1.5px solid #ef4444 !important; border-left: 5px solid #dc2626 !important; color: #991b1b; animation: shakeError 0.4s ease-in-out;">
                    <i class="fas fa-triangle-exclamation fs-4 me-3 flex-shrink-0" style="color: #dc2626;"></i>
                    <div class="small fw-semibold">
                        @if ($errors->has('password'))
                            {{ $errors->first('password') }}
                        @elseif ($errors->has('email'))
                            {{ $errors->first('email') }}
                        @elseif ($errors->has('captcha'))
                            {{ $errors->first('captcha') }}
                        @else
                            {{ $errors->first() }}
                        @endif
                    </div>
                </div>
            @endif

            <form action="{{ $loginUrl }}" method="POST" class="login-form">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="Masukkan email" required autofocus>
                    @error('email')
                        <div class="invalid-feedback d-block fw-semibold mt-1" style="color: #dc2626 !important; font-size: 0.85rem;">
                            <i class="fas fa-circle-exclamation me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Masukkan password" required autocomplete="off">
                    @error('password')
                        <div class="invalid-feedback d-block fw-semibold mt-1" style="color: #dc2626 !important; font-size: 0.85rem;">
                            <i class="fas fa-circle-exclamation me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- CAPTCHA --}}
                <div class="form-group mt-3">
                    <label for="captcha">Captcha</label>
                    <div class="captcha-group-wrapper d-flex align-items-center gap-2" style="display: flex !important; align-items: center !important; gap: 8px !important; width: 100% !important;">
                        <div class="captcha-img-box" id="captcha-img-box" title="Klik untuk muat ulang captcha" onclick="reloadCaptcha()" style="height: 46px !important; border: 1.5px solid #d8d8d8 !important; border-radius: 10px !important; background: #f8fafc !important; overflow: hidden !important; cursor: pointer !important; flex-shrink: 0 !important; display: inline-flex !important; align-items: center !important;">
                            <img src="{{ route('captcha') }}" alt="Captcha" class="captcha-image" id="captcha-image" style="display: block !important; width: 145px !important; height: 42px !important; object-fit: contain !important;">
                        </div>
                        <button type="button" class="btn-captcha-refresh" id="btn-reload-captcha" title="Muat ulang kode captcha" onclick="reloadCaptcha()" style="width: 46px !important; height: 46px !important; background: #ffffff !important; border: 1.5px solid #d8d8d8 !important; border-radius: 10px !important; color: #8C0A0F !important; cursor: pointer !important; flex-shrink: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; padding: 0 !important;">
                            <svg class="refresh-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                        </button>
                        <input type="text" id="captcha" name="captcha"
                            class="form-control captcha-input-field @error('captcha') is-invalid @enderror"
                            placeholder="5 kode" maxlength="5" required autocomplete="off"
                            style="height: 46px !important; text-transform: uppercase !important; font-weight: 700 !important; letter-spacing: 2px !important; text-align: center !important; font-size: 0.95rem !important; flex: 1 !important; min-width: 0 !important;">
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
