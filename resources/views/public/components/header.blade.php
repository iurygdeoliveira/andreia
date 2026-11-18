<!-- Navigation -->
<nav class="fixed w-full z-40 glass-panel border-b-0 py-4 transition-all duration-300" aria-label="Navegação principal">
    <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
        <a href="{{ route('home') }}" class="font-heading font-black text-2xl tracking-tighter text-white"
            aria-label="Método Andreia 360 — página inicial">
            ANDREIA<span class="text-accent-gold">360</span>
        </a>

        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}"
                class="text-xs sm:text-sm font-semibold text-brand-soft hover:text-white transition-colors">
                Área da Aluna
            </a>
            <a href="{{ route('home') }}#planos"
                class="text-xs sm:text-sm font-bold text-brand-navy bg-white px-5 py-2 rounded-full hover:bg-base-off transition-colors">
                Garantir Vaga
            </a>
        </div>
    </div>
</nav>
