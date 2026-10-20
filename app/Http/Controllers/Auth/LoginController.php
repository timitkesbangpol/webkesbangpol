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

    /**
     * Generate 5-character distinct code and clean SVG vector captcha
     */
    private function generateCaptchaData()
    {
        $chars = '2346789ACDEFGHJKMNPQRTUVWXYZ';
        $code = '';
        $charLen = strlen($chars);
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[random_int(0, $charLen - 1)];
        }

        session([
            'captcha_code' => $code,
            'captcha_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        $width = 160;
        $height = 46;

        $colors = ['#8C0A0F', '#B40D14', '#1e293b', '#334155', '#991b1b', '#0f172a'];

        // Random background wave lines
        $paths = '';
        for ($i = 0; $i < 3; $i++) {
            $startY = rand(8, 38);
            $cp1X = rand(25, 65);
            $cp1Y = rand(4, 42);
            $cp2X = rand(95, 135);
            $cp2Y = rand(4, 42);
            $endY = rand(8, 38);
            $stroke = (rand(0, 1) === 1) ? 'rgba(180, 13, 20, 0.22)' : 'rgba(100, 116, 139, 0.22)';
            $paths .= "<path d=\"M 0 $startY C $cp1X $cp1Y, $cp2X $cp2Y, $width $endY\" fill=\"none\" stroke=\"$stroke\" stroke-width=\"1.6\" />";
        }

        // Random dots
        $dots = '';
        for ($i = 0; $i < 30; $i++) {
            $cx = rand(2, $width - 2);
            $cy = rand(2, $height - 2);
            $r = rand(10, 16) / 10;
            $dots .= "<circle cx=\"$cx\" cy=\"$cy\" r=\"$r\" fill=\"rgba(148, 163, 184, 0.4)\" />";
        }

        // Characters
        $charsSvg = '';
        $spacing = ($width - 24) / 5;

        for ($i = 0; $i < 5; $i++) {
            $char = $code[$i];
            $x = 14 + ($i * $spacing) + rand(-2, 2);
            $y = rand(30, 35);
            $rot = rand(-12, 12);
            $color = $colors[array_rand($colors)];
            $fontSize = rand(23, 26);

            $charsSvg .= "<text x=\"$x\" y=\"$y\" fill=\"$color\" font-size=\"{$fontSize}px\" font-weight=\"800\" font-family=\"'Segoe UI', -apple-system, BlinkMacSystemFont, Arial, sans-serif\" transform=\"rotate($rot $x $y)\">$char</text>";
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">';
        $svg .= '<defs><linearGradient id="cbg" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#f8fafc"/><stop offset="50%" stop-color="#f1f5f9"/><stop offset="100%" stop-color="#e2e8f0"/></linearGradient></defs>';
        $svg .= '<rect width="' . $width . '" height="' . $height . '" rx="6" fill="url(#cbg)" stroke="#cbd5e1" stroke-width="1"/>';
        $svg .= $dots;
        $svg .= $paths;
        $svg .= $charsSvg;
        $svg .= '</svg>';

        return [
            'code' => $code,
            'svg' => $svg,
            'data_url' => 'data:image/svg+xml;base64,' . base64_encode($svg),
        ];
    }

    /**
     * Endpoint untuk menampilkan gambar Captcha
     */
    public function captcha(Request $request)
    {
        // Jika dibuka langsung via address bar browser biasa (bukan tag <img> atau AJAX),
        // arahkan pengguna langsung ke halaman login agar tidak tampil raw gambar layar hitam
        $acceptHeader = (string) $request->header('Accept', '');
        if ($request->acceptsHtml() && str_starts_with($acceptHeader, 'text/html')) {
            return redirect()->route('login');
        }

        $data = $this->generateCaptchaData();

        return response($data['svg'], 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
        ]);
    }

    /**
     * Endpoint AJAX untuk reload Captcha tanpa reload seluruh halaman
     */
    public function reloadCaptcha()
    {
        $data = $this->generateCaptchaData();

        return response()->json([
            'status' => 'success',
            'data_url' => $data['data_url'],
            'url' => route('captcha') . '?t=' . (int) (microtime(true) * 1000),
        ], 200, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Validasi login: Email terdaftar, password, dan Captcha
     */
    protected function validateLogin(Request $request)
    {
        // Bersihkan url.intended jika mengarah ke captcha atau login
        if ($request->session()->has('url.intended')) {
            $intended = (string) $request->session()->get('url.intended');
            $customPath = (string) env('ADMIN_LOGIN_PATH', 'kelola-bakesbangpol');
            if (str_contains($intended, '/captcha') || str_contains($intended, '/login') || str_contains($intended, $customPath)) {
                $request->session()->forget('url.intended');
            }
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
            'captcha' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $expectedCaptcha = strtoupper(trim((string) $request->session()->get('captcha_code', '')));
                    $inputCaptcha = strtoupper(trim((string) $value));
                    $expiresAt = $request->session()->get('captcha_expires_at');

                    if ($expiresAt && now()->timestamp > $expiresAt) {
                        $fail('Kode captcha telah kedaluwarsa. Silakan muat ulang captcha.');
                        return;
                    }

                    if (empty($expectedCaptcha) || $inputCaptcha !== $expectedCaptcha) {
                        $fail('Kode captcha tidak sesuai. Silakan coba lagi.');
                    }
                },
            ],
        ], [
            'email.required' => 'Alamat email atau Gmail wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Alamat email atau Gmail tidak terdaftar dalam sistem Bakesbangpol.',
            'password.required' => 'Password akun wajib diisi.',
            'captcha.required' => 'Kode captcha wajib diisi.',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            $request->session()->flash('login_error', $firstError);

            throw (new \Illuminate\Validation\ValidationException($validator))
                ->redirectTo(route('login'));
        }
    }

    /**
     * Kustomisasi peringatan jika password yang dimasukkan salah
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $message = 'Password yang Anda masukkan salah. Silakan periksa kembali kata sandi akun Anda.';
        $request->session()->flash('login_error', $message);

        throw \Illuminate\Validation\ValidationException::withMessages([
            'password' => [$message],
        ])->redirectTo(route('login'));
    }

    /**
     * Intersepsi setelah password & captcha benar: JANGAN LANGSUNG LOGIN, TAPI KIRIM OTP!
     */
    protected function authenticated(Request $request, $user)
    {
        // Bersihkan url.intended agar tidak pernah terjadi redirect liar ke /captcha atau URL lain
        $request->session()->forget('url.intended');

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

        // Simpan OTP ke database dengan audit trail
        DB::table('otp_verifikasi')->insert([
            'user_id' => $user->id,
            'email' => $user->email,
            'otp_code' => $otpCode,
            'expired_at' => $expiredAt,
            'is_used' => 0,
            'attempts' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kirim email OTP
        try {
            Mail::to($user->email)->send(new SendOtpMail($otpCode, $user->name, $request->ip()));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email OTP Laravel: ' . $e->getMessage());
        }

        // Arahkan pengguna ke halaman verifikasi OTP
        return redirect()->route('otp.verify');
    }
}
