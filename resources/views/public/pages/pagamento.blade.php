@extends('public.layouts.app')

@section('title', 'Pagamento PIX | ' . ($course?->title ?? ('Pedido ' . $order->order_number)) . ' | Método Andreia 360')
@section('description', 'Finalize o pagamento via PIX para liberar seu acesso.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="pt-32 pb-24 lg:pt-40 relative z-10" aria-labelledby="pagamento-titulo">
    <div class="max-w-2xl mx-auto px-6 relative">

        <div class="pricing-premium rounded-[2rem]">
            <div class="bg-[#021f3b] border border-accent-gold/40 rounded-[2rem] p-8 md:p-12 shadow-2xl text-center relative z-10">

                @if ($order->grantsAccess())
                    {{-- O webhook ja confirmou: nao faz sentido mostrar QR Code. --}}
                    <div class="w-16 h-16 rounded-2xl bg-accent-gold/15 border border-accent-gold/30 flex items-center justify-center text-accent-gold mx-auto mb-6 shadow-[0_0_25px_rgba(212,168,67,0.18)]">
                        <i class="ph-fill ph-check-circle text-4xl text-accent-gold" aria-hidden="true"></i>
                    </div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-gold/10 border border-accent-gold/25 text-accent-gold text-xs font-semibold tracking-wider uppercase mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-gold"></span>
                        Acesso Liberado
                    </span>
                    <h1 id="pagamento-titulo" class="font-heading text-3xl sm:text-4xl font-black uppercase tracking-tighter text-white mb-4">
                        Pagamento confirmado
                    </h1>
                    <p class="text-brand-light font-light leading-relaxed mb-8">
                        Enviamos para <strong class="text-white font-semibold">{{ $order->buyer_email }}</strong> as instruções
                        de acesso à plataforma.
                    </p>
                    <a href="{{ url('/login') }}"
                        class="btn-primary inline-flex items-center gap-2 font-heading font-black uppercase tracking-wider px-8 py-4 rounded-2xl">
                        Acessar Área da Aluna
                        <i class="ph-bold ph-arrow-right" aria-hidden="true"></i>
                    </a>

                @elseif (! $order->payment_qr_code_payload)
                    {{-- A cobrança pode existir mesmo quando o QR Code está indisponível. --}}
                    <div class="w-16 h-16 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 mx-auto mb-6">
                        <i class="ph-fill ph-warning-circle text-4xl" aria-hidden="true"></i>
                    </div>
                    <h1 id="pagamento-titulo" class="font-heading text-2xl sm:text-3xl font-black uppercase tracking-tighter text-white mb-3">
                        O código PIX está temporariamente indisponível
                    </h1>
                    <p class="text-brand-light font-light mb-8">
                        Atualize esta página para consultar novamente o código do seu pedido.
                    </p>
                    <a href="{{ route('pedido.pagamento', ['pedido' => $order->order_number]) }}"
                        class="btn-primary inline-flex items-center gap-2 font-heading font-black uppercase tracking-wider px-8 py-4 rounded-2xl">
                        Tentar novamente
                        <i class="ph-bold ph-arrow-right" aria-hidden="true"></i>
                    </a>

                @else
                    <div class="inline-block bg-accent-gold text-brand-navy text-xs font-black uppercase tracking-widest py-1.5 px-5 rounded-full mb-6">
                        Pagamento Instantâneo via PIX
                    </div>
                    <h1 id="pagamento-titulo" class="font-heading text-3xl sm:text-4xl font-black uppercase tracking-tighter text-white mb-3">
                        Escaneie para pagar
                    </h1>
                    <p class="text-brand-light font-light mb-8 max-w-md mx-auto leading-relaxed">
                        Abra o app do seu banco, escaneie o código e o acesso é liberado automaticamente.
                    </p>

                    @if ($order->payment_qr_code_image)
                        <div class="inline-block p-3.5 rounded-2xl bg-white border-2 border-accent-gold/60 shadow-[0_0_30px_rgba(212,168,67,0.18)] mb-8">
                            <img src="data:image/png;base64,{{ $order->payment_qr_code_image }}"
                                alt="QR Code para pagamento via PIX" width="260" height="260"
                                class="rounded-lg block">
                        </div>
                    @endif

                    <div class="text-left mb-8">
                        <label for="pix-payload" class="block text-xs font-bold uppercase tracking-widest text-accent-gold mb-2">
                            Ou copie o código PIX
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2.5">
                            <input type="text" id="pix-payload" readonly value="{{ $order->payment_qr_code_payload }}"
                                class="w-full bg-brand-navy border border-white/15 rounded-xl py-3.5 px-4 text-brand-light text-sm outline-none focus:border-accent-gold">
                            <button type="button" id="copiar-pix"
                                class="btn-primary shrink-0 px-6 py-3.5 rounded-xl font-heading font-black uppercase tracking-wider text-sm flex items-center justify-center gap-2 cursor-pointer">
                                <i class="ph-fill ph-copy text-lg" aria-hidden="true"></i>
                                <span>Copiar</span>
                            </button>
                        </div>
                    </div>

                    <p class="text-sm text-brand-soft flex items-center justify-center gap-2" role="status">
                        <i class="ph-fill ph-clock-countdown text-accent-gold text-lg" aria-hidden="true"></i>
                        Aguardando o pagamento. Esta página se atualiza sozinha.
                    </p>
                @endif

                <p class="mt-8 pt-6 border-t border-white/10 text-xs text-brand-soft">
                    Pedido <strong class="text-white font-semibold">{{ $order->order_number }}</strong>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@unless ($order->grantsAccess())
<script>
    (function () {
        const botao = document.getElementById('copiar-pix');
        const campo = document.getElementById('pix-payload');

        if (botao && campo) {
            botao.addEventListener('click', function () {
                navigator.clipboard.writeText(campo.value).then(function () {
                    const rotulo = botao.querySelector('span');
                    rotulo.textContent = 'Copiado';
                    setTimeout(function () { rotulo.textContent = 'Copiar'; }, 2000);
                });
            });
        }

        // O acesso e liberado pelo webhook, nao por esta pagina: aqui so
        // observamos ate o status virar pago.
        const url = @json(route('pedido.status', ['pedido' => $order->order_number]));

        const timer = setInterval(function () {
            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (dados) {
                    if (dados && dados.pago && dados.redirect) {
                        clearInterval(timer);
                        window.location.href = dados.redirect;
                    }
                })
                .catch(function () { /* rede instavel: tenta de novo no proximo ciclo */ });
        }, 5000);
    })();
</script>
@endunless
@endpush
