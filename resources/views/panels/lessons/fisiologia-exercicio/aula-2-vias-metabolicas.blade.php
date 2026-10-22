<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 1 &bull; Bioenergética e Trabalho Muscular" badge-icon="heroicon-m-bolt" estimated-time="10 min de leitura" />

    {{-- Main Lesson Section: Bento Hero --}}
    <x-lesson::bento-card>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#10B981]/10 border border-[#10B981]/30 text-[#34D399] text-xs font-bold uppercase tracking-wider">
                <x-filament::icon icon="heroicon-m-bolt" class="h-4 w-4" />
                Módulo 1 &bull; Bioenergética e Trabalho Muscular
            </div>
            <span class="text-xs text-gray-400 font-medium">Tempo estimado: 10 min de leitura</span>
        </div>

        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Vias Metabólicas e <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Ressíntese de ATP</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                A contração muscular depende do fornecimento ininterrupto de ATP. A intensidade e o volume do esforço determinam qual via metabólica predomina no fornecimento imediato e contínuo de energia.
            </p>
        </div>

        {{-- 3 Energy Systems Bento Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
            {{-- System 1 --}}
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-6 rounded-2xl shadow-xl flex flex-col justify-between space-y-4 hover:border-[#10B981]/50 transition duration-300">
                <div class="space-y-2">
                    <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-[#10B981]/10 text-[#34D399] border border-[#10B981]/25 uppercase tracking-wider">Potência Máxima</span>
                    <h3 class="text-lg font-bold text-white">Sistema ATP-CP</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Fosfagênios intramusculares de liberação imediata (0 a 10s). Sem dependência de O₂ e sem acúmulo imediato de lactato.
                    </p>
                </div>
                <div class="pt-3 border-t border-[#1b3329] text-[11px] text-[#10B981] font-semibold">
                    Ex: Sprints e 1RM
                </div>
            </div>

            {{-- System 2 --}}
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-6 rounded-2xl shadow-xl flex flex-col justify-between space-y-4 hover:border-teal-500/50 transition duration-300">
                <div class="space-y-2">
                    <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/25 uppercase tracking-wider">Intermediário</span>
                    <h3 class="text-lg font-bold text-white">Glicólise Anaeróbia</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Degradação rápida do glicogênio com geração de lactato e prótons H⁺ (30s a 2 min), gerando acidose e fadiga neuromuscular transitória.
                    </p>
                </div>
                <div class="pt-3 border-t border-[#1b3329] text-[11px] text-teal-300 font-semibold">
                    Ex: Séries hipertróficas
                </div>
            </div>

            {{-- System 3 --}}
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-6 rounded-2xl shadow-xl flex flex-col justify-between space-y-4 hover:border-cyan-500/50 transition duration-300">
                <div class="space-y-2">
                    <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 uppercase tracking-wider">Alta Capacidade</span>
                    <h3 class="text-lg font-bold text-white">Sistema Oxidativo</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Fosforilação oxidativa na matriz mitocondrial (Krebs + Cadeia de Elétrons). Queima sustentada de carboidratos e lipídios.
                    </p>
                </div>
                <div class="pt-3 border-t border-[#1b3329] text-[11px] text-cyan-300 font-semibold">
                    Ex: Corrida de longa duração
                </div>
            </div>
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Diagramas Bioenergéticos --}}
    <x-lesson::bento-card>
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-chart-bar" class="h-5 w-5 text-[#10B981]" />
            Cinética dos Sistemas Energéticos e Mitocôndria
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image7.png') }}" 
                     alt="Cinética Metabólica" 
                     class="w-full h-48 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Cinética de contribuição dos sistemas energéticos.
                </div>
            </div>

            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image10.png') }}" 
                     alt="Mitocôndria e Fosforilação" 
                     class="w-full h-48 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Fosforilação oxidativa na crista mitocondrial.
                </div>
            </div>

            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image15.png') }}" 
                     alt="Ciclo de Krebs e Metabolismo" 
                     class="w-full h-48 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Ciclo de Krebs e beta-oxidação lipídica.
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image12.png') }}" 
                     alt="Oxidação de Substratos" 
                     class="w-full h-56 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
            </div>
            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image17.png') }}" 
                     alt="Visão Sistêmica do Esforço" 
                     class="w-full h-56 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
            </div>
        </div>

        {{-- Key Takeaways Callout --}}
        <x-lesson::callout title="Conceito do Continuum Bioenergético">
            Nenhuma via energética atua em total exclusividade. A sobreposição coordenada de cada sistema garante tanto a explosão inicial necessária em testes de esforço quanto a sustentabilidade de cargas prolongadas em programas de condicionamento físico.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
