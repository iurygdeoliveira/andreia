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
                Força de Cisalhamento e <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Ativação Simpático-Adrenal</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                O aumento pulsátil do fluxo sanguíneo exerce um atrito mecânico protetor sobre o endotélio vascular (Shear Stress), enquanto o eixo neuro-adrenal ajusta a força miocárdica e o tônus vascular sistêmico.
            </p>
        </div>

        {{-- Shear Stress Glowing Card --}}
        <div class="bg-gradient-to-br from-[#10B981]/15 via-[#0d1a15] to-[#050a08] border border-[#10B981]/40 rounded-3xl p-8 shadow-2xl space-y-4 relative overflow-hidden">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-[#10B981]/20 border border-[#10B981]/40 flex items-center justify-center text-[#34D399]">
                    <x-filament::icon icon="heroicon-m-sparkles" class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Mecanismo do Shear Stress e Óxido Nítrico (NO)</h3>
                    <span class="text-xs text-[#34D399] font-medium">Transdução de sinal mecânico em proteção vascular</span>
                </div>
            </div>

            <p class="text-sm text-gray-300 leading-relaxed">
                A força de cisalhamento atua sobre a camada íntima endotelial, estimulando a síntese e liberação local de <strong class="text-white font-bold">Óxido Nítrico (NO)</strong>, potente agente vasodilatador que induz relaxamento do músculo liso arteriolar e inibe a agregação plaquetária.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-xl bg-[#050a08]/80 border border-[#1b3329] text-xs space-y-1">
                    <span class="text-[#10B981] font-bold block">&bull; Efeito Imediato:</span>
                    <p class="text-gray-300">Queda da resistência vascular periférica e otimização do fluxo sanguíneo nos grupamentos musculares ativos.</p>
                </div>
                <div class="p-4 rounded-xl bg-[#050a08]/80 border border-[#1b3329] text-xs space-y-1">
                    <span class="text-[#10B981] font-bold block">&bull; Efeito Crônico:</span>
                    <p class="text-gray-300">Proteção endotelial a longo prazo, remodelamento arteriolar e prevenção ativa de aterosclerose e hipertensão.</p>
                </div>
            </div>
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Comando Central e Ação Cardioestimulante --}}
    <x-lesson::bento-card>
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-cpu-chip" class="h-5 w-5 text-[#10B981]" />
            Integração Cortical e Medula Adrenal
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-6 space-y-4">
                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-2">
                    <span class="text-xs font-bold text-[#34D399] uppercase">Córtex Motor (Áreas 4 e 6)</span>
                    <h4 class="font-bold text-white text-base">Comando Central e Vasodilatação</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        A intenção e execução do ato motor disparam impulsos simpáticos descendentes que provocam vasodilatação muscular e redução da resistência vascular local.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-2">
                    <span class="text-xs font-bold text-amber-400 uppercase">Inotropismo Positivo</span>
                    <h4 class="font-bold text-white text-base">Influxo de Cálcio ($Ca^{2+}$) na Fase 2</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        As fibras simpáticas cardioestimulantes aumentam a permeabilidade ao cálcio durante o potencial de ação miocárdico, potencializando o volume cardíaco por minuto.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-2">
                    <span class="text-xs font-bold text-purple-400 uppercase">Catecolaminas Circulantes</span>
                    <h4 class="font-bold text-white text-base">Adrenalina e Noradrenalina</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        A medula da glândula adrenal secreta adrenalina, noradrenalina e dopamina, mantendo a pressão arterial diferencial e sustentando o fluxo coronariano.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-6 space-y-4">
                <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-4 group overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/cursos/fisiologia-exercicio/image27.png') }}" 
                         alt="Comparação dos Efeitos Simpáticos" 
                         class="w-full h-auto object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                    <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                        Figura 4.1 — Redistribuição do fluxo sanguíneo tecidual no exercício.
                    </div>
                </div>

                <div class="rounded-2xl border border-[#1b3329] bg-[#050a08] p-3 group overflow-hidden shadow-xl">
                    <img src="{{ asset('images/cursos/fisiologia-exercicio/image28.jfif') }}" 
                         alt="Ativação Neurocardíaca" 
                         class="w-full h-36 object-contain mx-auto" />
                </div>
            </div>
        </div>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
