<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 3 &bull; Nutrição Inteligente" badge-icon="heroicon-m-book-open" estimated-time="12 min de leitura e receitas" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                E-book Saladas Fit 40+: <span class="text-[#D4A843]">Receitas Práticas e Refrescantes</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                Descubra combinações ricas em fitoestrógenos, proteínas e molhos anti-inflamatórios para transformar suas refeições em momentos de puro prazer.
            </p>
        </div>

        {{-- Galeria de Saladas com Imagens Oficiais --}}
        <div class="grid sm:grid-cols-2 gap-6">
            {{-- Salada 1 --}}
            <div class="rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 overflow-hidden shadow-xl flex flex-col">
                <img src="{{ asset('images/andreia360/salada_andreia_premium.png') }}" alt="Salada Premium Andreia" class="w-full h-48 object-cover">
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <span class="text-xs text-[#D4A843] font-bold uppercase tracking-wider">Assinatura do Método</span>
                        <h4 class="text-white font-bold text-lg font-heading">Salada Premium Andreia 360</h4>
                        <p class="text-xs text-gray-300 mt-1">Mix de folhas escuras, lascas de salmão grelhado, nozes crocantes e sementes de gergelim tostado com azeite extravirgem e limão siciliano.</p>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-white/10">
                        <span>320 kcal</span>
                        <span class="text-accent-gold font-bold">28g Proteína</span>
                    </div>
                </div>
            </div>

            {{-- Salada 2 --}}
            <div class="rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 overflow-hidden shadow-xl flex flex-col">
                <img src="{{ asset('images/andreia360/salada_power_frango_abacate.png') }}" alt="Salada Frango e Abacate" class="w-full h-48 object-cover">
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <span class="text-xs text-[#D4A843] font-bold uppercase tracking-wider">Alta Saciedade</span>
                        <h4 class="text-white font-bold text-lg font-heading">Salada Power Frango & Abacate</h4>
                        <p class="text-xs text-gray-300 mt-1">Peito de frango desfiado temperado com cúrcuma, cubos de abacate maduro, rúcula baby e tomates cereja com vinagrete balsâmico.</p>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-white/10">
                        <span>360 kcal</span>
                        <span class="text-accent-gold font-bold">34g Proteína</span>
                    </div>
                </div>
            </div>

            {{-- Salada 3 --}}
            <div class="rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 overflow-hidden shadow-xl flex flex-col">
                <img src="{{ asset('images/andreia360/salada_detox_atum.png') }}" alt="Salada Detox Atum" class="w-full h-48 object-cover">
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <span class="text-xs text-[#D4A843] font-bold uppercase tracking-wider">Prática em 5 Minutos</span>
                        <h4 class="text-white font-bold text-lg font-heading">Salada Detox de Atum e Grão-de-Bico</h4>
                        <p class="text-xs text-gray-300 mt-1">Atum sólido em azeite, grão-de-bico cozido, pepino japonês em rodelas, salsinha fresca e cebola roxa marinada no azeite com orégano.</p>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-white/10">
                        <span>310 kcal</span>
                        <span class="text-accent-gold font-bold">30g Proteína</span>
                    </div>
                </div>
            </div>

            {{-- Salada 4 --}}
            <div class="rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 overflow-hidden shadow-xl flex flex-col">
                <img src="{{ asset('images/andreia360/salada_tropical_proteica.png') }}" alt="Salada Tropical Proteica" class="w-full h-48 object-cover">
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <span class="text-xs text-[#D4A843] font-bold uppercase tracking-wider">Refrescante & Digestiva</span>
                        <h4 class="text-white font-bold text-lg font-heading">Salada Tropical com Queijo Cottage</h4>
                        <p class="text-xs text-gray-300 mt-1">Alface romana, cubos de manga ou morango fresco, queijo cottage cremoso e lascas de amêndoas com molho de mostarda dijon e melado.</p>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-white/10">
                        <span>280 kcal</span>
                        <span class="text-accent-gold font-bold">22g Proteína</span>
                    </div>
                </div>
            </div>
        </div>

        <x-lesson::callout title="Molho Anti-Inflamatório Curinga">
            Misture 2 colheres de sopa de azeite extravirgem, 1 colher de sobremesa de suco de limão, 1 pitada de cúrcuma em pó, pimenta-do-reino e uma pitada de sal marinho. Regue sobre qualquer salada para maximizar a absorção de nutrientes.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
