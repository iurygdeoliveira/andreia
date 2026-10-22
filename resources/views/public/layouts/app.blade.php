<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@hasSection('title')@yield('title')@else{{ $title ?? config('app.name', 'EAFS - Portal de Cursos') }}@endif</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="@hasSection('description')@yield('description')@else{{ $description ?? 'A maior plataforma de atualização profissional e cursos online para áreas da saúde com certificação reconhecida pelo MEC.' }}@endif">
    <meta name="author" content="EAFS">
    <meta name="robots" content="@hasSection('robots')@yield('robots')@else{{ $robots ?? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' }}@endif">

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@hasSection('title')@yield('title')@else{{ $title ?? config('app.name', 'EAFS - Portal de Cursos') }}@endif">
    <meta property="og:description" content="@hasSection('description')@yield('description')@else{{ $description ?? 'A maior plataforma de atualização profissional e cursos online para áreas da saúde com certificação reconhecida pelo MEC.' }}@endif">
    <meta property="og:site_name" content="EAFS">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:image" content="{{ asset('images/og-cover.jpg') }}">
    <meta property="og:image:secure_url" content="{{ asset('images/og-cover.jpg') }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="EAFS — Escola de Avaliação Física e Saúde">
    <link rel="image_src" href="{{ asset('images/og-cover.jpg') }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@hasSection('title')@yield('title')@else{{ $title ?? config('app.name', 'EAFS - Portal de Cursos') }}@endif">
    <meta name="twitter:description" content="@hasSection('description')@yield('description')@else{{ $description ?? 'A maior plataforma de atualização profissional e cursos online para áreas da saúde com certificação reconhecida pelo MEC.' }}@endif">
    <meta name="twitter:image" content="{{ asset('images/og-cover.jpg') }}">
    <meta name="twitter:image:alt" content="EAFS — Escola de Avaliação Física e Saúde">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

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

<body class="antialiased selection:bg-brand selection:text-white relative min-h-screen flex flex-col bg-white dark:bg-[#050a08] text-gray-900 dark:text-[#f8fafc]">

    {{-- Link de salto para acessibilidade e navegação por teclado --}}
    <a href="#conteudo"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:bg-brand focus:text-white focus:px-4 focus:py-2 focus:rounded-full focus:font-semibold">
        Pular para o conteúdo principal
    </a>

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
