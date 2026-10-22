<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 3 &bull; Ajustes Respiratórios e Termorregulação" badge-icon="heroicon-m-sparkles" estimated-time="8 min de leitura" />

    {{-- Main Lesson Section: Bento Hero --}}
    <x-lesson::bento-card>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#10B981]/10 border border-[#10B981]/30 text-[#34D399] text-xs font-bold uppercase tracking-wider">
                <x-filament::icon icon="heroicon-m-sparkles" class="h-4 w-4" />
                Módulo 3 &bull; Ajustes Respiratórios e Termorregulação
            </div>
            <span class="text-xs text-gray-400 font-medium">Tempo estimado: 8 min de leitura</span>
        </div>

        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                Ajuste Respiratório e <span class="bg-gradient-to-r from-[#34D399] to-[#10B981] bg-clip-text text-transparent">Trocas Gasosas</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-normal">
                O aumento exponencial da ventilação minuto durante o esforço físico garante oxigenação ótima da hemoglobina e eliminação ágil de dióxido de carbono ($CO_2$) gerado pelo metabolismo celular ativo.
            </p>
        </div>

        {{-- 2 Core Respiratory Pillars --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <div class="bg-[#050a08]/80 border border-[#1b3329] p-7 rounded-2xl shadow-xl space-y-3 hover:border-[#10B981]/40 transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#10B981]/15 border border-[#10B981]/30 flex items-center justify-center text-[#10B981]">
                        <x-filament::icon icon="heroicon-m-sparkles" class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-white">Broncodilatação Simpática</h3>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed">
                    O sistema simpático-adrenal e as catecolaminas circulantes relaxam a musculatura lisa bronquiolar (via receptores &beta;₂-adrenérgicos), reduzindo o trabalho elástico ventilatório e permitindo fluxos aéreos de até 150 L/min.
                </p>
            </div>

            <div class="bg-[#050a08]/80 border border-[#1b3329] p-7 rounded-2xl shadow-xl space-y-3 hover:border-teal-500/40 transition">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-300">
                        <x-filament::icon icon="heroicon-m-cpu-chip" class="h-6 w-6" />
                    </div>
                    <h3 class="text-lg font-bold text-white">Interação Bulbar Cardio-Respiratória</h3>
                </div>
                <p class="text-xs text-gray-300 leading-relaxed">
                    Os neurônios do centro respiratório reticular bulbar estão anatomicamente entrelaçados com a área vasomotora pressora, integrando a frequência ventilatória e o débito cardíaco instantâneo.
                </p>
            </div>
        </div>
    </x-lesson::bento-card>

    {{-- Bento Section 2: Alvéolo e Bomba Respiratória --}}
    <x-lesson::bento-card>
        <h3 class="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-arrows-pointing-out" class="h-5 w-5 text-[#10B981]" />
            Acoplamento Alveolar e Bomba Torácica
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <div class="md:col-span-5 rounded-2xl border border-[#1b3329] bg-[#050a08] p-5 group overflow-hidden shadow-2xl">
                <img src="{{ asset('images/cursos/fisiologia-exercicio/image31.jfif') }}" 
                     alt="Acoplamento Alveolar" 
                     class="w-full h-auto object-contain mx-auto transform group-hover:scale-105 transition-transform duration-500" />
                <div class="pt-3 border-t border-[#1b3329] text-xs text-gray-400 text-center font-medium">
                    Esquema 6.1 — Difusão alvéolo-capilar e centros respiratórios bulbares.
                </div>
            </div>

            <div class="md:col-span-7 space-y-4">
                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-2">
                    <span class="text-xs font-bold text-[#10B981] uppercase">Difusão de Gases</span>
                    <h4 class="font-bold text-white text-base">Saturação e Transporte de O₂</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        A circulação acelerada pelo aumento do débito cardíaco entra em contato com uma superfície de troca alveolar expandida, mantendo a saturação de oxi-hemoglobina acima de 95% na maioria dos indivíduos sadios.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-[#050a08] border border-[#1b3329] space-y-2">
                    <span class="text-xs font-bold text-teal-400 uppercase">Efeito Hemodinâmico Mecânico</span>
                    <h4 class="font-bold text-white text-base">Mecanismo da Bomba Respiratória</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">
                        A expansão da caixa torácica a cada inspiração vigorosa gera pressão intratorácica negativa, atuando como uma bomba de sucção mecânica que atrai o sangue das veias cavas para dentro das câmaras cardíacas direitas.
                    </p>
                </div>
            </div>
        </div>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
