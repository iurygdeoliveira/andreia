@extends('public.layouts.app')

@section('title', 'Pagamento PIX | ' . ($course?->title ?? ('Pedido ' . $order->order_number)) . ' | Método Andreia 360')
@section('description', 'Finalize o pagamento via PIX para liberar seu acesso.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="pt-32 pb-24 lg:pt-40" aria-labelledby="pagamento-titulo">
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 relative">

        <div class="bg-dark-card border border-dark-border rounded-2xl p-6 md:p-10 shadow-2xl text-center">

            @if ($order->grantsAccess())
                {{-- O webhook ja confirmou: nao faz sentido mostrar QR Code. --}}
                <div class="w-16 h-16 rounded-full bg-brand-muted flex items-center justify-center text-brand mx-auto mb-6">
                    <i class="ph-fill ph-check-circle text-4xl" aria-hidden="true"></i>
                </div>
                <h1 id="pagamento-titulo" class="text-2xl md:text-3xl font-extrabold text-white mb-3">
                    Pagamento confirmado
                </h1>
                <p class="text-gray-400 mb-8">
                    Enviamos para <strong class="text-white">{{ $order->buyer_email }}</strong> as instruções
                    de acesso à plataforma.
                </p>

            @elseif (! $order->payment_qr_code_payload)
                {{-- A cobrança pode existir mesmo quando o QR Code está indisponível. --}}
                <div class="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 mx-auto mb-6">
                    <i class="ph-fill ph-warning-circle text-4xl" aria-hidden="true"></i>
                </div>
                <h1 id="pagamento-titulo" class="text-2xl md:text-3xl font-extrabold text-white mb-3">
                    O código PIX está temporariamente indisponível
                </h1>
                <p class="text-gray-400 mb-8">
                    Atualize esta página para consultar novamente o código do seu pedido.
                </p>
                <a href="{{ route('pedido.pagamento', ['pedido' => $order->order_number]) }}"
                    class="inline-flex items-center gap-2 bg-brand hover:bg-brand-hover text-white font-bold px-8 py-3.5 rounded-full transition-all">
                    Tentar novamente
                    <i class="ph-bold ph-arrow-right" aria-hidden="true"></i>
                </a>

            @else
                <h1 id="pagamento-titulo" class="text-2xl md:text-3xl font-extrabold text-white mb-3">
                    Escaneie para pagar
                </h1>
                <p class="text-gray-400 mb-8">
                    Abra o app do seu banco, escaneie o código e o acesso é liberado automaticamente.
                </p>

                @if ($order->payment_qr_code_image)
                    <img src="data:image/png;base64,{{ $order->payment_qr_code_image }}"
                        alt="QR Code para pagamento via PIX" width="260" height="260"
                        class="mx-auto mb-8 rounded-xl bg-white p-3">
                @endif

                <div class="text-left mb-8">
                    <label for="pix-payload" class="block text-sm font-medium text-gray-300 mb-1.5">
                        Ou copie o código PIX
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="pix-payload" readonly value="{{ $order->payment_qr_code_payload }}"
                            class="w-full bg-dark border border-dark-border rounded-lg py-3 px-4 text-white text-sm outline-none">
                        <button type="button" id="copiar-pix"
                            class="shrink-0 bg-brand hover:bg-brand-hover text-white font-bold px-5 rounded-lg transition-all flex items-center gap-2">
                            <i class="ph-fill ph-copy text-lg" aria-hidden="true"></i>
                            <span>Copiar</span>
                        </button>
                    </div>
                </div>

                <p class="text-sm text-gray-400 flex items-center justify-center gap-2" role="status">
                    <i class="ph-fill ph-clock-countdown text-brand" aria-hidden="true"></i>
                    Aguardando o pagamento. Esta página se atualiza sozinha.
                </p>
            @endif

            <p class="mt-8 pt-6 border-t border-dark-border text-xs text-gray-500">
                Pedido <strong class="text-gray-400">{{ $order->order_number }}</strong>
            </p>
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
