<x-filament-panels::page>
    @inject('cart', 'App\Services\CartService')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    <style>
        body:has(.checkout-andreia) {
            font-family: 'Inter', sans-serif !important;
            background-color: #010d26 !important;
            background-image: radial-gradient(circle at 50% 0%, rgba(2, 48, 89, 0.45) 0%, rgba(1, 13, 38, 1) 55%) !important;
            color: #F0F4F8 !important;
        }

        body:has(.checkout-andreia) .fi-topbar {
            background-color: rgba(1, 13, 38, 0.92) !important;
            backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid rgba(156, 188, 217, 0.12) !important;
        }

        body:has(.checkout-andreia) .fi-header-heading {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: -0.03em !important;
            color: #ffffff !important;
            font-size: clamp(2rem, 4vw, 3rem) !important;
        }

        .checkout-andreia .fi-section {
            background-color: #023059 !important;
            border: 1px solid rgba(156, 188, 217, 0.18) !important;
            border-radius: 2rem !important;
            box-shadow: 0 20px 45px -15px rgba(0, 0, 0, 0.45) !important;
            padding: 0.5rem !important;
        }

        .checkout-andreia .fi-section-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding-bottom: 1rem !important;
        }

        .checkout-andreia .fi-section-header-heading {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: -0.02em !important;
            color: #ffffff !important;
            font-size: 1.25rem !important;
        }

        .checkout-andreia .fi-section-header-description {
            color: #9cbcd9 !important;
        }

        .checkout-andreia .fi-section-header-icon {
            color: #D4A843 !important;
        }

        .checkout-andreia .fi-fo-field-wrp-label label {
            color: #F0F4F8 !important;
            font-weight: 600 !important;
        }

        .checkout-andreia .fi-input-wrp {
            background-color: #010d26 !important;
            border: 1px solid rgba(156, 188, 217, 0.22) !important;
            border-radius: 0.75rem !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }

        .checkout-andreia .fi-input-wrp:focus-within {
            border-color: #D4A843 !important;
            box-shadow: 0 0 0 1px #D4A843, 0 0 15px rgba(212, 168, 67, 0.2) !important;
        }

        .checkout-andreia .fi-input-wrp input {
            color: #ffffff !important;
        }

        .checkout-andreia .fi-input-wrp input::placeholder {
            color: rgba(156, 188, 217, 0.55) !important;
        }

        .andreia-pricing-premium {
            position: relative;
        }

        .andreia-pricing-premium::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 2rem;
            background: linear-gradient(135deg, #D4A843, #5881a6, #D4A843);
            z-index: 0;
            opacity: 0.85;
        }

        .checkout-andreia .fi-btn.fi-color-primary {
            background: linear-gradient(135deg, #D4A843 0%, #b58e33 100%) !important;
            border: 1px solid rgba(212, 168, 67, 0.45) !important;
            border-radius: 1rem !important;
            color: #010d26 !important;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 1.1rem 1.75rem !important;
            width: 100% !important;
            box-shadow: 0 12px 28px -8px rgba(212, 168, 67, 0.45) !important;
            transition: all 0.3s ease !important;
        }

        .checkout-andreia .fi-btn.fi-color-primary *,
        .checkout-andreia .fi-btn.fi-color-primary svg {
            color: #010d26 !important;
        }

        .checkout-andreia .fi-btn.fi-color-primary:hover {
            background: linear-gradient(135deg, #e0bc60 0%, #D4A843 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 18px 34px -8px rgba(212, 168, 67, 0.6) !important;
        }

        .andreia-noise-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 40;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }
    </style>

    <div class="andreia-noise-overlay" aria-hidden="true"></div>

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none" aria-hidden="true">
        <div class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] rounded-full bg-[#021f3b] opacity-35 blur-[120px]"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[35vw] h-[35vw] rounded-full bg-[#023059] opacity-25 blur-[120px]"></div>
    </div>

    <div class="checkout-andreia relative z-10">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-white/10 pb-6">
            <div>
                <span class="text-[#D4A843] font-bold text-xs tracking-widest uppercase block mb-2">
                    02 / Checkout Seguro
                </span>
                <p class="text-sm sm:text-base text-[#F0F4F8] font-light border-l-4 border-[#D4A843] pl-4">
                    Preencha seus dados para gerar o QR Code PIX e liberar seu acesso imediatamente após o pagamento.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#D4A843]/10 border border-[#D4A843]/25 text-[#D4A843] text-xs font-semibold uppercase tracking-wider shrink-0 self-start sm:self-center">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D4A843]"></span>
                Ambiente 100% Seguro
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
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
            <div class="lg:col-span-5 lg:sticky lg:top-24">
                <div class="andreia-pricing-premium rounded-[2rem]">
                    <div class="relative z-10 rounded-[2rem] bg-[#021f3b] p-7 md:p-9 shadow-2xl border border-[#D4A843]/40">
                        <div class="inline-block bg-[#D4A843] text-[#010d26] text-[11px] font-black uppercase tracking-widest py-1.5 px-4 rounded-full mb-5">
                            Pagamento Instantâneo via PIX
                        </div>

                        <div class="flex items-center gap-3 pb-5 border-b border-white/10">
                            <x-filament::icon
                                icon="heroicon-o-shopping-bag"
                                class="h-6 w-6 text-[#D4A843]"
                            />
                            <h3 class="text-xl font-black uppercase tracking-tight text-white" style="font-family: 'Outfit', sans-serif;">
                                Resumo do Pedido ({{ $cart->count() }} {{ $cart->count() === 1 ? 'curso' : 'cursos' }})
                            </h3>
                        </div>

                        <div class="divide-y divide-white/10 mt-4 max-h-80 overflow-y-auto pr-1">
                            @foreach ($cart->getItems() as $item)
                                @php
                                    $itemPlan = is_string($item['purchase_type'] ?? null)
                                        ? \App\Enums\PurchaseType::tryFrom($item['purchase_type'])
                                        : null;
                                @endphp
                                <div class="py-4 flex justify-between items-start gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-bold text-white truncate" style="font-family: 'Outfit', sans-serif;">
                                            {{ $item['title'] }}
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            @if ($itemPlan)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-[#D4A843]/15 border border-[#D4A843]/30 text-[#D4A843] text-[11px] font-bold uppercase tracking-wider">
                                                    {{ $itemPlan->getShortLabel() }}
                                                </span>
                                            @endif
                                            <p class="text-xs text-[#9cbcd9]">
                                                Acesso imediato • Método Andreia 360
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-base font-black text-[#D4A843] whitespace-nowrap" style="font-family: 'Outfit', sans-serif;">
                                        R$ {{ number_format($item['price'], 2, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-white/10 pt-5 mt-4 space-y-3">
                            <div class="flex justify-between items-center text-sm text-[#9cbcd9]">
                                <span>Subtotal</span>
                                <span class="font-semibold text-[#F0F4F8]">R$ {{ number_format($cart->total(), 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-white/10">
                                <span class="text-sm font-bold uppercase tracking-widest text-[#F0F4F8]">Total (PIX)</span>
                                <span class="text-3xl font-black text-[#D4A843] tracking-tight" style="font-family: 'Outfit', sans-serif;">
                                    R$ {{ number_format($cart->total(), 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-7 hidden lg:block">
                            {{ $this->gerarCobrancaAction }}
                        </div>

                        <div class="mt-5 flex items-center justify-center gap-2 text-xs text-[#9cbcd9] text-center">
                            <x-filament::icon icon="heroicon-o-shield-check" class="h-4 w-4 text-[#D4A843]" />
                            <span>Pagamento instantâneo e seguro via PIX</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
