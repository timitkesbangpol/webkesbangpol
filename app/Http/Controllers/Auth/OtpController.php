<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendOtpMail;
use App\Models\User;

class OtpController extends Controller
{
    /**
     * Mask email address for security and privacy (e.g. kesb***@gmail.com)
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 3) {
            $maskedName = substr($name, 0, 1) . '***';
        } else {
            $maskedName = substr($name, 0, 4) . str_repeat('*', max(3, strlen($name) - 4));
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Tampilkan halaman verifikasi OTP
     */
    public function showVerifyForm(Request $request)
    {
        if (!$request->session()->has('otp_auth')) {
            return redirect()->route('login');
        }

        $otpAuth = $request->session()->get('otp_auth');

        // Batasi sesi OTP maksimal 15 menit
        if (now()->timestamp - ($otpAuth['sent_at'] ?? 0) > 900) {
            $request->session()->forget('otp_auth');
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi verifikasi OTP telah kedaluwarsa (lebih dari 15 menit). Silakan login kembali.',
            ]);
        }

        $email = $otpAuth['email'] ?? '';
        $maskedEmail = $this->maskEmail($email);

        // Hitung sisa waktu cooldown kirim ulang
        $lastSentAt = $otpAuth['sent_at'] ?? 0;
        $elapsed = now()->timestamp - $lastSentAt;
        $remainingCooldown = max(0, 60 - $elapsed);

        return view('auth.otp-verify', compact('email', 'maskedEmail', 'remainingCooldown'));
    }

    /**
     * Verifikasi kode OTP 6 digit dengan proteksi Anti-Brute Force
     */
    public function verify(Request $request)
    {
        // Honeypot bot protection
        if ($request->filled('website_hp')) {
            return redirect()->route('login');
        }

        if (!$request->session()->has('otp_auth')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi login telah berakhir. Silakan login kembali.',
            ]);
        }

        $otpAuth = $request->session()->get('otp_auth');

        // Cek umur sesi OTP
        if (now()->timestamp - ($otpAuth['sent_at'] ?? 0) > 900) {
            $request->session()->forget('otp_auth');
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi verifikasi OTP telah kedaluwarsa. Silakan login kembali.',
            ]);
        }

        $request->validate([
            'otp' => 'required|string|size:6|regex:/^[0-9]+$/',
        ], [
            'otp.required' => 'Silakan masukkan 6 digit kode OTP.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 digit angka.',
            'otp.regex' => 'Kode OTP hanya boleh memuat angka.',
        ]);

        $email = $otpAuth['email'];
        $inputCode = trim($request->input('otp'));

        // Ambil record OTP aktif terbaru untuk email ini
        $record = DB::table('otp_verifikasi')
            ->where('email', $email)
            ->where('is_used', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (!$record) {
            return back()->withErrors([
                'otp' => 'Kode OTP tidak ditemukan atau telah digunakan. Silakan minta kode baru.',
            ])->withInput();
        }

        // Cek kadaluarsa waktu OTP (5 menit)
        if (now()->greaterThan($record->expired_at)) {
            DB::table('otp_verifikasi')->where('id', $record->id)->update(['is_used' => 1]);
            return back()->withErrors([
                'otp' => 'Kode OTP telah kedaluwarsa (berlaku 5 menit). Silakan klik kirim ulang kode.',
            ])->withInput();
        }

        // Proteksi Brute Force: Maksimal 5x percobaan salah
        $maxAttempts = 5;
        if (($record->attempts ?? 0) >= $maxAttempts) {
            DB::table('otp_verifikasi')->where('id', $record->id)->update(['is_used' => 1]);
            $request->session()->forget('otp_auth');
            Log::warning("Serangan brute force OTP terdeteksi pada akun {$email} dari IP {$request->ip()}");
            return redirect()->route('login')->withErrors([
                'email' => 'Batas percobaan OTP terlampaui (maksimal 5 kali salah). Sesi login dibatalkan demi keamanan. Silakan login kembali.',
            ]);
        }

        // Verifikasi timing-safe dengan hash_equals
        if (!hash_equals((string) $record->otp_code, (string) $inputCode)) {
            $newAttempts = ($record->attempts ?? 0) + 1;
            DB::table('otp_verifikasi')->where('id', $record->id)->update(['attempts' => $newAttempts]);

            $remaining = $maxAttempts - $newAttempts;
            if ($remaining <= 0) {
                DB::table('otp_verifikasi')->where('id', $record->id)->update(['is_used' => 1]);
                $request->session()->forget('otp_auth');
                Log::warning("Akun {$email} dibatalkan karena 5x salah input OTP dari IP {$request->ip()}");
                return redirect()->route('login')->withErrors([
                    'email' => 'Anda telah salah memasukkan kode OTP sebanyak 5 kali. Sesi dibatalkan demi keamanan. Silakan login kembali.',
                ]);
            }

            return back()->withErrors([
                'otp' => "Kode OTP salah. Sisa kesempatan: {$remaining} kali lagi.",
            ])->withInput();
        }

        // OTP Benar: Tandai OTP terpakai
        DB::table('otp_verifikasi')->where('id', $record->id)->update([
            'is_used' => 1,
            'updated_at' => now(),
        ]);

        $user = User::find($otpAuth['user_id']);
        if (!$user) {
            $request->session()->forget('otp_auth');
            return redirect()->route('login')->withErrors([
                'email' => 'Akun pengguna tidak ditemukan.',
            ]);
        }

        // Login user secara resmi & amankan sesi
        Auth::login($user, $otpAuth['remember'] ?? false);
        $request->session()->forget('otp_auth');
        $request->session()->regenerate();

        Log::info("Autentikasi 2FA Sukses: User {$user->email} berhasil masuk dari IP {$request->ip()}");

        return redirect()->intended('/home')->with('success', 'Verifikasi berhasil. Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Kirim ulang kode OTP dengan proteksi rate limit server-side
     */
    public function resend(Request $request)
    {
        // Honeypot bot protection
        if ($request->filled('website_hp')) {
            return redirect()->route('login');
        }

        if (!$request->session()->has('otp_auth')) {
            return redirect()->route('login');
        }

        $otpAuth = $request->session()->get('otp_auth');
        $email = $otpAuth['email'];
        $user = User::find($otpAuth['user_id']);

        if (!$user) {
            return redirect()->route('login');
        }

        // Proteksi Cooldown: Minimal jeda 60 detik antar permintaan kirim ulang
        $lastSentAt = $otpAuth['sent_at'] ?? 0;
        $elapsed = now()->timestamp - $lastSentAt;
        if ($elapsed < 60) {
            $remaining = 60 - $elapsed;
            return back()->withErrors([
                'otp' => "Harap tunggu {$remaining} detik lagi sebelum meminta kode OTP baru.",
            ]);
        }

        // Proteksi Flooding: Maksimal 5x kirim ulang per sesi login
        $resendCount = $otpAuth['resend_count'] ?? 0;
        if ($resendCount >= 5) {
            return back()->withErrors([
                'otp' => 'Batas maksimal pengiriman ulang OTP tercapai (5 kali). Silakan coba login kembali beberapa saat lagi.',
            ]);
        }

        // Generate kode baru 6 digit
        $otpCode = (string) random_int(100000, 999999);
        $expiredAt = now()->addMinutes(5);

        // Nonaktifkan semua OTP lama yang belum terpakai untuk email ini
        DB::table('otp_verifikasi')
            ->where('email', $email)
            ->where('is_used', 0)
            ->update(['is_used' => 1]);

        // Simpan OTP baru dengan audit log
        DB::table('otp_verifikasi')->insert([
            'user_id' => $user->id,
            'email' => $email,
            'otp_code' => $otpCode,
            'expired_at' => $expiredAt,
            'is_used' => 0,
            'attempts' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update waktu kirim dan counter resend di session
        $otpAuth['sent_at'] = now()->timestamp;
        $otpAuth['resend_count'] = $resendCount + 1;
        $request->session()->put('otp_auth', $otpAuth);

        // Kirim email OTP
        try {
            Mail::to($email)->send(new SendOtpMail($otpCode, $user->name, $request->ip()));
        } catch (\Exception $e) {
            Log::error('Gagal kirim ulang OTP Laravel: ' . $e->getMessage());
        }

        return back()->with('resend_success', 'Kode OTP baru berhasil dikirimkan ke email Anda!');
    }
}
