<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 4 &bull; Sistema Endócrino e Equilíbrio Hidroeletrolítico" badge-icon="heroicon-m-beaker" estimated-time="9 min de leitura" />

    {{-- Main Lesson Section: Bento Hero --}}
    <x-lesson::bento-card>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#10B981]/10 border border-[#10B981]/30 text-[#34D399] text-xs font-bold uppercase tracking-wider">
                <x-filament::icon icon="heroicon-m-beaker" class="h-4 w-4" />
                Módulo 4 &bull; Sistema Endócrino e Equilíbrio Hidroeletrolítico
            </div>
            <span class="text-xs text-gray-400 font-medium">Tempo estimado: 9 min de leitura</span>
        </div>

        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Ajustes Endócrinos no <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Esforço Físico</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                A sinalização hormonal mobiliza estoques periféricos de glicose e gordura, redirecionando o fluxo energético para o miócito em contração e regulando a regeneração miofibrilar pós-treino.
            </p>
        </div>

        {{-- 5 Hormones Bento Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">
            {{-- Adrenalina --}}
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-6 rounded-2xl shadow-xl space-y-3 hover:border-[#10B981]/40 transition">
                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-[#10B981]/10 text-[#34D399] border border-[#10B981]/25 uppercase tracking-wider">Catecolamina</span>
                <h3 class="text-lg font-bold text-white">Adrenalina</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Estimula a <strong class="text-white">lipase hormônio-sensível</strong> para quebrar os triglicerídeos do tecido adiposo em ácidos graxos livres e glicerol, além de intensificar a glicogenólise hepática.
                </p>
            </div>

            {{-- GH --}}
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-6 rounded-2xl shadow-xl space-y-3 hover:border-teal-500/40 transition">
                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/25 uppercase tracking-wider">Hipofisário</span>
                <h3 class="text-lg font-bold text-white">Hormônio do Crescimento (GH)</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Apresenta efeito poupador de glicose tecidual e acentuado potencial lipolítico, aumentando as taxas de ácidos graxos no sangue durante esforços intensos.
                </p>
            </div>

            {{-- Insulina --}}
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-6 rounded-2xl shadow-xl space-y-3 hover:border-cyan-500/40 transition">
                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 uppercase tracking-wider">Pancreático</span>
                <h3 class="text-lg font-bold text-white">Insulina &amp; GLUT-4</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    A contração mecânica induz a translocação das vesículas de GLUT-4 para o sarcolema independentemente da insulina, captando glicose mesmo com a insulinemia reduzida.
                </p>
            </div>

            {{-- Cortisol --}}
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-6 rounded-2xl shadow-xl space-y-3 hover:border-amber-500/40 transition">
                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/25 uppercase tracking-wider">Eixo HHA</span>
                <h3 class="text-lg font-bold text-white">Cortisol</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Estimulado pela elevação de lactato e ativação simpática; promove quebra proteica e lipólise para fornecer aminoácidos e glicerol à gliconeogênese hepática.
                </p>
            </div>

            {{-- Testosterona --}}
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-6 rounded-2xl shadow-xl space-y-3 hover:border-purple-500/40 transition md:col-span-2 lg:col-span-2">
                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/25 uppercase tracking-wider">Anabolismo &amp; Hipertrofia</span>
                <h3 class="text-lg font-bold text-white">Testosterona</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Presente em homens e mulheres (em menor proporção). Estimula a transcrição gênica de novas proteínas miofibrilares, eleva a densidade mineral óssea e promove aumento de força e massa muscular adaptativa.
                </p>
            </div>
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Visualização dos Hormônios --}}
    <x-lesson::bento-card>
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-chart-pie" class="h-5 w-5 text-[#10B981]" />
            Mapeamento da Ação Hormonal
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image40.jpg') }}" 
                     alt="Ação da Adrenalina" 
                     class="w-full h-44 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Ativação da lipase hormônio-sensível por catecolaminas.
                </div>
            </div>

            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image41.gif') }}" 
                     alt="Dinâmica do GH" 
                     class="w-full h-44 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Picos de secreção de GH no treino resistido e intervalado.
                </div>
            </div>

            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image43.jpg') }}" 
                     alt="Cortisol e Eixo HHA" 
                     class="w-full h-44 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Ressíntese de glicogênio e modulação do cortisol.
                </div>
            </div>
        </div>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
