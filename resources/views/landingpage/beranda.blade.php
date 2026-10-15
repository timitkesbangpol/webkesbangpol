@extends('landingpage.layouts.app')
@section('title', 'Halaman Beranda')

@push('styles')
    @vite(['resources/css/home.css'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.1.14/swiper-bundle.min.css">

    <style>
        /* ===== Sorotan Kegiatan (carousel coverflow) ===== */
        .highlight-swiper {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 0 50px;
        }

        .highlight-swiper .swiper-slide { width: 560px; }

        .highlight-swiper .highlight-card { aspect-ratio: auto; }

        /* !important menimpa .swiper-slide img { height: 80vh !important } di home.css */
        .highlight-swiper .highlight-card img {
            width: 100%;
            height: 320px !important;
            object-fit: cover !important;
        }

        /* hilangkan bayangan bawaan efek coverflow */
        .highlight-swiper .swiper-slide-shadow-left,
        .highlight-swiper .swiper-slide-shadow-right {
            background: none !important;
        }

        /* bullet aktif: menimpa aturan putih di home.css */
        .highlight-swiper .swiper-pagination-bullet-active {
            background: #B40D1A !important;
            opacity: 1 !important;
        }

        /* panah: menimpa display:none di home.css */
        .highlight-swiper .swiper-button-prev,
        .highlight-swiper .swiper-button-next {
            display: flex;
            color: #B40D1A;
        }

        .highlight-swiper .swiper-button-prev::after,
        .highlight-swiper .swiper-button-next::after {
            display: block !important;
        }

        .highlight-swiper .swiper-button-prev:hover,
        .highlight-swiper .swiper-button-next:hover {
            color: #8f0a13 !important;
            transform: none !important;
        }

        @media (max-width: 576px) {
            .highlight-swiper .swiper-slide { width: 300px; }
            .highlight-swiper .highlight-card img { height: 220px !important; }
        }
    </style>

    <style>
        /* ===== Galeri Kegiatan (marquee) ===== */
        .galeri-marquee {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gm-viewport {
            overflow: hidden;
            padding: 12px 0;
            -webkit-mask-image: linear-gradient(to right, transparent, #000 7%, #000 93%, transparent);
                    mask-image: linear-gradient(to right, transparent, #000 7%, #000 93%, transparent);
        }

        .gm-track {
            display: flex;
            width: max-content;
            will-change: transform;
        }

        /* margin-right (bukan gap) supaya lebar satu set foto tepat */
        .gm-track .galeri-item {
            flex: 0 0 auto;
            width: 260px;
            margin-right: 20px;
            display: block;
        }

        .gm-btn {
            position: absolute;
            top: 45%;
            z-index: 3;
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 50%;
            background: rgba(0, 0, 0, .45);
            color: #fff;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            transition: background .2s;
        }
        .gm-btn:hover { background: rgba(0, 0, 0, .7); }
        .gm-prev { left: 6px; }
        .gm-next { right: 6px; }

        @media (max-width: 576px) {
            .gm-track .galeri-item { width: 200px; margin-right: 14px; }
        }

        /* Lightbox tampil di atas navbar */
        .fancybox__container { z-index: 99999 !important; }
    </style>
@endpush

@section('content')

    {{-- ========================== Hero Section ========================== --}}
    <section class="hero">
        {{-- Ilustrasi Eksklusif Kesbangpol Kota Bandung --}}
        <div class="hero-skyline" style="background-image: url('{{ asset('images/component/BGKesbangpol.png') }}')"></div>

        <div class="hero-container">
            <div class="hero-content">
                <h2 class="display-5 fw-light fade-in" id="welcome-text">PUSAT INFORMASI DAN PELAYANAN</h2>
                <h1 class="fw-bold fade-in" id="title">BADAN KESATUAN BANGSA DAN POLITIK</h1>
                <h1 class="fw-bold fade-in" id="title">KOTA BANDUNG</h1>
                <p class="lead fade-in" id="description">Menyajikan Informasi Terbaru dan Artikel Inspiratif untuk Mendukung Pemahaman Kebangsaan Anda</p>

                <div class="divider fade-in"></div>
            </div>
        </div>
    </section>


    {{-- ========================== Sorotan Kegiatan (Highlight Besar) ========================== --}}
    @php
        // Swiper mode loop butuh slide yang cukup; gandakan jika kurang dari 6
        $slides = collect($banners);
        while ($slides->count() > 0 && $slides->count() < 6) {
            $slides = $slides->concat($banners);
        }
    @endphp

    <section class="galeri-section" id="highlight-section" data-aos="fade-up" data-aos-duration="1000">
        <div class="galeri-header">
            <span class="section-tag">Sorotan</span>
            <h2 class="judul-galeri">Kegiatan Terbaru</h2>
            <p class="desc-galeri">Momen dan kegiatan terkini Bakesbangpol Kota Bandung</p>
        </div>

        <div class="swiper highlight-swiper">
            <div class="swiper-wrapper">
                @foreach ($slides as $banner)
                    <div class="swiper-slide">
                        <div class="highlight-card"
                             data-bs-toggle="modal"
                             data-bs-target="#highlightModal"
                             data-img="{{ asset('images/banner/' . $banner->gambar_upload) }}"
                             data-title="{{ $banner->judul }}"
                             data-desc="{{ $banner->caption }}">
                            <img src="{{ asset('images/banner/' . $banner->gambar_upload) }}" alt="{{ $banner->judul }}" loading="lazy">
                            <div class="highlight-overlay">
                                <h3>{{ $banner->judul }}</h3>
                                <span class="highlight-tap-hint"><i class="fas fa-circle-info"></i> Ketuk untuk lihat detail</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </section>

    {{-- Modal deskripsi singkat (1 modal dipakai ulang untuk semua highlight) --}}
    <div class="modal fade" id="highlightModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content highlight-modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                <img id="highlightModalImg" src="" alt="" class="highlight-modal-img">
                <div class="modal-body">
                    <h3 id="highlightModalTitle"></h3>
                    <p id="highlightModalDesc"></p>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================== Layanan Section ========================== --}}
    <section class="py-5" id="layanan-section">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up" data-aos-delay="400" data-aos-duration="800">
                <h2 class="fw-bold display-5 mb-3">Pelayanan Kami</h2>
                <p class="text-muted fs-5">Layanan unggulan yang dapat diakses oleh masyarakat</p>
            </div>

            <div class="row g-4">
                {{-- Box 1 --}}
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="500" data-aos-duration="600">
                    <div class="layanan-box">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle me-3" data-aos="zoom-in" data-aos-delay="500">
                                <i class="fas fa-landmark"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Profil Badan</h5>
                        </div>
                        <p class="text-muted">Menyajikan informasi mengenai struktur organisasi, tugas pokok, fungsi, dan visi-misi Badan.</p>
                        <div class="text-end mt-3">
                            <a href="{{ route('tampilmenuprofile') }}" class="layanan-link">
                                Selengkapnya
                                <span class="icon-wrapper"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Box 2 --}}
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="600" data-aos-duration="600">
                    <div class="layanan-box">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle me-3" data-aos="zoom-in" data-aos-delay="600">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Simple Sakti</h5>
                        </div>
                        <p class="text-muted">Sistem pelaporan situasi keamanan dan ketertiban wilayah Kota Bandung secara digital.</p>
                        <div class="text-end mt-3">
                            <a href="https://layanan.bandung.go.id" target="_blank" rel="noopener" class="layanan-link">
                                Selengkapnya
                                <span class="icon-wrapper"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Box 3 --}}
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="700" data-aos-duration="600">
                    <div class="layanan-box">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle me-3" data-aos="zoom-in" data-aos-delay="700">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Dokumen dan Regulasi</h5>
                        </div>
                        <p class="text-muted">Mengakses dokumen perencanaan strategis serta laporan evaluasi dan pengukuran kinerja Badan.</p>
                        <div class="text-end mt-3">
                            <a href="{{ route('tampilmenusakip') }}" class="layanan-link">
                                Selengkapnya
                                <span class="icon-wrapper"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Box 4 --}}
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="800" data-aos-duration="600">
                    <div class="layanan-box">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle me-3" data-aos="zoom-in" data-aos-delay="800">
                                <i class="fas fa-database"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Data Organisasi Masyarakat</h5>
                        </div>
                        <p class="text-muted">Menampilkan informasi dan rekapitulasi data organisasi kemasyarakatan yang terdaftar.</p>
                        <div class="text-end mt-3">
                            <a href="{{ route('tampil-data-ormas') }}" class="layanan-link">
                                Selengkapnya
                                <span class="icon-wrapper"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ========================== Galeri Kegiatan Section ========================== --}}
    <section class="galeri-section">
        <div class="galeri-header" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">
            <span class="section-tag">Dokumentasi</span>
            <h2 class="judul-galeri">Galeri Kegiatan</h2>
            <p class="desc-galeri">Dokumentasi kegiatan dan penghargaan di Bakesbangpol Kota Bandung</p>
        </div>

        <div class="galeri-marquee" id="galeri-marquee" data-aos="fade-up" data-aos-delay="500" data-aos-duration="1000">
            <button class="gm-btn gm-prev" type="button" aria-label="Sebelumnya">&#8249;</button>

            <div class="gm-viewport">
                <div class="gm-track">
                    @foreach ($galeris as $item)
                        <a href="{{ asset('images/gallery/' . $item->gambar_upload) }}"
                           data-fancybox="gallery"
                           data-caption="{{ $item->judul }}"
                           class="galeri-item">
                            <div class="galeri-card">
                                <div class="image-container">
                                    <img src="{{ asset('images/gallery/' . $item->gambar_upload) }}"
                                         alt="{{ $item->judul }}"
                                         loading="lazy" decoding="async" draggable="false">
                                    <div class="overlay"><i class="fas fa-search-plus"></i></div>
                                </div>
                                <div class="image-caption">
                                    <h4>{{ $item->judul }}</h4>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <button class="gm-btn gm-next" type="button" aria-label="Berikutnya">&#8250;</button>
        </div>
    </section>


    {{-- ========================== Articles Section ========================== --}}
    <section class="artikel-highlight py-5">
        <div class="container">
            <div class="text-center mb-5" id="judul-section-article" data-aos="fade-up" data-aos-delay="0" data-aos-duration="800">
                <h2 class="fw-bold">Berita dan Artikel</h2>
                <p>Temukan informasi terkini seputar kebijakan, kegiatan, dan berita menarik lainnya dari Bakesbangpol Kota Bandung.</p>
            </div>

            <div class="row gx-5 artikel-custom-layout">
                {{-- Artikel Utama --}}
                <div class="col-lg-7 col-md-12" id="artikel-utama-section" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                    <div class="artikel-highlight-box">
                        <div class="swiper artikelSwiper">
                            {{-- Diisi lewat JS --}}
                            <div class="swiper-wrapper" id="artikel-swiper-wrapper"></div>
                        </div>
                    </div>
                </div>

                {{-- List Artikel Terbaru --}}
                <div class="col-lg-5 col-md-12" id="list-artikel-section" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">
                    <div class="list-artikel-wrapper">
                        <h5 class="fw-bold mb-3">Artikel Terbaru</h5>
                        <hr>
                        {{-- Diisi lewat JS --}}
                        <div class="artikel-list-container" id="list-artikel-container"></div>
                        <div class="text-center">
                            <a href="/articles" class="lihat-semua-artikel-btn">
                                Lihat Semua Artikel <i class="fa-solid fa-circle-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/swiper@11.1.14/swiper-bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new Swiper('.highlight-swiper', {
                    effect: 'coverflow',
                    grabCursor: true,
                    centeredSlides: true,
                    slidesPerView: 'auto',
                    spaceBetween: 50,
                    loop: true,
                    autoplay: { delay: 2000, disableOnInteraction: false },
                    coverflowEffect: { rotate: 0, stretch: 0, depth: 100, modifier: 2.5 },
                    pagination: { el: '.highlight-swiper .swiper-pagination', clickable: true },
                    navigation: {
                        nextEl: '.highlight-swiper .swiper-button-next',
                        prevEl: '.highlight-swiper .swiper-button-prev',
                    },
                });
            });
        </script>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initArtikel();
            initHighlightModal();
            initGaleriMarquee();
        });

        /* ---------- Artikel (swiper utama + list terbaru) ---------- */
        function initArtikel() {
            const allPosts = @json($posts);

            // Urutkan dari yang terbaru
            const sortedPosts = [...allPosts].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            const formatDate = (dateStr) =>
                new Date(dateStr).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }); // 17 April 2025

            // Swiper: 3 artikel terbaru
            const swiperWrapper = document.getElementById('artikel-swiper-wrapper');
            swiperWrapper.innerHTML = sortedPosts.slice(0, 3).map(post => `
                <div class="swiper-slide">
                    <div class="card artikel-card">
                        <img src="/images/posts/${post.image}" alt="Gambar Artikel" />
                        <div class="card-overlay">
                            <span class="badge mb-2">BIDANG ${post.bidang?.no_bidang ?? ''}</span>
                            <h5 class="card-title">${post.title}</h5>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="card-date">${formatDate(post.created_at)}</small>
                                <a href="/artikel/${post.slug}" class="artikel-action-btn">
                                    Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            // List: semua artikel
            const listContainer = document.getElementById('list-artikel-container');
            listContainer.innerHTML = sortedPosts.map(post => `
                <a href="/artikel/${post.slug}" class="artikel-item-link">
                    <div class="artikel-item">
                        <span class="artikel-item-title">${post.title}</span>
                        <span class="artikel-item-date">${formatDate(post.created_at)}</span>
                    </div>
                </a>
            `).join('');

            if (window.artikelSwiper) window.artikelSwiper.update();
        }

        /* ---------- Modal Sorotan Kegiatan ---------- */
        function initHighlightModal() {
            const modal = document.getElementById('highlightModal');
            if (!modal) return;

            modal.addEventListener('show.bs.modal', (event) => {
                const card = event.relatedTarget;
                document.getElementById('highlightModalImg').src = card.dataset.img;
                document.getElementById('highlightModalTitle').textContent = card.dataset.title;
                document.getElementById('highlightModalDesc').textContent = card.dataset.desc || 'Tidak ada deskripsi tambahan.';
            });
        }

        /* ---------- Galeri Kegiatan: marquee berjalan, foto tetap bisa diklik ---------- */
        function initGaleriMarquee() {
            const root = document.getElementById('galeri-marquee');
            if (!root) return;

            const viewport  = root.querySelector('.gm-viewport');
            const track     = root.querySelector('.gm-track');
            const originals = [...track.children];
            if (!originals.length) return;

            const GAP   = 20;  // harus sama dengan margin-right .galeri-item di CSS
            const SPEED = 40;  // px per detik, ubah sesuai selera

            // Lebar satu set foto asli, dihitung sebelum duplikasi
            const setWidth = track.scrollWidth;
            const copies   = Math.ceil(viewport.clientWidth / setWidth) + 1;

            // Duplikat agar loop mulus. Salinan punya grup lightbox sendiri
            // supaya penomoran tetap "1 / N", bukan dua kali lipat.
            for (let i = 0; i < copies; i++) {
                originals.forEach((el, index) => {
                    const clone = el.cloneNode(true);
                    clone.setAttribute('aria-hidden', 'true');
                    clone.setAttribute('tabindex', '-1');
                    clone.removeAttribute('data-fancybox');   // salinan tidak ikut grup lightbox
                    clone.dataset.cloneOf = index;
                    track.appendChild(clone);
                });
            }

            // Klik pada salinan = klik pada foto aslinya, sehingga lightbox
            // (yang sudah terpasang di foto asli) terbuka dengan benar.
            track.addEventListener('click', (event) => {
                const clone = event.target.closest('[data-clone-of]');
                if (!clone) return;
                event.preventDefault();
                originals[clone.dataset.cloneOf].click();
            });

            const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
            let x = 0, nudge = 0, hovering = false, last = performance.now();

            root.addEventListener('pointerenter', () => hovering = true);
            root.addEventListener('pointerleave', () => hovering = false);

            const stepSize = () => originals[0].offsetWidth + GAP;
            root.querySelector('.gm-prev').addEventListener('click', () => nudge += stepSize());
            root.querySelector('.gm-next').addEventListener('click', () => nudge -= stepSize());

            (function tick(now) {
                const dt = Math.min((now - last) / 1000, 0.1);
                last = now;

                const lightboxOpen = document.documentElement.classList.contains('with-fancybox');
                if (!hovering && !reduceMotion && !lightboxOpen) x -= SPEED * dt;

                // Geser halus saat panah diklik
                const d = nudge * 0.12;
                x += d;
                nudge -= d;

                // Loop tanpa putus
                if (-x >= setWidth) x += setWidth;
                if (x > 0) x -= setWidth;

                track.style.transform = `translate3d(${x}px, 0, 0)`;
                requestAnimationFrame(tick);
            })(last);
        }
    </script>
@endsection