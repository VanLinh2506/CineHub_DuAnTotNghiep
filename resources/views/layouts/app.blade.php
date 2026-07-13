<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @auth
        <meta name="auth-user-id" content="{{ Auth::id() }}">
    @endauth
    <title>
        @if(isset($title))
            {{ $title }}@if($title !== 'CineHub') - @endif
        @endif
        CineHub
    </title>
    <link rel="icon" href="{{ storage_url('data/img/avt_webb.png') }}" type="image/png">
    
    @if(isset($meta_description))
        <meta name="description" content="{{ $meta_description }}">
    @endif
    @if(isset($meta_keywords))
        <meta name="keywords" content="{{ $meta_keywords }}">
    @endif
    @if(isset($meta_og_title))
        <meta property="og:title" content="{{ $meta_og_title }}">
    @endif
    @if(isset($meta_og_description))
        <meta property="og:description" content="{{ $meta_og_description }}">
    @endif
    @if(isset($meta_og_image))
        <meta property="og:image" content="{{ $meta_og_image }}">
    @endif
    
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="CineHub">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .cinehub-page-loader {
            position: fixed;
            inset: 0;
            z-index: 20000;
            display: grid;
            place-items: center;
            background: rgba(5, 6, 9, .34);
            backdrop-filter: blur(3px);
            opacity: 1;
            visibility: visible;
            transition: opacity .24s ease, visibility .24s ease;
        }
        .cinehub-page-loader.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        .cute-loader-card {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 12px 17px;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 999px;
            color: #fff;
            background: rgba(19,20,25,.94);
            box-shadow: 0 18px 55px rgba(0,0,0,.42);
            font-size: .82rem;
            font-weight: 750;
        }
        .cute-loader-film {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            color: #fff;
            background: linear-gradient(135deg, #e50914, #ff5965);
            animation: cinehubFilmBounce .75s ease-in-out infinite alternate;
        }
        .cute-loader-dots { display: flex; gap: 3px; }
        .cute-loader-dots i {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #ff5965;
            animation: cinehubDot .8s ease-in-out infinite alternate;
        }
        .cute-loader-dots i:nth-child(2) { animation-delay: .14s; }
        .cute-loader-dots i:nth-child(3) { animation-delay: .28s; }
        @keyframes cinehubFilmBounce { to { transform: translateY(-4px) rotate(5deg); } }
        @keyframes cinehubDot { to { opacity: .25; transform: translateY(-2px); } }
        @media (prefers-reduced-motion: reduce) {
            .cute-loader-film, .cute-loader-dots i { animation: none; }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="cinehub-page-loader" id="cinehubPageLoader" aria-live="polite" aria-label="Đang tải trang">
        <div class="cute-loader-card">
            <span class="cute-loader-film"><i class="fas fa-film"></i></span>
            <span>Đang tải phim</span>
            <span class="cute-loader-dots" aria-hidden="true"><i></i><i></i><i></i></span>
        </div>
    </div>
    @php
        $isAuthPage = Route::is('auth.*') || (request()->has('route') && (
            str_contains(request('route'), 'auth/login') || 
            str_contains(request('route'), 'auth/register')
        ));
    @endphp
    
    @if(!$isAuthPage)
        @include('components.header')
    @endif
    
    <main class="main-content">
        @yield('content')
    </main>
    
    @if(!$isAuthPage)
        @include('components.footer')
    @endif
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @vite(['resources/js/app.js'])

    <script>
        (() => {
            const loader = document.getElementById('cinehubPageLoader');
            if (!loader) return;

            let safetyTimer;
            const showLoader = () => {
                clearTimeout(safetyTimer);
                loader.classList.remove('is-hidden');
                safetyTimer = setTimeout(() => loader.classList.add('is-hidden'), 10000);
            };
            const hideLoader = () => {
                clearTimeout(safetyTimer);
                loader.classList.add('is-hidden');
            };

            window.addEventListener('load', hideLoader);
            window.addEventListener('pageshow', hideLoader);
            window.addEventListener('beforeunload', showLoader);

            document.addEventListener('click', event => {
                const link = event.target.closest('a[href]');
                if (!link || event.defaultPrevented || event.button !== 0) return;
                if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                if (link.target === '_blank' || link.hasAttribute('download')) return;

                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

                const destination = new URL(link.href, window.location.href);
                if (destination.origin === window.location.origin) showLoader();
            });

            document.addEventListener('submit', event => {
                if (!event.defaultPrevented) showLoader();
            });

            safetyTimer = setTimeout(hideLoader, 10000);
        })();
    </script>

    @stack('scripts')
</body>
</html>
