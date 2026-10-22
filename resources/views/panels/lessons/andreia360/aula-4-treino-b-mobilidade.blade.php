<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 2 &bull; Treinos Estratégicos" badge-icon="heroicon-m-heart" estimated-time="8 min de prática" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Treino B: <span class="text-[#D4A843]">Mobilidade, Postura e Core</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                Destrave a coluna, aumente a flexibilidade articular e alivie a sensação de peso nas pernas e cansaço diário com movimentos suaves e altamente eficazes.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <x-lesson::metric-stat 
                value="Zero Impacto" 
                label="Segurança Articular" 
                description="Exercícios pensados para quem tem dores nos joelhos, hérnias discais ou fibromialgia." 
                icon="heroicon-m-shield-check" 
                color="gold" />

            <x-lesson::metric-stat 
                value="15 Min" 
                label="Prática Diária" 
                description="Pode ser feito logo ao acordar ou antes de dormir para preparar o corpo para o descanso." 
                icon="heroicon-m-sun" 
                color="brand" />
        </div>

        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-white font-heading">Rotina de Descompressão e Ativação</h3>
            <ul class="space-y-4 text-gray-300 leading-relaxed">
                <li class="p-4 rounded-xl bg-[#021f3b] border border-[#5881a6]/20">
                    <strong class="text-white block mb-1">1. Gato e Vaca (Mobilidade Torácica e Lombar):</strong>
                    Realize 10 respirações profundas em quatro apoios, arqueando a coluna suavemente para cima e para baixo.
                </li>
                <li class="p-4 rounded-xl bg-[#021f3b] border border-[#5881a6]/20">
                    <strong class="text-white block mb-1">2. Rotação de Quadril 90/90:</strong>
                    Sente-se no chão com as pernas em ângulo de 90 graus para liberar a tensão no quadril e melhorar a circulação inferior.
                </li>
                <li class="p-4 rounded-xl bg-[#021f3b] border border-[#5881a6]/20">
                    <strong class="text-white block mb-1">3. Vácuo Abdominal e Respiração Diafragmática:</strong>
                    Técnica respiratória que reposiciona os órgãos internos, ativa o transverso profundo e afina a cintura.
                </li>
            </ul>
        </div>

        <x-lesson::callout title="Benefício Imediato">
            A prática de mobilidade estimula o líquido sinovial nas articulações e reduz a rigidez matinal típica das oscilações hormonais da menopausa.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
