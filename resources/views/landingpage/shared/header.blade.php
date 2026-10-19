{{-- Style header navbar --}}
<style>
    .nav-wrapper {
        padding-top: 25px !important;
    }
    @media (max-width: 991.98px) {
        .nav-wrapper {
            padding-top: 18px !important;
        }
    }
    @media (max-width: 576px) {
        .nav-wrapper {
            padding-top: 12px !important;
        }
    }
</style>

<!-- Backdrop untuk Focus Mode & Mobile Menu -->
<div class="nav-backdrop" id="navBackdrop" onclick="closeMobileMenu()"></div>

<div class="nav-wrapper" id="mainNav">
    <nav class="navbar navbar-expand-lg navbar-pill">
        <a href="/" class="brand-logo d-flex align-items-center text-decoration-none">
            <img src="{{ asset('images/component/logo3.png') }}" alt="Logo">
            <img src="{{ asset('images/component/logo1-2.png') }}" alt="Logo" class="ms-2">
            <div class="ms-3 text-white fw-bold brand-text" style="line-height: 1.15; font-size: 0.85rem; letter-spacing: 0.2px;">
                BAKESBANGPOL<br>KOTA BANDUNG
            </div>
        </a>

        <!-- Burger Button untuk Mobile -->
        <button class="navbar-toggler border-0 text-white shadow-none" type="button" onclick="toggleMobileMenu()" aria-label="Toggle navigation">
            <i class="fas fa-bars" style="font-size: 1.3rem;"></i>
        </button>

        <div class="navbar-collapse" id="navbarContent">
            <!-- Header khusus Offcanvas Mobile -->
            <div class="mobile-menu-header">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/component/logo1-2.png') }}" alt="Logo" style="height: 35px;">
                    <span class="ms-2 text-white fw-bold" style="font-size: 0.9rem;">BAKESBANGPOL</span>
                </div>
                <button class="btn-close-menu" onclick="closeMobileMenu()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" onclick="toggleDropdown(event, this)">
                        Profil <i class="fas fa-chevron-down ms-1 chevron-icon"></i>
                    </a>
                    <div class="mega-menu"><div class="row">
                        <div class="col-md-4">
                            <div class="mega-menu-title">Tentang Kami</div>
                            <a href="{{ route('tampilvisimisi') }}">Visi Misi</a>
                            <a href="{{ route('tampiltugasfungsi') }}">Tugas dan Fungsi</a>
                            <a href="{{ route('tampilstruktur') }}">Struktur Organisasi</a>
                        </div>
                        <div class="col-md-4">
                            <div class="mega-menu-title">Lembaga</div>
                            <a href="{{ route('tampildasarhukum') }}">Landasan Hukum</a>
                            <a href="{{ route('tampilprogram') }}">Program & Kegiatan</a>
                            <a href="{{ route('tampilsejarah') }}">Sejarah</a>
                        </div>
                        <div class="col-md-4"><div class="mega-menu-empty">Informasi kelembagaan Bakesbangpol Kota Bandung yang mencakup visi misi hingga sejarah.</div></div>
                    </div></div>
                </li>

                <li class="nav-item"><a class="nav-link" href="{{ route('semua-artikel')}}">Artikel</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" onclick="toggleDropdown(event, this)">
                        SAKIP <i class="fas fa-chevron-down ms-1 chevron-icon"></i>
                    </a>
                    <div class="mega-menu"><div class="row">
                        <div class="col-md-3">
                            <div class="mega-menu-title">Perencanaan</div>
                            <a href="{{ route('tampiliku') }}">IKU</a>
                            <a href="{{ route('tampilrenja') }}">RENJA</a>
                            <a href="{{ route('tampilrenstra') }}">RENSTRA</a>
                        </div>
                        <div class="col-md-3">
                            <div class="mega-menu-title">Evaluasi</div>
                            <a href="{{ route('tampilukurkerja') }}">Pengukuran Kerja</a>
                            <a href="{{ route('tampillakip') }}">Laporan AKIP</a>
                        </div>
                        <div class="col-md-3">
                            <div class="mega-menu-title">Dokumen Kajian</div>
                            <a href="{{ route('tampillaporankajian') }}">Laporan Kajian</a>
                        </div>
                        <div class="col-md-3"><div class="mega-menu-empty">Dokumen akuntabilitas kinerja disusun sebagai transparansi publik.</div></div>
                    </div></div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" onclick="toggleDropdown(event, this)">
                        Mitra <i class="fas fa-chevron-down ms-1 chevron-icon"></i>
                    </a>
                    <div class="mega-menu"><div class="row">
                        <div class="col-md-4">
                            <div class="mega-menu-title">Pemerintahan</div>
                            <a href="{{ route('tampilmitra') }}">Semua Mitra</a>
                            <a href="{{ route('mitra.detail', ['kategori' => 'forkopimda']) }}">FORKOPIMDA</a>
                        </div>
                        <div class="col-md-4">
                            <div class="mega-menu-title">Lembaga</div>
                            <a href="{{ route('mitra.detail', ['kategori' => 'bnn']) }}">BNN</a>
                            <a href="{{ route('mitra.detail', ['kategori' => 'partai-politik']) }}">Partai Politik</a>
                            <a href="{{ route('mitra.detail', ['kategori' => 'fkdm']) }}">FKDM</a>
                        </div>
                        <div class="col-md-4"><div class="mega-menu-empty">Mitra strategis dalam menjaga kondusivitas kota.</div></div>
                    </div></div>
                </li>

                <li class="nav-item"><a class="nav-link" href="https://layanan.bandung.go.id" target="_blank">Pelayanan</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" onclick="toggleDropdown(event, this)">
                        Informasi <i class="fas fa-chevron-down ms-1 chevron-icon"></i>
                    </a>
                    <div class="mega-menu"><div class="row">
                        <div class="col-md-4">
                            <div class="mega-menu-title">Pemilu</div>
                            <a href="{{ route('pemilu.index') }}">Pusat Info Pemilu</a>
                        </div>
                        <div class="col-md-4">
                            <div class="mega-menu-title">Data Ormas</div>
                            <a href="{{ route('tampil-data-ormas') }}">Direktori Data Ormas</a>
                        </div>
                        <div class="col-md-4">
                            <div class="mega-menu-empty">Layanan informasi publik seputar kepemiluan dan organisasi kemasyarakatan Kota Bandung.</div>
                        </div>
                        {{-- Sembunyikan menu Potensi Konflik
                        <div class="col-md-4">
                            <div class="mega-menu-title">Statistik</div>
                            <a href="{{ route('tampil-jumlah-potensi-konflik') }}">Potensi Konflik</a>
                        </div>
                        --}}
                    </div></div>
                </li>
            </ul>
        </div>
    </nav>
