{{-- CTA final (Bloco 7 do briefing).
     Antes a pagina terminava no ultimo <details> da FAQ e caia no footer: quem
     leu tudo e ficou convencido nao tinha botao. --}}
<section class="py-24 bg-dark relative overflow-hidden" aria-labelledby="cta-final-titulo">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-brand/5 rounded-full blur-[100px] pointer-events-none" aria-hidden="true"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">

        <h2 id="cta-final-titulo" class="text-3xl md:text-4xl font-extrabold text-white mb-6 leading-tight">
            Suas horas complementares não vão se resolver sozinhas.
        </h2>

        <p class="text-lg text-gray-400 mb-10">
            Você pode continuar catando dicas soltas na internet e correndo o risco de ficar sem as horas que a
            faculdade exige — ou garantir agora um conhecimento estruturado, com certificado válido, que te
            destaca no mercado.
        </p>

        <ul class="grid sm:grid-cols-3 gap-6 mb-12 text-left sm:text-center">
            @foreach ([
                'ph-certificate' => 'Certificado válido pelo MEC, sem custo adicional',
                'ph-clock-countdown' => 'Acesso imediato e válido por 1 ano inteiro',
                'ph-graduation-cap' => 'Método prático validado por 40 anos de mercado',
            ] as $icone => $texto)
                <li class="flex sm:flex-col items-center sm:items-center gap-3">
                    <i class="ph-fill {{ $icone }} text-3xl text-brand shrink-0" aria-hidden="true"></i>
                    <span class="text-sm text-gray-300">{{ $texto }}</span>
                </li>
            @endforeach
        </ul>

        <a href="#cursos"
            class="inline-flex items-center justify-center gap-2 bg-brand-cta hover:bg-brand-cta-hover active:scale-[0.98] text-white font-semibold py-4 px-10 rounded-full transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] text-lg">
            Garantir Meu Acesso Agora
            <i class="ph-bold ph-arrow-right" aria-hidden="true"></i>
        </a>

        <p class="mt-5 text-sm text-gray-400 inline-flex items-center justify-center gap-2">
            <i class="ph-fill ph-shield-check text-brand" aria-hidden="true"></i>
            7 dias de garantia incondicional &middot; Reembolso integral
        </p>
    </div>
</section>
