<!-- Hero Section -->
<section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden" aria-labelledby="hero-titulo">
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Text Content -->
            <div class="max-w-2xl">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-muted border border-brand/20 text-brand text-sm font-semibold mb-6">
                    <i class="ph-fill ph-certificate text-lg" aria-hidden="true"></i>
                    Certificado aceito pelo MEC
                </div>
                <h1 id="hero-titulo" class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1] mb-6">
                    Horas complementares garantidas e <br>
                    <span class="text-gradient">prática para alavancar sua carreira.</span>
                </h1>
                {{-- O texto citável por motores generativos (GEO) vive no JSON-LD e na
                     FAQ. Aqui, que é o elemento LCP, o leitor humano vem primeiro. --}}
                <p class="text-lg text-gray-400 mb-8 leading-relaxed max-w-xl">
                    Cursos online de <strong class="text-gray-200">Educação Física, Avaliação Física e Fisiologia do
                    Exercício</strong>, com certificado aceito pelo MEC. Acesso imediato, no seu ritmo, com quem
                    vive a prática há 40 anos.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#cursos"
                        class="bg-brand-cta hover:bg-brand-cta-hover active:scale-[0.98] text-white text-center font-semibold py-4 px-8 rounded-full transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] flex items-center justify-center gap-2">
                        Garantir Meu Acesso Agora <i class="ph-bold ph-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-gray-400 font-medium">
                    <span class="inline-flex items-center gap-2">
                        <i class="ph-fill ph-users-three text-brand text-lg" aria-hidden="true"></i>
                        +8.000 profissionais formados
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i class="ph-fill ph-clock-countdown text-brand text-lg" aria-hidden="true"></i>
                        Acesso imediato
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i class="ph-fill ph-shield-check text-brand text-lg" aria-hidden="true"></i>
                        7 dias de garantia
                    </span>
                </div>
            </div>

            <!-- Hero Logo Showcase -->
            <div
                class="relative lg:ml-auto hidden lg:flex justify-center items-center h-full py-12 lg:py-0 w-full max-w-lg mx-auto lg:mx-0">
                <!-- Elegant Bento Container -->
                <div
                    class="relative w-full aspect-square bg-gradient-to-br from-dark-card/50 to-dark-surface border border-dark-border/80 rounded-[3rem] shadow-2xl flex justify-center items-center overflow-hidden group">

                    <!-- Subtle static ambient light inside the card -->
                    <div
                        class="absolute -top-20 -right-20 w-72 h-72 bg-brand/10 rounded-full blur-[80px] group-hover:bg-brand/15 transition-colors duration-700" aria-hidden="true">
                    </div>
                    <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-brand-light/5 rounded-full blur-[60px]" aria-hidden="true">
                    </div>

                    <!-- Highlighted Logo -->
                    <img src="{{ asset('images/logo-hero.webp') }}"
                        alt="EAFS — Escola de Avaliação Física e Saúde"
                        class="relative z-10 w-64 md:w-80 lg:w-[380px] h-auto drop-shadow-2xl transform group-hover:scale-105 transition-transform duration-700"
                        decoding="async"
                        width="760" height="323">
                </div>
            </div>
        </div>
    </div>
</section>
