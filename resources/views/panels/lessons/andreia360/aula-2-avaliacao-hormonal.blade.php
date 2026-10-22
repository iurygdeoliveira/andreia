<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 1 &bull; Boas-Vindas e Fundamentos" badge-icon="heroicon-m-chart-bar" estimated-time="7 min de leitura" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Avaliação e o <span class="text-[#D4A843]">Mapa Hormonal 40+</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                Compreenda com clareza o que acontece com seu metabolismo a partir dos 40 anos e como destravar a queima de gordura visceral.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <x-lesson::metric-stat 
                value="-1% a 2%" 
                label="Perda Muscular Anual" 
                description="Sem treino de força adequado, a sarcopenia reduz a queima calórica diária em repouso." 
                icon="heroicon-m-arrow-trending-down" 
                color="rose" />

            <x-lesson::metric-stat 
                value="20 a 30 Min" 
                label="Duração Ideal do Treino" 
                description="Estímulos curtos e intensos evitam o pico prejudicial de cortisol e preservam as articulações." 
                icon="heroicon-m-clock" 
                color="gold" />
        </div>

        <div class="prose prose-invert max-w-none space-y-6 text-gray-300 leading-relaxed">
            <h3 class="text-2xl font-bold text-white font-heading">A Tríade Hormonal da Mulher Madura</h3>
            <p>
                A partir da perimenopausa, três hormônios sofrem alterações significativas no organismo feminino:
            </p>
            <div class="grid sm:grid-cols-3 gap-4 not-prose my-6">
                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30">
                    <h4 class="text-[#D4A843] font-bold text-base mb-2 font-heading">1. Estrogênio</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">Sua redução redistribui a gordura corporal dos quadris para a região abdominal e diminui a sensibilidade à insulina.</p>
                </div>
                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30">
                    <h4 class="text-[#D4A843] font-bold text-base mb-2 font-heading">2. Progesterona</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">A queda inicial da progesterona aumenta a retenção de líquidos, inchaço e impacta diretamente a profundidade do sono.</p>
                </div>
                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30">
                    <h4 class="text-[#D4A843] font-bold text-base mb-2 font-heading">3. Cortisol</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">Em alta decorrente de estresse, dietas restritivas e sono ruim, ele sinaliza ao corpo para reter calorias e degradar músculos.</p>
                </div>
            </div>
            <p>
                Ao invés de tentar "enganar" seu corpo com remédios ou jejuns extremos, nós alinhamos os nutrientes certos na hora certa e o tipo certo de exercício para enviar a mensagem biológica de segurança e vitalidade.
            </p>
        </div>

        <x-lesson::callout title="Ação Prática desta Aula">
            Acesse o formulário de anamnese disponibilizado no grupo de alunas e registre suas medidas de cintura, quadril e fotos de acompanhamento. Isso servirá de marco zero para a sua evolução.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
