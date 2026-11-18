<!-- Footer -->
<footer class="bg-[#010a1f] py-16 border-t border-white/5 text-center text-brand-soft/70 relative z-10">
    <div class="max-w-7xl mx-auto px-6">
        <div class="font-heading font-black text-2xl tracking-tighter text-white/50 mb-6">
            ANDREIA<span class="text-accent-gold/50">360</span>
        </div>
        <p class="mb-4">&copy; {{ now()->year }} Andreia Castro Fit. Transformando mulheres reais.</p>
        <p class="text-xs text-brand-soft/50 mb-6">Método Andreia 360 &bull; Saúde hormonal e emagrecimento sustentável 40+</p>
        <nav aria-label="Links legais" class="flex items-center justify-center gap-6 text-xs text-brand-soft/70">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Início</a>
            <a href="{{ route('privacidade') }}" class="hover:text-accent-gold transition-colors">Política de Privacidade</a>
        </nav>
    </div>
</footer>
