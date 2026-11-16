<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->is('verify-otp') || $request->is('resend-otp')) {
                return redirect()->route('otp.verify')->withErrors([
                    'otp' => 'Sesi verifikasi Anda telah disegarkan. Silakan masukkan kembali kode OTP.',
                ]);
            }

            if ($request->is('kelola-bakesbangpol') || $request->is('login')) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Sesi formulir telah kedaluwarsa. Silakan masukkan kembali email dan password.',
                ]);
            }

            return redirect()->back()->withInput($request->except('password', 'otp'))->withErrors([
                'error' => 'Sesi formulir telah kedaluwarsa demi keamanan. Silakan coba kembali.',
            ]);
        });
    })->create();
