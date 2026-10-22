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
                Equilíbrio Hidroeletrolítico e <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Reposição Hidrolítica</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                A sudorese elimina fluidos e eletrólitos vitais. A manutenção da osmolaridade e do volume vascular requer equilíbrio preciso entre a vasopressina (ADH), a aldosterona e o peptídeo natriurético atrial (ANP).
            </p>
        </div>

        {{-- Renal & Cardiovascular Hormonal Balance --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-7 rounded-2xl shadow-xl space-y-3 hover:border-[#10B981]/40 transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#10B981]/15 border border-[#10B981]/30 flex items-center justify-center text-[#10B981]">
                        <x-filament::icon icon="heroicon-m-shield-check" class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-white">Vasopressina (ADH) e Eixo RAA</h3>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed">
                    O aumento da osmolaridade plasmática ativa a liberação de <strong class="text-white">ADH</strong> pelos núcleos hipotalâmicos, retendo água livre nos ductos coletores renais. Concomitantemente, a <strong class="text-emerald-300">Aldosterona</strong> reabsorve Sódio ($Na^+$) para preservar o volume circulante.
                </p>
            </div>

            <div class="bg-[#050a08]/80 border border-[#1b3329] p-7 rounded-2xl shadow-xl space-y-3 hover:border-teal-500/40 transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-300">
                        <x-filament::icon icon="heroicon-m-arrows-pointing-out" class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-white">Peptídeo Natriurético Atrial (ANP)</h3>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed">
                    Secretado pelos miócitos atriais em resposta à sobrecarga volumétrica. Relaxa a musculatura lisa dos vasos, eleva a permeabilidade capilar e nos túbulos renais contra-regula o eixo RAA para evitar picos hipertensivos.
                </p>
            </div>
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Diretrizes de Hidratação e Bebidas Isotônicas --}}
    <x-lesson::bento-card>
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-beaker" class="h-5 w-5 text-[#10B981]" />
            Protocolos de Hidratação e Reposição Eletrolítica
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <div class="md:col-span-7 space-y-4">
                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-1.5 hover:border-[#10B981]/40 transition">
                    <span class="text-xs font-bold text-[#10B981] uppercase">1. Fase Pré-Exercício (4h antes)</span>
                    <h4 class="font-bold text-white text-sm">Estado de Eu-Hidratação</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        Ingestão de 5 a 7 mL de água por quilograma de peso corporal nas 4 horas que antecedem o treino para garantir volume plasmático basal ideal.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-1.5 hover:border-teal-500/40 transition">
                    <span class="text-xs font-bold text-teal-400 uppercase">2. Fase Intra-Exercício (&gt; 60 min)</span>
                    <h4 class="font-bold text-white text-sm">Bebidas Isotônicas com Eletrólitos</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        Consumir soluções contendo de 4% a 8% de carboidratos e 20 a 30 mEq/L de Sódio ($Na^+$) a cada 15-20 minutos, prevenindo a hiponatremia e a fadiga periférica.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-1.5 hover:border-cyan-500/40 transition">
                    <span class="text-xs font-bold text-cyan-400 uppercase">3. Fase Pós-Exercício (2 a 4h após)</span>
                    <h4 class="font-bold text-white text-sm">Restauração Total do Peso Corporal</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        Repor de 125% a 150% do peso corporal perdido durante a sessão de treino acompanhado de refeições contendo eletrólitos.
                    </p>
                </div>
            </div>

            <div class="md:col-span-5 rounded-2xl border border-[#1b3329] bg-[#050a08] p-5 group overflow-hidden shadow-2xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image45.jpeg') }}" 
                     alt="Composição de Bebidas Eletrolíticas" 
                     class="w-full h-auto object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Esquema 9.1 — Composição ideal e absorção entérica de isotônicos.
                </div>
            </div>
        </div>

        {{-- Course Completion Grand Bento Banner --}}
        <div class="bg-gradient-to-br from-[#10B981]/20 via-[#0d1a15] to-[#050a08] border border-[#10B981]/50 rounded-3xl p-8 sm:p-10 text-center space-y-4 shadow-2xl relative overflow-hidden">
            <div class="w-16 h-16 rounded-full bg-[#10B981]/25 border border-[#10B981]/50 flex items-center justify-center text-[#34D399] mx-auto shadow-[0_0_20px_rgba(16,185,129,0.4)]">
                <x-filament::icon icon="heroicon-m-academic-cap" class="h-9 w-9" />
            </div>

            <h3 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                Parabéns! Você Concluiu o Curso de Fisiologia do Exercício Muscular
            </h3>

            <p class="text-sm text-gray-300 max-w-2xl mx-auto leading-relaxed">
                Você finalizou com sucesso os 4 módulos de aprendizado sobre bioenergética muscular, dinâmica cardiovascular, controle respiratório, termorregulação e ajustes neuroendócrinos.
            </p>
        </div>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" title="Curso 100% Concluído" subtitle="Você completou todos os módulos de Fisiologia do Exercício Muscular." />
</div>
</x-filament-panels::page>
