<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    @php
        $rawTitle = trim($__env->yieldContent('title'));
        if (empty($rawTitle) || in_array(strtolower($rawTitle), ['halaman beranda', 'beranda', 'bakesbangpol', 'bakesbangpol kota bandung'])) {
            $pageTitle = 'Bakesbangpol Kota Bandung';
        } else {
            $pageTitle = $rawTitle . ' - Bakesbangpol Kota Bandung';
        }
        $metaDesc = trim($__env->yieldContent('meta_description')) ?: 'Portal Resmi Bakesbangpol (Badan Kesatuan Bangsa dan Politik) Kota Bandung. Menyajikan informasi berita, program kerja, ormas, dan pelayanan publik.';
        $metaImg = trim($__env->yieldContent('meta_image')) ?: asset('images/component/logoremovebg2.png');
        $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
    @endphp

    <title>{{ $pageTitle }}</title>

    <meta name="application-name" content="Bakesbangpol Kota Bandung" />
    <meta name="apple-mobile-web-app-title" content="Bakesbangpol" />
    <meta name="description" content="{{ $metaDesc }}" />
    <meta name="keywords" content="Bakesbangpol Kota Bandung, Bakesbangpol Bandung, Kesbangpol Kota Bandung, Badan Kesatuan Bangsa dan Politik Kota Bandung, Ormas Bandung" />
    <meta name="author" content="Bakesbangpol Kota Bandung" />
    <meta name="robots" content="index, follow" />
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:site_name" content="Bakesbangpol Kota Bandung" />
    <meta property="og:type" content="{{ $ogType }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $pageTitle }}" />
    <meta property="og:description" content="{{ $metaDesc }}" />
    <meta property="og:image" content="{{ $metaImg }}" />

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="{{ url()->current() }}" />
    <meta name="twitter:title" content="{{ $pageTitle }}" />
    <meta name="twitter:description" content="{{ $metaDesc }}" />
    <meta name="twitter:image" content="{{ $metaImg }}" />

    {{-- Vite CSS & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('images/component/logoremovebg2.png') }}" type="image/png" />

    {{-- FontAwesome 6 Free via Global CDN --}}
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{ asset('assets/css/landingpage-shared.css') }}">
    
    {{-- Schema.org Structured Data (JSON-LD) untuk Google Search --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "GovernmentOrganization",
          "@id": "{{ url('/') }}#organization",
          "name": "Bakesbangpol Kota Bandung",
          "legalName": "Badan Kesatuan Bangsa dan Politik Kota Bandung",
          "alternateName": [
            "Bakesbangpol",
            "BAKESBANGPOL",
            "Kesbangpol Kota Bandung",
            "Badan Kesatuan Bangsa dan Politik Kota Bandung",
            "Kesbangpol Bandung"
          ],
          "url": "{{ url('/') }}",
          "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/component/logoremovebg2.png') }}"
          },
          "image": "{{ asset('images/component/logoremovebg2.png') }}",
          "description": "Portal Resmi Bakesbangpol Kota Bandung. Menyajikan informasi berita, program kerja, ormas, dan pelayanan publik.",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "Jl. Wastukencana No.2, Babakan Ciamis, Kec. Sumur Bandung",
            "addressLocality": "Bandung",
            "addressRegion": "Jawa Barat",
            "postalCode": "40117",
            "addressCountry": "ID"
          },
          "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+62-22-4235052",
            "contactType": "customer service",
            "areaServed": "ID",
            "availableLanguage": "Indonesian"
          },
          "sameAs": [
            "https://www.instagram.com/bakesbangpolkotabandung/",
            "https://www.tiktok.com/@kesbangpolkotabandung",
            "https://layanan.bandung.go.id"
          ]
        },
        {
          "@type": "WebSite",
          "@id": "{{ url('/') }}#website",
          "url": "{{ url('/') }}",
          "name": "Bakesbangpol Kota Bandung",
          "alternateName": [
            "Bakesbangpol",
            "BAKESBANGPOL",
            "Bakesbangpol Bandung",
            "Kesbangpol Kota Bandung",
            "Badan Kesatuan Bangsa dan Politik Kota Bandung"
          ],
          "description": "Portal Resmi Bakesbangpol Kota Bandung",
          "publisher": {
            "@id": "{{ url('/') }}#organization"
          },
          "inLanguage": "id-ID"
        }
      ]
    }
    </script>
    @stack('schema')

    {{-- Tempat tambahan CSS dari blade lain --}}
    @stack('styles')
</head>
<body>
    <div id="app" class="main-wrapper">
        {{-- Header --}}
        @include('landingpage.shared.header')

        {{-- Main Content --}}
        <main class="main-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('landingpage.shared.footer')
    </div>

    {{-- Overlay loading --}}
    <div id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    {{-- Scroll to top button --}}
    <button id="scrollTopBtn" class="scroll-top-btn" title="Kembali ke atas">
        <i class="fas fa-arrow-up"></i>
    </button>

    {{-- Floating CS WhatsApp Button --}}
    @include('components.cs-button')

    {{-- Tempat tambahan JS dari blade lain --}}
    @stack('scripts')
</body>
</html>
