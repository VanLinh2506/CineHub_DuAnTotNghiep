<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    
    @stack('styles')
</head>
<body>
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

    @stack('scripts')
</body>
</html>
