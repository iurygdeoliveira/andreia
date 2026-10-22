<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 2 &bull; Ajustes Cardiovasculares e Hemodinâmicos" badge-icon="heroicon-m-heart" estimated-time="8 min de leitura" />

    {{-- Main Lesson Section: Bento Hero --}}
    <x-lesson::bento-card>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#10B981]/10 border border-[#10B981]/30 text-[#34D399] text-xs font-bold uppercase tracking-wider">
                <x-filament::icon icon="heroicon-m-heart" class="h-4 w-4" />
                Módulo 2 &bull; Ajustes Cardiovasculares e Hemodinâmicos
            </div>
            <span class="text-xs text-gray-400 font-medium">Tempo estimado: 8 min de leitura</span>
        </div>

        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Controle Ortostático e <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Eixo Neuro-Hormonal</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                A mudança da posição deitada para a posição de pé desafia a hemodinâmica pela ação gravitacional. O organismo ativa reflexos autonômicos e peptídeos hipofisários para estabilizar a pressão de perfusão encefálica.
            </p>
        </div>

        {{-- Side-by-side Bento Comparison Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            {{-- Adaptive Orthostatic Response --}}
            <div class="bg-gradient-to-br from-[#10B981]/15 via-[#0d1a15] to-[#050a08] border border-[#10B981]/40 rounded-3xl p-7 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#10B981]/20 border border-[#10B981]/30 flex items-center justify-center text-[#10B981]">
                        <x-filament::icon icon="heroicon-m-arrow-trending-up" class="h-6 w-6" />
                    </div>
                    <div>
                        <span class="text-xs text-[#34D399] font-bold uppercase tracking-wider">Mecanismo Fisiológico</span>
                        <h3 class="text-lg font-bold text-white">Hipertensão Ortostática Adaptativa</h3>
                    </div>
                </div>

                <p class="text-xs text-gray-300 leading-relaxed">
                    A redução transitória do retorno venoso diminui a estimulação dos <strong class="text-white">volumeceptores atriais</strong>. O hipotálamo anterior (núcleos supra-óptico e paraventricular) libera <strong class="text-emerald-300">vasopressina e oxitocina</strong>.
                </p>

                <div class="p-3.5 rounded-xl bg-[#050a08]/80 border border-[#1b3329] text-xs space-y-1.5">
                    <div class="text-gray-300">&bull; Vasoconstrição periférica mediada por receptores &alpha;-adrenérgicos.</div>
                    <div class="text-gray-300">&bull; Liberação de renina renal via receptores &beta;-adrenérgicos, ativando o sistema <strong>Renina-Angiotensina-Aldosterona (SRAA)</strong>.</div>
                </div>
            </div>

            {{-- Orthostatic Hypotension --}}
            <div class="bg-gradient-to-br from-rose-500/10 via-[#0d1a15] to-[#050a08] border border-rose-500/30 rounded-3xl p-7 shadow-2xl space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400">
                        <x-filament::icon icon="heroicon-m-arrow-trending-down" class="h-6 w-6" />
                    </div>
                    <div>
                        <span class="text-xs text-rose-400 font-bold uppercase tracking-wider">Falha de Compensação</span>
                        <h3 class="text-lg font-bold text-white">Hipotensão Ortostática e Síncope</h3>
                    </div>
                </div>

                <p class="text-xs text-gray-300 leading-relaxed">
                    Ocorre quando a resposta reflexa é tardia ou insuficiente. O represamento gravitacional de sangue nos membros inferiores causa hipoperfusão cerebral aguda.
                </p>

                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-200 space-y-1">
                    <strong class="block text-rose-300">Sintomas Clínicos Comuns:</strong>
                    Tontura súbita ao levantar rápido, escurecimento visual, palidez, desorientação e síncope reflexa (desmaio).
                </div>
            </div>
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Visualização e Eixo Hipotalâmico --}}
    <x-lesson::bento-card>
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-academic-cap" class="h-5 w-5 text-[#10B981]" />
            Volumeceptores e Aplicação na Avaliação Física
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <div class="md:col-span-5 rounded-2xl border border-[#1b3329] bg-[#050a08] p-5 group overflow-hidden shadow-2xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image30.jfif') }}" 
                     alt="Volumeceptores Atriais" 
                     class="w-full h-56 object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Esquema 5.1 — Mecanismo dos volumeceptores atriais de baixa pressão.
                </div>
            </div>

            <div class="md:col-span-7 space-y-4">
                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-2">
                    <span class="text-xs font-bold text-[#10B981] uppercase">Protocolo de Desaquecimento</span>
                    <h4 class="font-bold text-white text-base">Importância do Desaquecimento Ativo</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        Interromper abruptamente o exercício vigoroso desativa a bomba músculo-esquelética enquanto o leito vascular periférico ainda se encontra amplamente dilatado, predispondo o indivíduo à hipotensão pós-esforço e síncope.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-2">
                    <span class="text-xs font-bold text-teal-400 uppercase">Segurança no Teste Ergométrico</span>
                    <h4 class="font-bold text-white text-base">Aferição Postural da Pressão Arterial</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        A mensuração da pressão arterial em decúbito e na posição ortostática antes do início do teste avalia a integridade do reflexo barorreceptor e a reatividade neuroendócrina do aluno.
                    </p>
                </div>
            </div>
        </div>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
