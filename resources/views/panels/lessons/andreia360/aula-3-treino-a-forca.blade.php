<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 2 &bull; Treinos Estratégicos" badge-icon="heroicon-m-bolt" estimated-time="10 min de leitura + prática" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Treino A: <span class="text-[#D4A843]">Força e Ativação Metabólica</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                O treino de força para a mulher 40+ prioriza amplitude de movimento, estabilidade articular e cadência controlada para acelerar a queima calórica sem riscos de lesão.
            </p>
        </div>

        <div class="relative w-full rounded-2xl border border-[#5881a6]/30 bg-[#010d26] overflow-hidden group shadow-2xl">
            <img src="{{ asset('images/andreia360/andreia-treino.jpg') }}" 
                 alt="Treino Força Andreia Castro" 
                 class="w-full h-72 sm:h-96 object-cover object-center transform group-hover:scale-105 transition-transform duration-700 opacity-95 group-hover:opacity-100" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#010d26] via-transparent to-transparent opacity-80 pointer-events-none"></div>
            <div class="absolute bottom-4 left-6 right-6 flex items-center justify-between text-xs text-gray-300">
                <span class="flex items-center gap-2 font-medium">
                    <span class="w-2 h-2 rounded-full bg-[#D4A843] animate-pulse"></span>
                    Execução postural correta — biomecânica protegida.
                </span>
                <span class="px-2.5 py-1 rounded bg-[#021f3b] border border-[#5881a6]/40 text-accent-gold font-mono text-[10px]">Treino A</span>
            </div>
        </div>

        {{-- Sequência de Exercícios --}}
        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-white font-heading">Circuito de Força &bull; 3 Séries de 10 a 12 Repetições</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-[#D4A843] font-bold text-sm">Exercício 1</span>
                        <span class="text-xs text-gray-400">3x 12 reps</span>
                    </div>
                    <h4 class="text-white font-bold text-lg">Agachamento Box (na Cadeira)</h4>
                    <p class="text-xs text-gray-300">Fortalecimento de quadríceps e glúteos com proteção total dos joelhos e lombar.</p>
                </div>

                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-[#D4A843] font-bold text-sm">Exercício 2</span>
                        <span class="text-xs text-gray-400">3x 10 reps</span>
                    </div>
                    <h4 class="text-white font-bold text-lg">Remada Curvada com Halteres/Elástico</h4>
                    <p class="text-xs text-gray-300">Melhora postural imediata, abertura peitoral e eliminação de dores nas costas.</p>
                </div>

                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-[#D4A843] font-bold text-sm">Exercício 3</span>
                        <span class="text-xs text-gray-400">3x 12 reps</span>
                    </div>
                    <h4 class="text-white font-bold text-lg">Elevação Pélvica no Solo</h4>
                    <p class="text-xs text-gray-300">Ativação direta da cadeia posterior, glúteos e estabilização pélvica.</p>
                </div>

                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-[#D4A843] font-bold text-sm">Exercício 4</span>
                        <span class="text-xs text-gray-400">3x 30 seg</span>
                    </div>
                    <h4 class="text-white font-bold text-lg">Prancha Isométrica com Apoio Alto</h4>
                    <p class="text-xs text-gray-300">Contração do transverso do abdômen, reduzindo a diástase e a circunferência da cintura.</p>
                </div>
            </div>
        </div>

        <x-lesson::callout title="Dica da Treinadora Andreia">
            Respire profundamente: expire no momento de maior esforço e inspire no retorno. Nunca prenda a respiração (manobra de valsalva) durante as execuções.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
