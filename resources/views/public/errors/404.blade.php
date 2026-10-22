<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada | EAFS</title>
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    {{-- A Inter vem auto-hospedada dentro do website.css --}}
    @vite('resources/css/website.css')
</head>
<body class="antialiased min-h-screen flex flex-col items-center justify-center px-4">
    <main class="text-center max-w-lg">
        <p class="text-brand font-bold text-sm tracking-widest uppercase mb-4">Erro 404</p>
        <h1 class="text-5xl md:text-6xl font-extrabold mb-6">
            Página <span class="text-gradient">não encontrada</span>
        </h1>
        <p class="text-gray-400 text-lg mb-10 leading-relaxed">
            A página que você está procurando não existe ou foi movida.<br>
            Volte para a home e explore nossos cursos de Educação Física.
        </p>
        <a href="/"
            class="inline-block bg-brand-cta hover:bg-brand-cta-hover text-white font-semibold py-4 px-10 rounded-full transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] text-lg">
            Voltar para o Início
        </a>
        <p class="mt-12 text-sm text-gray-400">
            &copy; {{ now()->year }} EAFS — Escola de Avaliação Física e Saúde
        </p>
    </main>
</body>
</html>
