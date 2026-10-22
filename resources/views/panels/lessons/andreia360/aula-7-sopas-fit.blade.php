<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 3 &bull; Nutrição Inteligente" badge-icon="heroicon-m-sparkles" estimated-time="8 min de leitura e receitas" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Sopas Fit e Cremes Detox: <span class="text-[#D4A843]">O Jantar Perfeito</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                À noite, o organismo precisa de leveza para induzir o sono profundo e permitir que o hormônio do crescimento (GH) atue na queima de gordura e regeneração tecidual.
            </p>
        </div>

        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-white font-heading">Receitas Selecionadas para o Jantar</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-2">
                    <h4 class="text-white font-bold text-lg font-heading">1. Creme Dourado de Abóbora com Gengibre</h4>
                    <p class="text-xs text-gray-300">Cozinhe abóbora cabotiá com gengibre ralado e caldo de frango caseiro. Bata no liquidificador com 1 colher de azeite e finalize com frango desfiado e sementes de abóbora.</p>
                    <span class="inline-block text-xs text-accent-gold font-bold">Digestão rápida & Termogênica</span>
                </div>

                <div class="p-5 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-2">
                    <h4 class="text-white font-bold text-lg font-heading">2. Sopa Verde Detox com Couve e Alho-Poró</h4>
                    <p class="text-xs text-gray-300">Base de chuchu, abobrinha, couve manteiga picadinha e alho-poró refogado no azeite com peito de frango em cubos. Altíssimo teor de magnésio e potássio.</p>
                    <span class="inline-block text-xs text-accent-gold font-bold">Desinchaço & Relaxamento Muscular</span>
                </div>
            </div>
        </div>

        <x-lesson::callout title="Regra do Horário do Jantar">
            Tente jantar pelo menos 2 a 3 horas antes de se deitar. Isso evita o refluxo gástrico e permite que a insulina caia para níveis basais antes do início do sono.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
