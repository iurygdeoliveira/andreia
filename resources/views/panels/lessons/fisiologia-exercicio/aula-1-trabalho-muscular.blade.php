<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 1 &bull; Bioenergética e Trabalho Muscular" badge-icon="heroicon-m-bolt" estimated-time="8 min de leitura" />

    {{-- Main Lesson Section: Bento Hero --}}
    <x-lesson::bento-card>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#10B981]/10 border border-[#10B981]/30 text-[#34D399] text-xs font-bold uppercase tracking-wider">
                <x-filament::icon icon="heroicon-m-bolt" class="h-4 w-4" />
                Módulo 1 &bull; Bioenergética e Trabalho Muscular
            </div>
            <span class="text-xs text-gray-400 font-medium">Tempo estimado: 8 min de leitura</span>
        </div>

        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Fundamentos e <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Eficiência Mecânica</span> do Músculo
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                O músculo esquelético atua como um transdutor biológico sofisticado, convertendo a energia química dos substratos alimentares e do ATP em força de tração e deslocamento biomecânico.
            </p>
        </div>

        {{-- Image Showcase Bento Card --}}
        <div class="relative w-full rounded-2xl border border-[#1b3329] bg-[#050a08] overflow-hidden group shadow-2xl">
            <img src="{{ asset('images/cursos/fisiologia-exercicio/image1.jpg') }}" 
                 alt="Fisiologia do Exercício Muscular" 
                 class="w-full h-72 sm:h-96 object-cover transform group-hover:scale-105 transition-transform duration-700 opacity-95 group-hover:opacity-100" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#050a08] via-transparent to-transparent opacity-80 pointer-events-none"></div>
            <div class="absolute bottom-4 left-6 right-6 flex items-center justify-between text-xs text-gray-300">
                <span class="flex items-center gap-2 font-medium">
                    <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                    Figura 1.1 — Integração neuromuscular e controle biológico da sobrecarga física.
                </span>
                <span class="hidden sm:inline-block px-2.5 py-1 rounded bg-[#0d1a15]/90 border border-[#1b3329] text-gray-400 font-mono text-[10px]">Alta Resolução</span>
            </div>
        </div>

        {{-- Highlight Metric Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <x-lesson::metric-stat 
                value="20%" 
                label="Trabalho Mecânico Útil" 
                description="Fração da energia líquida convertida diretamente em força e movimento biomecânico." 
                icon="heroicon-m-bolt" 
                color="brand" />

            <x-lesson::metric-stat 
                value="80%" 
                label="Dissipação Calórica" 
                description="Energia liberada como calor, ativando os centros de termorregulação hipotalâmica." 
                icon="heroicon-m-fire" 
                color="amber" />
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Fórmula do Trabalho e Rendimento --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Formula Card --}}
        <div class="lg:col-span-1 bg-gradient-to-br from-[#13241d] to-[#0d1a15] border border-[#1b3329] rounded-[2rem] p-7 shadow-xl space-y-5 flex flex-col justify-between">
            <div class="space-y-2">
                <span class="text-[#10B981] text-xs font-bold uppercase tracking-wider">Fórmula Clássica</span>
                <h3 class="text-xl font-bold text-white">Trabalho Mecânico (W)</h3>
            </div>

            <div class="bg-[#050a08] p-6 rounded-2xl border border-[#10B981]/30 text-center space-y-2 shadow-inner">
                <div class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#34D399] to-[#10B981] tracking-widest">
                    W = F &times; d
                </div>
                <div class="text-[11px] text-gray-400 font-mono">Trabalho = Força &times; Distância</div>
            </div>

            <p class="text-xs text-gray-300 leading-relaxed">
                A cada <strong class="text-white font-semibold">427 kg/m</strong> de trabalho realizado são necessários teoricamente 1,0 kcal caso o rendimento biológico fosse 100%. Como a eficiência real é de 20%, o organismo exige cerca de 5 kcal de gasto metabólico total para a mesma tarefa.
            </p>
        </div>

        {{-- Substrates Timeline Card --}}
        <div class="lg:col-span-2 bg-gradient-to-br from-[#13241d] to-[#0d1a15] border border-[#1b3329] rounded-[2rem] p-7 sm:p-8 shadow-xl space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[#10B981] text-xs font-bold uppercase tracking-wider">Metabolismo de Substratos</span>
                    <h3 class="text-xl font-bold text-white mt-1">Transição Energética por Duração do Esforço</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#10B981]/15 border border-[#10B981]/30 flex items-center justify-center text-[#10B981]">
                    <x-filament::icon icon="heroicon-m-fire" class="h-5 w-5" />
                </div>
            </div>

            <div class="space-y-3">
                <div class="p-4 rounded-xl bg-[#050a08] border border-[#1b3329] flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-[#10B981]"></span>
                        <div>
                            <strong class="text-white text-sm block">0 a 60 Minutos &bull; Glicogênio Muscular</strong>
                            <span class="text-xs text-gray-400">Predomínio glicídico intramuscular em esforços de intensidade moderada a alta.</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded bg-[#10B981]/10 text-[#34D399] shrink-0">Fase 1</span>
                </div>

                <div class="p-4 rounded-xl bg-[#050a08] border border-[#1b3329] flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-teal-400"></span>
                        <div>
                            <strong class="text-white text-sm block">60 a 120 Minutos &bull; Glicose Plasmática Hepática</strong>
                            <span class="text-xs text-gray-400">Mobilização de glicose via glicogenólise hepática para manutenção da glicemia.</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded bg-teal-500/10 text-teal-300 shrink-0">Fase 2</span>
                </div>

                <div class="p-4 rounded-xl bg-[#050a08] border border-[#1b3329] flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-cyan-400"></span>
                        <div>
                            <strong class="text-white text-sm block">&gt; 120 Minutos &bull; Lipídios (Ácidos Graxos Livres)</strong>
                            <span class="text-xs text-gray-400">Oxidação contínua de gorduras e preservação do substrato glicídico vital.</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded bg-cyan-500/10 text-cyan-300 shrink-0">Fase 3</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Bento Section 3: Figuras e Conclusão --}}
    <x-lesson::bento-card>
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-photo" class="h-5 w-5 text-[#10B981]" />
            Representações Anatômicas e Bioquímicas
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image4.png') }}" 
                     alt="Mobilização de Substratos" 
                     class="w-full h-64 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Esquema 1.2 — Transição metabólica entre estoques de glicogênio e lipídios circulantes.
                </div>
            </div>

            <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image5.jpeg') }}" 
                     alt="Vias Metabólicas Energéticas" 
                     class="w-full h-64 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Esquema 1.3 — Vias oxidativas e captação periférica de substratos.
                </div>
            </div>
        </div>

        {{-- Key Takeaways Callout --}}
        <x-lesson::callout title="Síntese Prática para o Profissional EAFS">
            58,2 kcal de energia metabolizável podem ser fornecidas por <strong class="text-white">14,2 g de carboidratos</strong> ou por apenas <strong class="text-white">6,3 g de lipídios</strong>. Compreender a transição entre esses substratos é essencial para a correta prescrição de treinos intervalados, testes de esforço e estratégias de avaliação física.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
