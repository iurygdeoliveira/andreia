<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@hasSection('title')@yield('title')@else{{ $title ?? config('app.name', 'Método Andreia 360') }}@endif</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="@hasSection('description')@yield('description')@else{{ $description ?? 'Emagreça depois dos 40 sem passar fome. Método Andreia 360: treino, nutrição hormonal e suporte emocional.' }}@endif">
    <meta name="author" content="Andreia Castro">
    <meta name="theme-color" content="#010d26">
    <meta name="robots" content="@hasSection('robots')@yield('robots')@else{{ $robots ?? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' }}@endif">

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@hasSection('title')@yield('title')@else{{ $title ?? config('app.name', 'Método Andreia 360') }}@endif">
    <meta property="og:description" content="@hasSection('description')@yield('description')@else{{ $description ?? 'Emagreça depois dos 40 sem passar fome. Método Andreia 360: treino, nutrição hormonal e suporte emocional.' }}@endif">
    <meta property="og:site_name" content="Método Andreia 360">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:image" content="{{ asset('images/andreia360/andreia-hero.jpg') }}">
    <meta property="og:image:secure_url" content="{{ asset('images/andreia360/andreia-hero.jpg') }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Andreia Castro apresenta o Método Andreia 360">
    <link rel="image_src" href="{{ asset('images/andreia360/andreia-hero.jpg') }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@hasSection('title')@yield('title')@else{{ $title ?? config('app.name', 'Método Andreia 360') }}@endif">
    <meta name="twitter:description" content="@hasSection('description')@yield('description')@else{{ $description ?? 'Emagreça depois dos 40 sem passar fome. Método Andreia 360: treino, nutrição hormonal e suporte emocional.' }}@endif">
    <meta name="twitter:image" content="{{ asset('images/andreia360/andreia-hero.jpg') }}">
    <meta name="twitter:image:alt" content="Andreia Castro apresenta o Método Andreia 360">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Fontes do Design System Andreia 360 (Outfit + Inter) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    {{-- Preload da Inter (subset latin) --}}
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/inter/inter-latin.woff2') }}" crossorigin>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite(['resources/css/app.css', 'resources/css/website.css'])

    @stack('head')
</head>

<body class="font-sans antialiased selection:bg-accent-gold selection:text-brand-navy relative min-h-screen flex flex-col bg-[#010d26] text-[#F0F4F8] overflow-x-hidden">

    {{-- Link de salto para acessibilidade e navegação por teclado --}}
    <a href="#conteudo"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:bg-white focus:text-brand-navy focus:px-4 focus:py-2 focus:rounded-lg focus:font-bold">
        Ir para o conteúdo principal
    </a>

    <div class="noise-bg" aria-hidden="true"></div>

    {{-- Background Orbs (idênticos ao Design System Andreia 360) --}}
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none" aria-hidden="true">
        <div class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] rounded-full bg-brand-deep opacity-30 blur-[120px] mix-blend-screen animate-blob"></div>
        <div class="absolute top-[20%] right-[-10%] w-[30vw] h-[30vw] rounded-full bg-brand-medium opacity-20 blur-[100px] mix-blend-screen animate-blob"></div>
        <div class="absolute bottom-[-20%] left-[20%] w-[35vw] h-[35vw] rounded-full bg-brand-deep opacity-40 blur-[120px] mix-blend-screen animate-blob"></div>
    </div>

    @if(isset($header))
        {{ $header }}
    @elseif(!isset($noHeader) || !$noHeader)
        <x-public::header />
    @endif

    {{-- Main Content --}}
    <main id="conteudo" class="flex-grow">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @if(isset($footer))
        {{ $footer }}
    @elseif(!isset($noFooter) || !$noFooter)
        <x-public::footer />
    @endif

    @livewire('notifications')

    @filamentScripts
    @vite('resources/js/app.js')

    @stack('scripts')
</body>
</html>
