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

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-gray-700" for="contact_maps_embed">
                                URL Embed Peta (Iframe Source)
                            </label>
                            <input type="text" class="form-control form-control-lg fs-6 @error('contact_maps_embed') is-invalid @enderror"
                                id="contact_maps_embed" name="contact_maps_embed"
                                value="{{ old('contact_maps_embed', $settings['contact_maps_embed'] ?? '') }}"
                                placeholder="https://www.google.com/maps/embed?pb=...">
                            <div class="form-text small">
                                <strong>Tips:</strong> Buka Google Maps &rarr; Klik Bagikan &rarr; Pilih "Sematkan peta" &rarr; Salin URL atau paste seluruh kode HTML <code>&lt;iframe&gt;</code> ke sini.
                            </div>
                        </div>

                        {{-- Preview Maps --}}
                        @if(!empty($settings['contact_maps_embed']))
                            <div class="mt-3">
                                <label class="form-label fw-semibold text-muted small">Pratinjau Peta Saat Ini:</label>
                                <div class="ratio ratio-21x9 rounded-3 overflow-hidden border shadow-sm" style="max-height: 220px;">
                                    <iframe src="{{ $settings['contact_maps_embed'] }}" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                </div>
                            </div>
                        @endif

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
