<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 3 &bull; Nutrição Inteligente" badge-icon="heroicon-m-cake" estimated-time="9 min de leitura" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Nutrição Hormonal: <span class="text-[#D4A843]">Comer Bem sem Passar Fome</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                Esqueça dietas de passar fome. O segredo para acelerar o metabolismo aos 40+ é a densidade nutricional: refeições ricas em proteínas, fibras e fitoestrógenos naturais.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <x-lesson::metric-stat 
                value="1.4g a 1.8g" 
                label="Proteína por kg/dia" 
                description="Garante saciedade prolongada, preserva a musculatura e reduz picos de compulsão por doces à tarde." 
                icon="heroicon-m-fire" 
                color="gold" />

            <x-lesson::metric-stat 
                value="30g / Dia" 
                label="Fibras Alimentares" 
                description="Essenciais para a eliminação do excesso de estrogênio conjugado pelo intestino." 
                icon="heroicon-m-check-circle" 
                color="brand" />
        </div>

        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-white font-heading">A Regra do Prato Perfeito 360</h3>
            <div class="p-6 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-4">
                <div class="grid sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-[#010d26]/80 border border-white/5 text-center">
                        <span class="text-2xl block mb-1">🥗 50%</span>
                        <strong class="text-white block text-sm">Vegetais e Folhas</strong>
                        <p class="text-xs text-gray-400 mt-1">Fibras, compostos fenólicos e micronutrientes anti-inflamatórios.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#010d26]/80 border border-white/5 text-center">
                        <span class="text-2xl block mb-1">🍗 25%</span>
                        <strong class="text-white block text-sm">Proteínas de Alto Valor</strong>
                        <p class="text-xs text-gray-400 mt-1">Frango, ovos, peixes, atum, carne magra, queijo cottage.</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#010d26]/80 border border-white/5 text-center">
                        <span class="text-2xl block mb-1">🥑 25%</span>
                        <strong class="text-white block text-sm">Gorduras Boas & Carboidratos</strong>
                        <p class="text-xs text-gray-400 mt-1">Azeite de oliva, abacate, sementes, batata doce, arroz integral.</p>
                    </div>
                </div>
            </div>
        </div>

        <x-lesson::callout title="Dica de Ouro da Andreia">
            Comece sempre suas refeições pelos vegetais e pela fonte de proteína antes de tocar nos carboidratos. Essa simples ordem reduz a velocidade de absorção da glicose em até 40%.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