</div>

<script>
    // 1. MOBILE OFFCANVAS TOGGLE
    const navbarContent = document.getElementById('navbarContent');
    const navBackdrop = document.getElementById('navBackdrop');

    function toggleMobileMenu() {
        const isOpen = navbarContent.classList.toggle('show-offcanvas');
        navBackdrop.classList.toggle('show', isOpen);
        document.body.classList.toggle('menu-open', isOpen);
    }

    function closeMobileMenu() {
        navbarContent.classList.remove('show-offcanvas');
        navBackdrop.classList.remove('show');
        document.body.classList.remove('menu-open');
        document.querySelectorAll('.nav-item.dropdown').forEach(el => el.classList.remove('is-active'));
    }

    // 2. DROPDOWN TOGGLE (DESKTOP & MOBILE)
    function toggleDropdown(event, element) {
        event.preventDefault();
        event.stopPropagation();
        const parent = element.closest('.nav-item.dropdown');
        if (!parent) return;
        const wasActive = parent.classList.contains('is-active');
        document.querySelectorAll('.nav-item.dropdown').forEach(el => el.classList.remove('is-active'));
        if (!wasActive) {
            parent.classList.add('is-active');
        }
    }

    // Klik di luar dropdown untuk menutup menu
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-item.dropdown')) {
            document.querySelectorAll('.nav-item.dropdown').forEach(el => el.classList.remove('is-active'));
        }
    });

    // 3. AUTO ACTIVE LINK HIGHLIGHT
    document.addEventListener('DOMContentLoaded', () => {
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href && href !== '#' && (href === currentPath || (href !== '/' && currentPath.startsWith(href)))) {
                link.closest('.nav-item')?.classList.add('active');
            }
        });
    });

    // 4. SMART SCROLL AUTOHIDE (Ringan & Hemat Kode, 60fps dengan requestAnimationFrame)
    let lastScroll = 0;
    let ticking = false;
    const nav = document.getElementById('mainNav');

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                if (nav) {
                    if (currentScroll > lastScroll && currentScroll > 80) {
                        nav.classList.add('nav-hidden');
                        document.querySelectorAll('.nav-item.dropdown').forEach(el => el.classList.remove('is-active'));
                    } else {
                        nav.classList.remove('nav-hidden');
                    }
                }
                lastScroll = currentScroll <= 0 ? 0 : currentScroll;
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });
</script>