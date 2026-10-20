<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan formulir pengaturan media sosial, kontak, dan maps.
     */
    public function socialMedia()
    {
        $keys = [
            'social_instagram',
            'social_tiktok',
            'social_youtube',
            'social_facebook',
            'social_x',
            'contact_email',
            'contact_phone',
            'contact_address',
            'contact_working_days',
            'contact_working_hours',
            'contact_maps_embed',
            'contact_maps_url',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = AppSetting::getValue($key, '');
        }

        return view('dashboard.settings.social-media', compact('settings'));
    }

    /**
     * Simpan pembaruan pengaturan media sosial, kontak, dan maps.
     */
    public function updateSocialMedia(Request $request)
    {
        $request->validate([
            'social_instagram' => 'nullable|string|max:500',
            'social_tiktok' => 'nullable|string|max:500',
            'social_youtube' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|string|max:500',
            'social_x' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:100',
            'contact_address' => 'nullable|string|max:1000',
            'contact_working_days' => 'nullable|string|max:255',
            'contact_working_hours' => 'nullable|string|max:255',
            'contact_maps_embed' => 'nullable|string',
            'contact_maps_url' => 'nullable|string|max:1000',
        ]);

        $fields = [
            'social_instagram',
            'social_tiktok',
            'social_youtube',
            'social_facebook',
            'social_x',
            'contact_email',
            'contact_phone',
            'contact_address',
            'contact_working_days',
            'contact_working_hours',
            'contact_maps_embed',
            'contact_maps_url',
        ];

        foreach ($fields as $field) {
            $value = $request->input($field, '');

            // Jika admin memasukkan tag <iframe> lengkap untuk Google Maps, ekstrak URL src-nya saja
            if ($field === 'contact_maps_embed' && !empty($value)) {
                if (preg_match('/src=["\']([^"\']+)["\']/', $value, $matches)) {
                    $value = $matches[1];
                }
            }

            AppSetting::setValue($field, trim((string) $value));
        }

        // Hapus cache pengaturan footer agar langsung ter-refresh di landing page
        Cache::forget('site_footer_settings');

        return redirect()
            ->route('settings.social-media')
            ->with('success', 'Pengaturan Media Sosial, Kontak, dan Google Maps berhasil disimpan!');
    }
}
