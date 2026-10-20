<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function captcha()
    {
        $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5));
        session(['captcha_code' => $code]);

        $width = 220;
        $height = 70;
        $image = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($image, 250, 250, 250);
        $textColor = imagecolorallocate($image, 180, 13, 20);
        $lineColor = imagecolorallocate($image, 210, 210, 210);
        $dotColor = imagecolorallocate($image, 140, 140, 140);

        imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $backgroundColor);

        for ($i = 0; $i < 12; $i++) {
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
        }

        for ($i = 0; $i < 220; $i++) {
            imagesetpixel($image, mt_rand(0, $width - 1), mt_rand(0, $height - 1), $dotColor);
        }

        $x = 18;
        $fontPath = public_path('fonts/arial.ttf');
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            $y = mt_rand(20, 48);
            $color = imagecolorallocate($image, mt_rand(100, 180), mt_rand(20, 90), mt_rand(20, 90));

            if (function_exists('imagettftext') && is_file($fontPath)) {
                $angle = mt_rand(-20, 20);
                imagettftext($image, 24, $angle, $x, $y, $color, $fontPath, $char);
            } else {
                imagestring($image, 5, $x, $y, $char, $color);
            }

            $x += 34 + mt_rand(0, 6);
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData, 200, ['Content-Type' => 'image/png']);
    }

    /**
     * Validasi login: HANYA EMAIL TERDAFTAR di tabel users yang diizinkan!
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
            'captcha' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $expectedCaptcha = strtoupper((string) $request->session()->get('captcha_code', ''));
                    if (strtoupper((string) $value) !== $expectedCaptcha) {
                        $fail('Captcha salah.');
                    }
                },
            ],
        ], [
            'email.exists' => 'Alamat email tidak terdaftar dalam sistem. Hanya akun resmi Bakesbangpol yang dapat masuk.',
        ]);
    }

    /**
     * Intersepsi setelah password & captcha benar: JANGAN LANGSUNG LOGIN, TAPI KIRIM OTP!
     */
    protected function authenticated(Request $request, $user)
    {
        // Logout dulu dari login langsung bawaan Laravel
        Auth::logout();

        // Simpan state login sementara di session
        $request->session()->put('otp_auth', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'remember' => $request->filled('remember'),
            'sent_at' => now()->timestamp,
        ]);

        // Generate kode OTP 6 digit
        $otpCode = (string) random_int(100000, 999999);
        $expiredAt = now()->addMinutes(5);

        // Nonaktifkan OTP lama
        DB::table('otp_verifikasi')
            ->where('email', $user->email)
            ->where('is_used', 0)
            ->update(['is_used' => 1]);

        // Simpan OTP ke database
        DB::table('otp_verifikasi')->insert([
            'user_id' => $user->id,
            'email' => $user->email,
            'otp_code' => $otpCode,
            'expired_at' => $expiredAt,
            'is_used' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kirim email via Gmail SMTP
        try {
            Mail::to($user->email)->send(new SendOtpMail($otpCode, $user->name));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email OTP Laravel: ' . $e->getMessage());
        }

        // Arahkan pengguna ke halaman verifikasi OTP
        return redirect()->route('otp.verify');
    }
}
