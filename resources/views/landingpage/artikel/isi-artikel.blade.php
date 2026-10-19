@extends('landingpage.layouts.app')
@section('title', $post->title)
@section('meta_description', Str::limit(strip_tags($post->content), 150))
@section('meta_image', asset('images/posts/' . $post->image))
@section('og_type', 'article')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/articles.css') }}">
    <style>
        body {
            background-image: linear-gradient(rgba(12, 47, 78, 0.72), rgba(12, 47, 78, 0.72)), url("{{ asset('images/component/BGKesbangpol.png') }}") !important;
            background-repeat: no-repeat !important;
            background-position: center center !important;
            background-size: cover !important;
            background-attachment: fixed !important;
        }
    </style>
@endpush

@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  },
  "headline": {!! json_encode($post->title) !!},
  "image": [
    "{{ asset('images/posts/' . $post->image) }}"
  ],
  "datePublished": "{{ ($post->created_at ?? now())->toIso8601String() }}",
  "dateModified": "{{ ($post->updated_at ?? $post->created_at ?? now())->toIso8601String() }}",
  "author": {
    "@type": "Organization",
    "name": "Badan Kesatuan Bangsa dan Politik Kota Bandung",
    "url": "{{ url('/') }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Badan Kesatuan Bangsa dan Politik Kota Bandung",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/component/logoremovebg2.png') }}"
    }
  },
  "description": {!! json_encode(Str::limit(strip_tags($post->content), 160)) !!},
  "articleSection": {!! json_encode(optional($post->bidang)->nama_bidang ?? 'Berita') !!}
}
</script>
@endpush
@section('content')
<div class="back-to-home">
    <a href="{{ route('beranda') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        <span class="label-text">Kembali ke Beranda</span>
    </a>
</div>

<div class="container-article">
    <!-- KONTEN ARTIKEL -->
    <div class="article-main">
        <div class="article-wrapper">
            <div class="article-image">
                <img src="{{ asset('images/posts/' . $post->image) }}" alt="{{ $post->title }}">
            </div>

            <div class="article-content-wrapper">
                <div class="article-category">{{ $post->bidang->nama_bidang }}</div>
                
                <h1 class="article-title">{{ $post->title }}</h1>
                
                <div class="article-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $post->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span>{{ $post->bidang->nama_bidang }}</span>
                    </div>
                </div>

                <div class="article-content">
                    {!! $post->content !!}
                </div>

                <!-- Tombol Share -->
                <x-share-section :title="$post->title" heading="Bagikan Artikel Ini" :inline="true" />
            </div>
        </div>
    </div>

    <!-- SIDEBAR ARTIKEL LAINNYA -->
    <aside class="article-sidebar">
        <h4>Artikel Terkait</h4>
        <div class="sidebar-box">
            @foreach($latestPosts as $item)
                <a href="{{ route('isi-artikel', $item->slug) }}" class="sidebar-article-link">
                    <div class="sidebar-article">
                        <!-- Gambar -->
                        <div class="sidebar-thumb">
                            <img src="{{ asset('images/posts/' . $item->image) }}" alt="{{ $item->title }}">
                        </div>

                        <!-- Info -->
                        <div class="sidebar-info">
                            <h5 class="sidebar-title">
                                {{ Str::limit($item->title, 65) }}
                            </h5>
                            <div class="sidebar-meta">
                                <span class="sidebar-date">
                                    {{ $item->created_at->translatedFormat('d M Y') }}
                                </span>
                                <span class="sidebar-category">
                                    {{ $item->bidang->nama_bidang }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <a href="{{ route('semua-artikel') }}" class="lihat-semua-artikel-isi-artikel-btn">
            <span>Lihat Semua Artikel</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </aside>
</div>

    // Smooth scroll untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Reading progress indicator (optional)
    function updateReadingProgress() {
        const article = document.querySelector('.article-content');
        if (!article) return;

        const articleTop = article.offsetTop;
        const articleHeight = article.offsetHeight;
        const windowHeight = window.innerHeight;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        const progress = Math.min(100, Math.max(0, 
            ((scrollTop - articleTop + windowHeight) / articleHeight) * 100
        ));
        
        // You can use this progress value to update a progress bar
        document.documentElement.style.setProperty('--reading-progress', progress + '%');
    }

    window.addEventListener('scroll', updateReadingProgress);
    updateReadingProgress(); // Initial call
</script>
@endsection