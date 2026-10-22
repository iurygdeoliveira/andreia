<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 3 &bull; Ajustes Respiratórios e Termorregulação" badge-icon="heroicon-m-sun" estimated-time="10 min de leitura" />

    {{-- Main Lesson Section: Bento Hero --}}
    <x-lesson::bento-card>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#10B981]/10 border border-[#10B981]/30 text-[#34D399] text-xs font-bold uppercase tracking-wider">
                <x-filament::icon icon="heroicon-m-sun" class="h-4 w-4" />
                Módulo 3 &bull; Ajustes Respiratórios e Termorregulação
            </div>
            <span class="text-xs text-gray-400 font-medium">Tempo estimado: 10 min de leitura</span>
        </div>

        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Termorregulação e <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Distúrbios Térmicos</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                Como 80% da energia do trabalho muscular se dissipa sob a forma de calor, a manutenção da temperatura central exige resposta vasodilatadora cutânea coordenada pelo termostato hipotalâmico.
            </p>
        </div>

        {{-- 2 Core Principles --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-7 rounded-2xl shadow-xl space-y-3 hover:border-[#10B981]/40 transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#10B981]/15 border border-[#10B981]/30 flex items-center justify-center text-[#10B981]">
                        <x-filament::icon icon="heroicon-m-fire" class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-white">Vasodilatação Cutânea e Sudorese</h3>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed">
                    O hipotálamo anterior comanda o redirecionamento do sangue aquecido para a superfície da pele (pele avermelhada e quente) e ativa a secreção sudorípara para perda calórica por evaporação.
                </p>
            </div>

            <div class="bg-[#050a08]/80 border border-[#1b3329] p-7 rounded-2xl shadow-xl space-y-3 hover:border-teal-500/40 transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-300">
                        <x-filament::icon icon="heroicon-m-sparkles" class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-white">"Febre do Exercício" Adaptativa</h3>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed">
                    O organismo eleva seu ponto de ajuste térmico (38°C a 39°C) como mecanismo vantajoso, já que as pontes de actina-miosina e as enzimas miofibrilares atingem maior eficiência mecânica acima de 37°C.
                </p>
            </div>
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Distúrbios Provocados pelo Calor --}}
    <x-lesson::bento-card>
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
                <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-5 w-5 text-amber-400" />
                Quadro Clínico dos Distúrbios Térmicos
            </h3>
            <span class="text-xs px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 font-bold uppercase text-[10px]">Atenção do Treinador</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Colapso --}}
            <div class="bg-[#050a08] border border-amber-500/30 p-6 rounded-2xl space-y-3 shadow-xl flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500/20 text-amber-300 uppercase">1. Colapso por Calor</span>
                    <h4 class="font-bold text-white text-base">Vasodilatação Súbita</h4>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Abertura excessiva da circulação periférica cutânea provoca queda brusca da pressão arterial, palidez reflexa, taquicardia e síncope por hipoperfusão cerebral momentânea.
                    </p>
                </div>
            </div>

            {{-- Exaustão --}}
            <div class="bg-[#050a08] border border-rose-500/30 p-6 rounded-2xl space-y-3 shadow-xl flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-500/20 text-rose-300 uppercase">2. Exaustão Térmica</span>
                    <h4 class="font-bold text-white text-base">Desidratação Severa</h4>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Perda maciça de água pelo suor sem reposição proporcional. Caracteriza-se por fraqueza extrema, cefaleia intensa, náuseas, desorientação e vertigem.
                    </p>
                </div>
            </div>

            {{-- Cãibras --}}
            <div class="bg-[#050a08] border border-cyan-500/30 p-6 rounded-2xl space-y-3 shadow-xl flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-cyan-500/20 text-cyan-300 uppercase">3. Cãibras por Calor</span>
                    <h4 class="font-bold text-white text-base">Depleção Eletrolítica</h4>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Ocorre quando o atleta repõe apenas água pura após sudorese intensa, diluindo o sódio sérico (hiponatremia) e disparando espasmos musculares involuntários muito dolorosos.
                    </p>
                </div>
            </div>
        </div>

        {{-- Cold environment Banner --}}
        <div class="p-6 rounded-2xl bg-[#050a08] border border-blue-500/30 flex flex-col sm:flex-row items-center gap-6 shadow-xl">
            <div class="space-y-2 flex-1">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/10 text-blue-300 text-xs font-bold uppercase">
                    Ambiente Frio e Hipotermia
                </div>
                <p class="text-xs text-gray-300 leading-relaxed">
                    Em modalidades de longa duração em clima frio (triatlo, natação em águas abertas), a taxa de perda calórica supera a termogênese metabólica muscular. A hipotermia severa compromete a coordenação motora e o julgamento cognitivo.
                </p>
            </div>
            <div class="w-full sm:w-44 rounded-xl overflow-hidden border border-[#1b3329] shrink-0">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image51.jpg') }}" alt="Exercício no Frio" class="w-full h-28 object-cover" />
            </div>
        </div>

        {{-- Visual Showcase --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image34.jpg') }}" 
                     alt="Termorregulação e Sudorese" 
                     class="w-full h-48 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
            </div>
            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image47.jpg') }}" 
                     alt="Exaustão por Calor" 
                     class="w-full h-48 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
            </div>
        </div>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
