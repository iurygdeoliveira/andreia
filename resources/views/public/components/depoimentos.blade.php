@php
    $depoimentos = config('landing.depoimentos', []);
@endphp

{{-- Pilar 6: unica prova social de terceiro da pagina.
     Renderiza nada enquanto config('landing.depoimentos') estiver vazio - o
     briefing marca as citacoes como pendentes do cliente, e depoimento
     inventado e sinal de confianca falso, nao ausencia de sinal. --}}
@if (filled($depoimentos))
<section id="depoimentos" class="py-20 bg-dark-surface border-y border-dark-border" aria-labelledby="depoimentos-titulo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="text-brand text-sm font-bold tracking-wider uppercase mb-2">Quem já passou por aqui</p>
            <h2 id="depoimentos-titulo" class="text-3xl md:text-4xl font-extrabold text-white">
                Não é promessa. É o que aconteceu com eles.
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach ($depoimentos as $depoimento)
                <figure class="bg-dark-card border border-dark-border rounded-2xl p-6 flex flex-col h-full">
                    <blockquote class="text-gray-300 leading-relaxed flex-grow mb-6">
                        "{{ $depoimento['citacao'] }}"
                    </blockquote>
                    <figcaption class="flex items-center gap-3 pt-5 border-t border-dark-border">
                        @if (filled($depoimento['foto'] ?? null))
                            <img src="{{ asset($depoimento['foto']) }}"
                                alt="Retrato de {{ $depoimento['nome'] }}"
                                width="48" height="48" loading="lazy" decoding="async"
                                class="w-12 h-12 rounded-full object-cover border border-dark-border shrink-0">
                        @endif
                        <div>
                            <p class="text-white font-bold text-sm">{{ $depoimento['nome'] }}</p>
                            <p class="text-gray-400 text-xs">{{ $depoimento['papel'] }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
