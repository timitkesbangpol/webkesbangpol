@extends('dashboard.layouts.app')

@section('title', 'Pengaturan Media Sosial & Kontak')

@section('content')
<div class="container-fluid px-0">

    {{-- 1. HERO HEADER --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between p-4 mb-4 bg-white rounded-3 shadow-sm border border-light">
        <div class="mb-3 mb-md-0">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-danger bg-opacity-10 text-danger fw-semibold small mb-2">
                <i class="fas fa-share-nodes"></i>
                <span>Pengaturan Sistem</span>
            </div>
            <h1 class="h3 fw-bold text-gray-800 mb-1">Media Sosial & Informasi Kontak</h1>
            <p class="text-muted small mb-0">
                Kelola tautan media sosial, alamat kantor, jam operasional, dan peta lokasi yang ditampilkan di footer portal publik.
            </p>
        </div>
        <div>
            <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3 rounded-pill shadow-sm">
                <i class="fas fa-external-link-alt me-1"></i> Lihat Portal Publik
            </a>
        </div>
    </div>

    {{-- Feedback Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-circle-check fs-4 me-3 text-success"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-circle-exclamation fs-4 me-2"></i>
                <strong>Terdapat beberapa kesalahan:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('settings.social-media.update') }}" method="POST">
        @csrf

        <div class="row g-4">

            {{-- Kolom Kiri: Media Sosial --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                                <i class="fas fa-globe"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-gray-800">Tautan Media Sosial</h5>
                                <small class="text-muted">Ikon di footer hanya akan muncul jika tautan diisi.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        {{-- YouTube --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700 d-flex align-items-center gap-2" for="social_youtube">
                                <i class="fab fa-youtube text-danger fs-5"></i>
                                <span>YouTube Channel / Video</span>
                            </label>
                            <input type="url" class="form-control form-control-lg fs-6 @error('social_youtube') is-invalid @enderror"
                                id="social_youtube" name="social_youtube"
                                value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}"
                                placeholder="https://www.youtube.com/@namachannel">
                            <div class="form-text small">Masukkan link lengkap saluran resmi YouTube Bakesbangpol (kosongkan jika belum ada).</div>
                        </div>

                        {{-- Instagram --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700 d-flex align-items-center gap-2" for="social_instagram">
                                <i class="fab fa-instagram text-primary fs-5" style="color: #E1306C !important;"></i>
                                <span>Instagram</span>
                            </label>
                            <input type="url" class="form-control form-control-lg fs-6 @error('social_instagram') is-invalid @enderror"
                                id="social_instagram" name="social_instagram"
                                value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                                placeholder="https://www.instagram.com/bakesbangpolkotabandung/">
                        </div>

                        {{-- TikTok --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700 d-flex align-items-center gap-2" for="social_tiktok">
                                <i class="fab fa-tiktok text-dark fs-5"></i>
                                <span>TikTok</span>
                            </label>
                            <input type="url" class="form-control form-control-lg fs-6 @error('social_tiktok') is-invalid @enderror"
                                id="social_tiktok" name="social_tiktok"
                                value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}"
                                placeholder="https://www.tiktok.com/@kesbangpolkotabandung">
                        </div>

                        {{-- Facebook --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700 d-flex align-items-center gap-2" for="social_facebook">
                                <i class="fab fa-facebook text-primary fs-5"></i>
                                <span>Facebook (Opsional)</span>
                            </label>
                            <input type="url" class="form-control form-control-lg fs-6 @error('social_facebook') is-invalid @enderror"
                                id="social_facebook" name="social_facebook"
                                value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                                placeholder="https://www.facebook.com/halamanresmi">
                        </div>

                        {{-- X / Twitter --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700 d-flex align-items-center gap-2" for="social_x">
                                <i class="fab fa-x-twitter text-dark fs-5"></i>
                                <span>X (Twitter) (Opsional)</span>
                            </label>
                            <input type="url" class="form-control form-control-lg fs-6 @error('social_x') is-invalid @enderror"
                                id="social_x" name="social_x"
                                value="{{ old('social_x', $settings['social_x'] ?? '') }}"
                                placeholder="https://x.com/kesbangpol">
                        </div>

                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Kontak & Jam Kerja --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex p-2 bg-success bg-opacity-10 text-success rounded-circle">
                                <i class="fas fa-address-book"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-gray-800">Kontak & Jam Kerja</h5>
                                <small class="text-muted">Informasi operasional dan kontak yang tampil di footer.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700 d-flex align-items-center gap-2" for="contact_email">
                                <i class="fas fa-envelope text-danger"></i>
                                <span>Email Kontak Resmi</span>
                            </label>
                            <input type="email" class="form-control form-control-lg fs-6 @error('contact_email') is-invalid @enderror"
                                id="contact_email" name="contact_email"
                                value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                placeholder="bkbp@bandung.go.id">
                        </div>

                        {{-- WhatsApp / Telepon --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700 d-flex align-items-center gap-2" for="contact_phone">
                                <i class="fab fa-whatsapp text-success"></i>
                                <span>Telepon / WhatsApp</span>
                            </label>
                            <input type="text" class="form-control form-control-lg fs-6 @error('contact_phone') is-invalid @enderror"
                                id="contact_phone" name="contact_phone"
                                value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                                placeholder="022-4203344 atau 0812xxxxxxx">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-gray-700" for="contact_working_days">
                                    <i class="fas fa-calendar-day text-primary me-1"></i> Hari Kerja
                                </label>
                                <input type="text" class="form-control form-control-lg fs-6"
                                    id="contact_working_days" name="contact_working_days"
                                    value="{{ old('contact_working_days', $settings['contact_working_days'] ?? '') }}"
                                    placeholder="Senin - Jumat">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-gray-700" for="contact_working_hours">
                                    <i class="fas fa-clock text-warning me-1"></i> Jam Operasional
                                </label>
                                <input type="text" class="form-control form-control-lg fs-6"
                                    id="contact_working_hours" name="contact_working_hours"
                                    value="{{ old('contact_working_hours', $settings['contact_working_hours'] ?? '') }}"
                                    placeholder="08.00 - 16.00 WIB">
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700 d-flex align-items-center gap-2" for="contact_address">
                                <i class="fas fa-location-dot text-danger"></i>
                                <span>Alamat Kantor Lengkap</span>
                            </label>
                            <textarea class="form-control @error('contact_address') is-invalid @enderror"
                                id="contact_address" name="contact_address" rows="3"
                                placeholder="Jl. Wastukencana No.2, Babakan Ciamis, Kec. Sumur Bandung...">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Baris Bawah: Google Maps --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex p-2 bg-info bg-opacity-10 text-info rounded-circle">
                                <i class="fas fa-map-location-dot"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-gray-800">Peta Lokasi Google Maps</h5>
                                <small class="text-muted">Masukkan URL embed Google Maps atau paste kode embed HTML iframe lengkap.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-stretch">
                            {{-- SISI KIRI: INFORMASI & FORM INPUT --}}
                            <div class="col-lg-6 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-bold text-gray-800 mb-0" for="contact_maps_embed">
                                            URL Embed Peta (Iframe Source)
                                        </label>
                                        <button type="button" id="btn-default-maps" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" style="font-size: 0.78rem;" title="Kembalikan ke lokasi resmi kantor Bakesbangpol Bandung">
                                            <i class="fas fa-rotate-left me-1"></i> Gunakan Lokasi Resmi
                                        </button>
                                    </div>

                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light text-muted">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <input type="text" class="form-control form-control-lg fs-6 @error('contact_maps_embed') is-invalid @enderror"
                                            id="contact_maps_embed" name="contact_maps_embed"
                                            value="{{ old('contact_maps_embed', $settings['contact_maps_embed'] ?? '') }}"
                                            placeholder="https://www.google.com/maps/embed?pb=...">
                                    </div>
                                    @error('contact_maps_embed')
                                        <div class="text-danger small mt-1 mb-2">{{ $message }}</div>
                                    @enderror

                                    <div class="alert alert-light border rounded-3 p-3 mt-3 text-secondary small">
                                        <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                            <i class="fas fa-circle-info text-primary"></i>
                                            <span>Panduan Cara Ambil Embed Google Maps:</span>
                                        </div>
                                        <ol class="ps-3 mb-0 text-muted" style="line-height: 1.6;">
                                            <li>Buka lokasi kantor di <a href="https://maps.google.com" target="_blank" rel="noopener noreferrer" class="fw-semibold text-primary text-decoration-none">Google Maps <i class="fas fa-arrow-up-right-from-square" style="font-size: 0.7rem;"></i></a></li>
                                            <li>Klik tombol <strong>Bagikan</strong> (Share) &rarr; pilih tab <strong>"Sematkan peta"</strong></li>
                                            <li>Klik <strong>"Salin HTML"</strong> lalu tempel (paste) ke kolom di atas. Sistem otomatis mengekstrak URL <code>src</code> yang bersih.</li>
                                        </ol>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2 small text-muted">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1" id="map-status-badge">
                                            <i class="fas fa-circle-check me-1"></i> Peta Terhubung
                                        </span>
                                        <span class="text-muted" style="font-size: 0.8rem;">Pratinjau otomatis ter-update saat mengetik</span>
                                    </div>
                                    <a id="btn-open-maps" href="{{ !empty($settings['contact_maps_embed']) ? $settings['contact_maps_embed'] : 'https://maps.google.com' }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-primary fw-semibold" style="font-size: 0.82rem;">
                                        Buka di Google Maps <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- SISI KANAN: MAP / PRATINJAU PETA --}}
                            <div class="col-lg-6">
                                <div class="card h-100 border border-light-subtle rounded-3 overflow-hidden shadow-sm">
                                    <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                        <span class="small fw-bold text-gray-700 d-flex align-items-center gap-2">
                                            <i class="fas fa-map text-danger"></i> Pratinjau Peta (Live Preview)
                                        </span>
                                        <span class="badge bg-secondary-subtle text-dark border font-monospace" style="font-size: 0.72rem;">Footer Portal Publik</span>
                                    </div>
                                    <div class="position-relative" style="min-height: 280px; height: 100%; background: #e2e8f0;">
                                        <iframe id="maps-iframe-preview"
                                            src="{{ !empty($settings['contact_maps_embed']) ? $settings['contact_maps_embed'] : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1980.422750703021!2d107.61025168324443!3d-6.909070212796336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e63a6d8df397%3A0xa78857f0be375298!2sKesbangpol%20Kota%20Bandung!5e0!3m2!1sen!2sid!4v1745050653864!5m2!1sen!2sid' }}"
                                            style="width: 100%; height: 100%; min-height: 280px; border: 0;"
                                            allowfullscreen=""
                                            loading="lazy"
                                            referrerpolicy="no-referrer-when-downgrade">
                                        </iframe>
                                        <div id="maps-placeholder" class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center text-muted" style="background: #f8fafc;">
                                            <i class="fas fa-map-location-dot fs-1 mb-2 text-secondary opacity-50"></i>
                                            <span class="small fw-semibold">Belum ada URL embed peta yang valid</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tombol Simpan --}}
        <div class="d-flex align-items-center justify-content-end gap-3 my-4">
            <button type="submit" class="btn btn-danger btn-lg px-5 py-2 rounded-pill shadow-sm fw-semibold">
                <i class="fas fa-save me-2"></i> Simpan Pengaturan
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultEmbedUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1980.422750703021!2d107.61025168324443!3d-6.909070212796336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e63a6d8df397%3A0xa78857f0be375298!2sKesbangpol%20Kota%20Bandung!5e0!3m2!1sen!2sid!4v1745050653864!5m2!1sen!2sid';
    const mapsInput = document.getElementById('contact_maps_embed');
    const iframePreview = document.getElementById('maps-iframe-preview');
    const placeholder = document.getElementById('maps-placeholder');
    const btnDefault = document.getElementById('btn-default-maps');
    const btnOpenMaps = document.getElementById('btn-open-maps');
    const statusBadge = document.getElementById('map-status-badge');

    function cleanAndSetMapUrl(val) {
        if (!val) {
            if (iframePreview) iframePreview.classList.add('d-none');
            if (placeholder) placeholder.classList.remove('d-none');
            if (statusBadge) {
                statusBadge.className = 'badge bg-secondary-subtle text-muted border border-secondary-subtle rounded-pill px-2 py-1';
                statusBadge.innerHTML = '<i class="fas fa-circle-xmark me-1"></i> Tidak Ada Peta';
            }
            return;
        }

        let cleanUrl = val.trim();
        // Otomatis ekstrak jika pengguna mem-paste tag <iframe> lengkap
        const match = cleanUrl.match(/src=["']([^"']+)["']/);
        if (match && match[1]) {
            cleanUrl = match[1];
            mapsInput.value = cleanUrl;
        }

        if (cleanUrl && cleanUrl.startsWith('http')) {
            if (iframePreview) {
                iframePreview.src = cleanUrl;
                iframePreview.classList.remove('d-none');
            }
            if (placeholder) placeholder.classList.add('d-none');
            if (btnOpenMaps) btnOpenMaps.href = cleanUrl;
            if (statusBadge) {
                statusBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1';
                statusBadge.innerHTML = '<i class="fas fa-circle-check me-1"></i> Peta Terhubung';
            }
        } else {
            if (iframePreview) iframePreview.classList.add('d-none');
            if (placeholder) placeholder.classList.remove('d-none');
            if (statusBadge) {
                statusBadge.className = 'badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1';
                statusBadge.innerHTML = '<i class="fas fa-triangle-exclamation me-1"></i> URL Tidak Valid';
            }
        }
    }

    if (mapsInput) {
        // Cek inisial saat halaman dimuat
        cleanAndSetMapUrl(mapsInput.value);

        mapsInput.addEventListener('input', function () {
            cleanAndSetMapUrl(this.value);
        });

        mapsInput.addEventListener('paste', function () {
            setTimeout(() => cleanAndSetMapUrl(mapsInput.value), 60);
        });
    }

    if (btnDefault) {
        btnDefault.addEventListener('click', function () {
            if (mapsInput) {
                mapsInput.value = defaultEmbedUrl;
                cleanAndSetMapUrl(defaultEmbedUrl);
                mapsInput.focus();
            }
        });
    }
});
</script>
@endpush
