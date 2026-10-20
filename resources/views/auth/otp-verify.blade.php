@extends('auth.auth-page')

@section('title', 'Verifikasi Kode OTP')

@section('auth_body')
    <link rel="stylesheet" href="{{ asset('assets/css/login-form.css') }}">
    <style>
        .otp-input {
            letter-spacing: 0.45em;
            text-align: center;
            font-size: 1.85rem;
            font-weight: 800;
            padding: 12px 10px;
            border-radius: 14px;
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            color: #0f172a;
            transition: all 0.25s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .otp-input:focus {
            border-color: #b91c1c;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.12), inset 0 1px 2px rgba(0,0,0,0.02);
            outline: none;
        }
        .notice-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.8rem;
            color: #92400e;
            line-height: 1.5;
            text-align: left;
            margin-bottom: 20px;
        }
        .btn-resend {
            background: none;
            border: none;
            color: #b91c1c;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-resend:hover:not(:disabled) {
            background-color: #fee2e2;
            text-decoration: none;
        }
        .btn-resend:disabled {
            color: #94a3b8;
            cursor: not-allowed;
            background: none;
        }
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #f1f5f9;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }
    </style>

    <div class="login-wrapper d-flex align-items-center justify-content-center">
        <div class="login-card" style="max-width: 440px;">
            {{-- Logo Kesbangpol --}}
            <div class="login-logo mb-2">
                <img src="{{ asset('images/component/logo1-2.png') }}" alt="Logo Kesbangpol" class="logo-img me-2">
                <h2 class="logo-text m-0">BAKESBANGPOL</h2>
            </div>

            <div class="text-center">
                <div class="security-badge">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                    </svg>
                    Two-Factor Authentication (2FA)
                </div>
            </div>

            <h3 class="text-center mb-1 font-weight-bold" style="font-size: 1.35rem; color: #1e293b;">
                Verifikasi Kode OTP
            </h3>
            <p class="text-center text-muted mb-3" style="font-size: 0.85rem;">
                Kode 6 digit keamanan telah dikirim ke email:<br>
                <strong class="text-dark">{{ $maskedEmail ?? $email }}</strong>
            </p>

            {{-- Alert Informasi Folder Spam --}}
            <div class="notice-box">
                <strong>💡 Email belum muncul?</strong><br>
                Jika tidak ada di <strong>Kotak Masuk Utama</strong>, periksa folder <strong style="text-decoration: underline;">Spam</strong> atau tab <strong>Promosi</strong> di akun Gmail Anda.
            </div>

            {{-- Alert Berhasil Kirim Ulang --}}
            @if(session('resend_success'))
                <div class="alert alert-success py-2 px-3 mb-3 text-center" style="font-size: 0.82rem; border-radius: 10px;">
                    ✓ {{ session('resend_success') }}
                </div>
            @endif

            <form action="{{ route('otp.process') }}" method="POST" class="login-form" id="otp-form">
                @csrf
                {{-- Honeypot protection --}}
                <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

                <div class="form-group mb-4">
                    <input type="text" 
                           id="otp" 
                           name="otp" 
                           class="form-control otp-input @error('otp') is-invalid @enderror"
                           maxlength="6" 
                           inputmode="numeric" 
                           pattern="[0-9]*" 
                           placeholder="• • • • • •" 
                           value="{{ old('otp') }}" 
                           required 
                           autofocus 
                           autocomplete="one-time-code">
                    @error('otp')
                        <div class="invalid-feedback text-center mt-2 font-weight-bold" style="font-size: 0.82rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="btn-submit-otp" class="btn-login w-100 mb-3" style="height: 48px;">
                    <span class="btn-text">Verifikasi & Masuk</span>
                    <span class="btn-icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                    </span>
                </button>
            </form>

            {{-- Form Kirim Ulang --}}
            <div class="text-center pt-2 border-top">
                <p class="text-muted mb-1" style="font-size: 0.8rem;">Tidak menerima kode OTP?</p>
                <form action="{{ route('otp.resend') }}" method="POST" id="resend-form">
                    @csrf
                    {{-- Honeypot protection --}}
                    <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">

                    <button type="submit" id="btn-resend-otp" class="btn-resend" {{ ($remainingCooldown ?? 60) > 0 ? 'disabled' : '' }}>
                        @if(($remainingCooldown ?? 60) > 0)
                            Kirim ulang kode dalam <span id="countdown">{{ $remainingCooldown ?? 60 }}</span> detik
                        @else
                            🔄 Kirim Ulang Kode OTP Sekarang
                        @endif
                    </button>
                </form>
                <div class="mt-3">
                    <a href="{{ route('login') }}" class="text-secondary" style="font-size: 0.8rem; text-decoration: none;">
                        ← Batal & Kembali ke Halaman Login
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('otp');
            const form = document.getElementById('otp-form');
            const submitBtn = document.getElementById('btn-submit-otp');

            if (input) {
                // Hanya izinkan angka numerik
                input.addEventListener('input', function (e) {
                    this.value = this.value.replace(/\D/g, '');

                    // Auto-submit saat 6 digit telah terisi
                    if (this.value.length === 6) {
                        if (submitBtn) {
                            submitBtn.querySelector('.btn-text').textContent = 'Memverifikasi...';
                        }
                        form.submit();
                    }
                });

                // Loading effect saat submit manual
                form.addEventListener('submit', function () {
                    if (submitBtn) {
                        submitBtn.querySelector('.btn-text').textContent = 'Memverifikasi...';
                    }
                });
            }

            // Hitung mundur tombol kirim ulang
            let timeLeft = parseInt('{{ $remainingCooldown ?? 60 }}', 10);
            const btn = document.getElementById('btn-resend-otp');
            const countdownEl = document.getElementById('countdown');

            if (timeLeft > 0 && btn) {
                const timer = setInterval(function () {
                    timeLeft--;
                    if (timeLeft <= 0) {
                        clearInterval(timer);
                        btn.disabled = false;
                        btn.innerHTML = '🔄 Kirim Ulang Kode OTP Sekarang';
                    } else {
                        if (countdownEl) {
                            countdownEl.textContent = timeLeft;
                        }
                    }
                }, 1000);
            }
        });
    </script>
@endsection
