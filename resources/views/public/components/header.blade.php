<!-- Navigation -->
<nav class="fixed w-full z-50 glass-nav transition-all duration-300" aria-label="Navegação principal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center gap-8">
                <a href="/" class="flex items-center gap-2" aria-label="EAFS — Página inicial">
                    <img src="{{ asset('images/logo-nav.webp') }}" alt="EAFS" class="h-16 w-auto"
                        width="150" height="64" fetchpriority="high" decoding="async">
                </a>

                <div class="hidden md:flex items-center space-x-6 text-sm font-medium text-gray-300">
                    <a href="#cursos" class="hover:text-brand transition-colors">Cursos</a>
                    <a href="#professor" class="hover:text-brand transition-colors">O Professor</a>
                    <a href="#faq" class="hover:text-brand transition-colors">Dúvidas</a>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @php $cartCount = app(\App\Services\CartService::class)->count(); @endphp
                <button
                    type="button"
                    x-data="{ count: {{ (int) $cartCount }} }"
                    x-on:cart-updated.window="count = $event.detail.count"
                    x-on:click="$dispatch('open-cart')"
                    class="relative p-2 text-gray-300 hover:text-white transition-colors rounded-full hover:bg-white/5"
                    title="Ver carrinho de cursos"
                    aria-label="Ver carrinho de compras">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span
                        x-show="count > 0"
                        x-text="count"
                        style="{{ $cartCount > 0 ? '' : 'display: none;' }}"
                        class="absolute -top-1 -right-1 bg-brand text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center transition-all duration-200">
                        {{ $cartCount > 0 ? $cartCount : '' }}
                    </span>
                </button>

                <a href="{{ route('login') }}"
                    class="text-sm text-gray-400 hover:text-gray-200 transition-colors">Já sou aluno</a>
                <a href="#cursos"
                    class="bg-brand-cta hover:bg-brand-cta-hover active:scale-[0.98] text-white text-sm font-semibold py-3 px-5 rounded-full transition-all shadow-[0_0_15px_rgba(16,185,129,0.4)] hover:shadow-[0_0_25px_rgba(16,185,129,0.6)]">
                    Garantir Acesso
                </a>
            </div>
        </div>
    </div>
</nav>
