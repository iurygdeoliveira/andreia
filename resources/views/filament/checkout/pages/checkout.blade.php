<x-filament-panels::page>
    @inject('cart', 'App\Services\CartService')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Formulário do Comprador --}}
        <div class="lg:col-span-7">
            <form wire:submit.prevent="confirmar">
                {{ $this->form }}

                <div class="mt-6 lg:hidden">
                    {{ $this->gerarCobrancaAction }}
                </div>
            </form>
        </div>

        {{-- Resumo dos Cursos do Carrinho --}}
        <div class="lg:col-span-5">
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex items-center gap-2 pb-4 border-b border-gray-200 dark:border-gray-800">
                    <x-filament::icon
                        icon="heroicon-o-shopping-bag"
                        class="h-5 w-5 text-primary-500"
                    />
                    <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Resumo do Pedido ({{ $cart->count() }} {{ $cart->count() === 1 ? 'curso' : 'cursos' }})
                    </h3>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800 mt-4 max-h-80 overflow-y-auto pr-1">
                    @foreach ($cart->getItems() as $item)
                        <div class="py-3 flex justify-between items-start gap-4">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $item['title'] }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Certificado MEC incluso • Qtd: 1
                                </p>
                            </div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                R$ {{ number_format($item['price'], 2, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-200 dark:border-gray-800 pt-4 mt-4 space-y-2">
                    <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span>R$ {{ number_format($cart->total(), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-base font-bold text-gray-950 dark:text-white pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span>Total (PIX)</span>
                        <span class="text-primary-600 dark:text-primary-400 text-lg">
                            R$ {{ number_format($cart->total(), 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 hidden lg:block">
                    {{ $this->gerarCobrancaAction }}
                </div>

                <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                    <x-filament::icon icon="heroicon-o-shield-check" class="h-4 w-4 text-emerald-500" />
                    <span>Pagamento instantâneo e seguro via PIX</span>
                </div>
            </div>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
