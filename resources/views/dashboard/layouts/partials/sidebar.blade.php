<aside class="dashboard-sidebar" id="dashboardSidebar">
        <a href="{{ url('/home') }}" class="dashboard-sidebar-brand d-flex align-items-center justify-content-center py-3">
            <img src="{{ asset('images/component/logo1-2.png') }}" alt="Logo" class="sidebar-logo" width="35" height="35">
            <span class="sidebar-title">BAKESBANGPOL</span>
        </a>
        <hr class="sidebar-divider">
        <nav class="dashboard-sidebar-menu mt-2">
            <ul class="nav flex-column">
                @can('manage-blog')
                <li class="nav-item">
                    <a href="{{ url('admin/blog') }}" class="nav-link">
                        <i class="fas fa-blog me-2"></i> <span class="sidebar-text">Blog</span>
                    </a>
                </li>
                @endcan

                <li class="nav-item mt-2">
                    <span class="sidebar-heading">Beranda</span>
                    <a href="{{ url('/posts') }}" class="nav-link {{ Request::is('posts*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper me-2"></i> <span class="sidebar-text">Artikel</span>
                    </a>

                    <a href="{{ url('/banners') }}" class="nav-link {{ Request::is('banners*') ? 'active' : '' }}">
                        <i class="fas fa-image me-2"></i> <span class="sidebar-text">Banner</span>
                    </a>

                    <a href="{{ url('/galeris') }}" class="nav-link {{ Request::is('galeris*') ? 'active' : '' }}">
                        <i class="fas fa-images me-2"></i> <span class="sidebar-text">Galeri</span>
                    </a>
                </li>
                

                <li class="nav-item mt-2">
                    <span class="sidebar-heading">Profil</span>
                    <li class="nav-item">
                        <a href="{{ url('/bidangs') }}" class="nav-link {{ Request::is('bidangs*') ? 'active' : '' }}">
                            <i class="fas fa-sitemap me-2"></i> <span class="sidebar-text">Bidang</span>
                        </a>

                        <a href="{{ url('/visimisis') }}" class="nav-link {{ Request::is('visimisis*') ? 'active' : '' }}">
                            <i class="fas fa-eye me-2"></i> <span class="sidebar-text">Profil Organisasi</span>
                        </a>

                        <a href="{{ url('/strukturors') }}" class="nav-link {{ Request::is('strukturors') || Request::is('strukturors?*') ? 'active' : '' }}">
                            <i class="fas fa-list me-2"></i> <span class="sidebar-text">Data Struktur</span>
                        </a>
                        <a href="{{ url('/landasanhukum') }}" class="nav-link {{ Request::is('landasanhukum*') ? 'active' : '' }}">
                            <i class="fas fa-gavel me-2"></i> <span class="sidebar-text">Dasar Hukum</span>
                        </a>

                        <a href="{{ url('/programs') }}" class="nav-link {{ Request::is('programs*') ? 'active' : '' }}">
                            <i class="fas fa-list-alt me-2"></i> <span class="sidebar-text">Program</span>
                        </a>
                    </li>
                </li>

                <li class="nav-item mt-2">
                    <span class="sidebar-heading">Sakip</span>
                    <li class="nav-item">
                        <a href="{{ url('/iku') }}" class="nav-link {{ Request::is('iku*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line me-2"></i> <span class="sidebar-text">Indikator Kinerja</span>
                        </a>

                        <a href="{{ url('/renja') }}" class="nav-link {{ Request::is('renja*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt me-2"></i> <span class="sidebar-text">RENJA</span>
                        </a>

                        <a href="{{ url('/renstra') }}" class="nav-link {{ Request::is('renstra*') ? 'active' : '' }}">
                            <i class="fas fa-bullseye me-2"></i> <span class="sidebar-text">RENSTRA</span>
                        </a>

                        <a href="{{ url('/ukurkerja') }}" class="nav-link {{ Request::is('ukurkerja*') ? 'active' : '' }}">
                            <i class="fas fa-tasks me-2"></i> <span class="sidebar-text">Pengukuran Kerja</span>
                        </a>

                        <a href="{{ url('/lakip') }}" class="nav-link {{ Request::is('lakip*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt me-2"></i> <span class="sidebar-text">Laporan AKIP</span>
                        </a>

                        <a href="{{ url('/laporankajian') }}" class="nav-link {{ Request::is('laporankajian*') ? 'active' : '' }}">
                            <i class="fas fa-book-open me-2"></i> <span class="sidebar-text">Laporan Kajian</span>
                        </a>
                    </li>
                </li>

                <li class="nav-item mt-2">
                    <span class="sidebar-heading">Mitra</span>
                    <li class="nav-item">
                        <a href="{{ url('/mitras') }}" class="nav-link {{ Request::is('mitras*') ? 'active' : '' }}">
                            <i class="fas fa-handshake me-2"></i> <span class="sidebar-text">Mitra</span>
                        </a>
                    </li>
                </li>

                <li class="nav-item mt-2">
                    <span class="sidebar-heading">Informasi</span>
                    <li class="nav-item">
                        <a href="{{ url('/ormass') }}" class="nav-link {{ Request::is('ormass*') ? 'active' : '' }}">
                            <i class="fas fa-users me-2"></i> <span class="sidebar-text">Organisasi Masyarakat</span>
                        </a>
                        {{-- Sembunyikan Potensi Konflik
                        <a href="{{ url('/potensi-konflik') }}" class="nav-link {{ Request::is('potensi-konflik*') ? 'active' : '' }}">
                            <i class="fas fa-magnifying-glass-chart me-2"></i> <span class="sidebar-text">Potensi Konflik</span>
                        </a>
                        --}}
                        <a href="{{ route('admin.pemilu-raya.dashboard') }}" class="nav-link {{ Request::is('admin/pemilu-raya*') || Request::is('admin/pilpres*') ? 'active' : '' }}">
                            <i class="fas fa-vote-yea me-2"></i> <span class="sidebar-text">Pemilu Raya</span>
                        </a>
                    </li>
                </li>

                <li class="nav-item mt-2">
                    <span class="sidebar-heading">Pengaturan</span>
                    <li class="nav-item">
                        <a href="{{ route('settings.social-media') }}" class="nav-link {{ Request::is('settings/social-media*') ? 'active' : '' }}">
                            <i class="fas fa-share-alt me-2"></i> <span class="sidebar-text">Media Sosial & Kontak</span>
                        </a>
                    </li>
                </li>

            </ul>
        </nav>

        {{-- Profil Admin — dipindah ke sini dari navbar kanan atas --}}
        @auth
        <div class="sidebar-footer">
            <div class="sidebar-user-dropdown" id="sidebarUserDropdown">
                <button type="button" class="sidebar-user-btn" id="sidebarUserBtn">
                    <span class="sidebar-user-avatar"><i class="fas fa-user"></i></span>
                    <span class="sidebar-user-info sidebar-text">
                        <span class="sidebar-user-name">{{ Auth::user()->name }}</span>
                        <span class="sidebar-user-role">Administrator</span>
                    </span>
                    <i class="fas fa-chevron-up sidebar-user-caret sidebar-text"></i>
                </button>
                <ul class="sidebar-user-menu">
                    <li>
                        <a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profil</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        @endauth
    </aside>
    <button type="button" class="dashboard-sidebar-backdrop" id="dashboardSidebarBackdrop"
        aria-label="Tutup menu navigasi" tabindex="-1"></button>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.dashboard-sidebar');
            const menuToggle = document.getElementById('dashboardMobileMenuToggle');
            const backdrop = document.getElementById('dashboardSidebarBackdrop');
            const closeMobileMenu = function () {
                document.body.classList.remove('admin-sidebar-open');
                if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
            };

            if (sidebar && menuToggle && backdrop) {
                menuToggle.addEventListener('click', function () {
                    const isOpen = document.body.classList.toggle('admin-sidebar-open');
                    menuToggle.setAttribute('aria-expanded', String(isOpen));
                });
                backdrop.addEventListener('click', closeMobileMenu);
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') closeMobileMenu();
                });
                sidebar.querySelectorAll('.dashboard-sidebar-menu .nav-link').forEach(function (link) {
                    link.addEventListener('click', closeMobileMenu);
                });
            }

            const dropdown = document.getElementById('sidebarUserDropdown');
            const btn = document.getElementById('sidebarUserBtn');
            if (!dropdown || !btn) return;

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('open');
            });

            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });
        });
    </script>