<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 3 &bull; Nutrição Inteligente" badge-icon="heroicon-m-heart" estimated-time="10 min de leitura e receitas" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Sobremesas Fit: <span class="text-[#D4A843]">Doce Aliado sem Culpa</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                Comer doce não é pecado. Com os ingredientes certos, o doce vira um momento de saciedade proteica e controle da ansiedade no seu processo de emagrecimento.
            </p>
        </div>

        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-white font-heading">3 Receitas Favoritas das Alunas 40+</h3>
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-3">
                    <span class="text-2xl">🍫</span>
                    <h4 class="text-white font-bold text-base font-heading">Mousse Cremoso de Chocolate</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">200g de queijo cottage batido com 2 colheres de cacau 100%, 1 scoop de whey ou proteína vegetal e gotas de estévia. Fica com consistência de mousse aerada de confeitaria!</p>
                    <span class="inline-block text-[11px] text-accent-gold font-bold">18g Proteína &bull; Zero Açúcar</span>
                </div>

                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-3">
                    <span class="text-2xl">🥥</span>
                    <h4 class="text-white font-bold text-base font-heading">Cocada de Forno Cremosa Fit</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">Coco ralado seco sem açúcar, 2 ovos inteiros, leite de coco light e xilitol ou eritritol a gosto. Asse por 25 minutos até dourar a casquinha.</p>
                    <span class="inline-block text-[11px] text-accent-gold font-bold">Gorduras Boas &bull; Baixo Carboidrato</span>
                </div>

                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-3">
                    <span class="text-2xl">🍓</span>
                    <h4 class="text-white font-bold text-base font-heading">Cheesecake Fit de Frutas Vermelhas</h4>
                    <p class="text-xs text-gray-300 leading-relaxed">Base de farinha de aveia com óleo de coco, recheio de ricota fresca com iogurte grego natural e geleia caseira de morango com chia sem açúcar.</p>
                    <span class="inline-block text-[11px] text-accent-gold font-bold">Antioxidantes &bull; Alta Fibra</span>
                </div>
            </div>
        </div>

        <x-lesson::callout title="Por que o Cacau 100% Funciona?">
            O cacau puro é riquíssimo em triptofano e flavonoides, precursores da serotonina. Ele acalma o sistema nervoso central e sacia o paladar doce sem provocar o efeito rebote de querer comer mais.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
