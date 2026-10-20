<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use App\Models\User;

class OtpController extends Controller
{
    /**
     * Tampilkan halaman verifikasi OTP
     */
    public function showVerifyForm(Request $request)
    {
        if (!$request->session()->has('otp_auth')) {
            return redirect()->route('login');
        }

        $otpAuth = $request->session()->get('otp_auth');
        $email = $otpAuth['email'] ?? '';

        return view('auth.otp-verify', compact('email'));
    }

    /**
     * Verifikasi kode OTP 6 digit
     */
    public function verify(Request $request)
    {
        if (!$request->session()->has('otp_auth')) {
            return redirect()->route('login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }

        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Silakan masukkan 6 digit kode OTP.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 digit angka.',
        ]);

        $otpAuth = $request->session()->get('otp_auth');
        $email = $otpAuth['email'];
        $code = trim($request->input('otp'));

        // Cek kode di database
        $record = DB::table('otp_verifikasi')
            ->where('email', $email)
            ->where('otp_code', $code)
            ->where('is_used', 0)
            ->orderBy('id', 'desc')
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau tidak ditemukan.'])->withInput();
        }

        // Cek kadaluarsa
        if (now()->greaterThan($record->expired_at)) {
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan klik kirim ulang kode.'])->withInput();
        }

        // Tandai OTP terpakai
        DB::table('otp_verifikasi')->where('id', $record->id)->update(['is_used' => 1]);

        // Login user
        $user = User::find($otpAuth['user_id']);
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Akun pengguna tidak ditemukan.']);
        }

        Auth::login($user, $otpAuth['remember'] ?? false);
        $request->session()->forget('otp_auth');
        $request->session()->regenerate();

        return redirect()->intended('/home')->with('success', 'Verifikasi berhasil. Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Kirim ulang kode OTP
     */
    public function resend(Request $request)
    {
        if (!$request->session()->has('otp_auth')) {
            return redirect()->route('login');
        }

        $otpAuth = $request->session()->get('otp_auth');
        $email = $otpAuth['email'];
        $user = User::find($otpAuth['user_id']);

        if (!$user) {
            return redirect()->route('login');
        }

        // Generate kode baru
        $otpCode = (string) random_int(100000, 999999);
        $expiredAt = now()->addMinutes(5);

        // Nonaktifkan OTP lama
        DB::table('otp_verifikasi')
            ->where('email', $email)
            ->where('is_used', 0)
            ->update(['is_used' => 1]);

        // Simpan OTP baru
        DB::table('otp_verifikasi')->insert([
            'user_id' => $user->id,
            'email' => $email,
            'otp_code' => $otpCode,
            'expired_at' => $expiredAt,
            'is_used' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($email)->send(new SendOtpMail($otpCode, $user->name));
        } catch (\Exception $e) {
            \Log::error('Gagal kirim ulang OTP Laravel: ' . $e->getMessage());
        }

        return back()->with('resend_success', 'Kode OTP baru berhasil dikirimkan ke email Anda!');
    }
}
