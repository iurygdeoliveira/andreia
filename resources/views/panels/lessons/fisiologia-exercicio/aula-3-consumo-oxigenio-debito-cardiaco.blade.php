<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 2 &bull; Ajustes Cardiovasculares e Hemodinâmicos" badge-icon="heroicon-m-heart" estimated-time="9 min de leitura" />

    {{-- Main Lesson Section: Bento Hero --}}
    <x-lesson::bento-card>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#10B981]/10 border border-[#10B981]/30 text-[#34D399] text-xs font-bold uppercase tracking-wider">
                <x-filament::icon icon="heroicon-m-heart" class="h-4 w-4" />
                Módulo 2 &bull; Ajustes Cardiovasculares e Hemodinâmicos
            </div>
            <span class="text-xs text-gray-400 font-medium">Tempo estimado: 9 min de leitura</span>
        </div>

        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Consumo de Oxigênio (VO₂) e <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Débito Cardíaco</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                A oferta de oxigênio aos músculos ativos exige acoplamento imediato entre o volume sistólico ejetado pelos ventrículos e a frequência cardíaca, coordenados pela atividade simpática e retorno venoso.
            </p>
        </div>

        {{-- VO2 Stats Comparison Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <x-lesson::metric-stat 
                value="250" 
                unit="mL/min" 
                label="VO₂ Basal de Repouso" 
                description="Equivalente a 1 MET (taxa metabólica de repouso)." 
                icon="heroicon-m-heart" 
                color="brand" />

            <x-lesson::metric-stat 
                value="> 5.000" 
                unit="mL/min" 
                label="VO₂ Máximo no Esforço" 
                description="Elevação de até 20 vezes o valor basal em atletas de alta performance." 
                icon="heroicon-m-bolt" 
                color="teal" />
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Dinâmica Cardíaca e Frank-Starling --}}
    <x-lesson::bento-card>
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-arrow-trending-up" class="h-5 w-5 text-[#10B981]" />
            Tríade do Ajuste Hemodinâmico
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 space-y-4">
                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-1.5 hover:border-[#10B981]/40 transition">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#10B981] uppercase">
                        <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                        1. Resposta Antecipatória
                    </div>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        Aumento precoce da frequência cardíaca antes mesmo do início da contração motora, desencadeado por descarga simpática central cortical.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-1.5 hover:border-[#10B981]/40 transition">
                    <div class="flex items-center gap-2 text-xs font-bold text-teal-400 uppercase">
                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                        2. Bomba Músculo-Esquelética
                    </div>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        A contração muscular comprime as veias profundas e impulsiona mecanicamente o volume de sangue em direção ao átrio direito.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-1.5 hover:border-[#10B981]/40 transition">
                    <div class="flex items-center gap-2 text-xs font-bold text-cyan-400 uppercase">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        3. Autorregulação Heterométrica (Frank-Starling)
                    </div>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        O estiramento das fibras miocárdicas pelo aumento do volume diastólico final amplifica a força de contração ventricular, elevando o volume sistólico para além de 70 mL por batimento.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-5 rounded-2xl border border-[#1b3329] bg-[#050a08] p-5 group overflow-hidden shadow-2xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image22.png') }}" 
                     alt="Dinâmica Ventricular e Frank-Starling" 
                     class="w-full h-auto object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Esquema 3.1 — Dinâmica ventricular e Lei de Frank-Starling.
                </div>
            </div>
        </div>

        {{-- Key Takeaways Callout --}}
        <x-lesson::callout title="Cálculo do Débito Cardíaco (DC)">
            <strong class="text-white">Débito Cardíaco = Frequência Cardíaca (FC) &times; Volume Sistólico (VS)</strong>. Em indivíduos condicionados, o incremento do débito ocorre principalmente pelo ganho de volume sistólico, gerando menor sobrecarga cronotrópica em repouso e esforços submáximos.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
