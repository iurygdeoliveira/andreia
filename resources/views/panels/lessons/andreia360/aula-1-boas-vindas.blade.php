<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 1 &bull; Boas-Vindas e Fundamentos" badge-icon="heroicon-m-sparkles" estimated-time="6 min de leitura" />

    {{-- Bento Hero --}}
    <x-lesson::bento-card>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#D4A843]/10 border border-[#D4A843]/30 text-[#D4A843] text-xs font-bold uppercase tracking-wider font-heading">
                <x-filament::icon icon="heroicon-m-sparkles" class="h-4 w-4" />
                Módulo 1 &bull; Boas-Vindas
            </div>
            <span class="text-xs text-gray-400 font-medium">Tempo estimado: 6 min</span>
        </div>

        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Seja Muito Bem-Vinda ao <span class="text-[#D4A843]">Método Andreia 360</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                Parabéns pelo primeiro e mais importante passo: escolher cuidar de você com respeito, acolhimento e respaldo técnico para o seu momento de vida.
            </p>
        </div>

        {{-- Image Showcase --}}
        <div class="relative w-full rounded-2xl border border-[#5881a6]/30 bg-[#010d26] overflow-hidden group shadow-2xl">
            <img src="{{ asset('images/andreia360/andreia-hero.jpg') }}" 
                 alt="Andreia Castro" 
                 class="w-full h-72 sm:h-96 object-cover object-top transform group-hover:scale-105 transition-transform duration-700 opacity-95 group-hover:opacity-100" />
            <div class="absolute inset-0 bg-gradient-to-t from-[#010d26] via-transparent to-transparent opacity-80 pointer-events-none"></div>
            <div class="absolute bottom-4 left-6 right-6 flex items-center justify-between text-xs text-gray-300">
                <span class="flex items-center gap-2 font-medium">
                    <span class="w-2 h-2 rounded-full bg-[#D4A843] animate-pulse"></span>
                    Andreia Castro — Personal Trainer & Mentora de Transformação Feminina 40+
                </span>
                <span class="hidden sm:inline-block px-2.5 py-1 rounded bg-[#021f3b] border border-[#5881a6]/40 text-accent-gold font-mono text-[10px]">Acesso Aluna</span>
            </div>
        </div>

        {{-- Os 4 Pilares --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <x-lesson::metric-stat 
                value="4 Pilares" 
                label="Estrutura Integrada" 
                description="Treino, Nutrição Hormonal, Mentalidade e Suporte semanal em perfeita sinergia." 
                icon="heroicon-m-squares-2x2" 
                color="gold" />

            <x-lesson::metric-stat 
                value="100% Real" 
                label="Sem Dietas Extremas" 
                description="Adaptado à rotina de mães, trabalhadoras e mulheres com vidas reais e atarefadas." 
                icon="heroicon-m-heart" 
                color="brand" />
        </div>

        <div class="prose prose-invert max-w-none space-y-6 text-gray-300 leading-relaxed">
            <h3 class="text-2xl font-bold text-white font-heading">Por que o Método 360 é Diferente de Tudo que Você já Fez?</h3>
            <p>
                A maioria dos programas tradicionais de emagrecimento foi elaborada pensando em mulheres de 20 anos ou homens jovens. Eles impõem horas na esteira, cortes radicais de carboidratos e treinos exaustivos que elevam o <strong>cortisol</strong> (o hormônio do estresse). Em mulheres maduras, isso trava o metabolismo e acumula gordura na região abdominal.
            </p>
            <p>
                No Método Andreia 360, nós não lutamos contra os seus hormônios; nós ajustamos os estímulos para que o seu corpo volte a responder naturalmente, promovendo:
            </p>
            <ul class="list-disc pl-6 space-y-2 text-gray-300">
                <li><strong class="text-white">Reativação Metabólica:</strong> treinos curtos de força que estimulam a massa muscular e queimam gordura mesmo em repouso.</li>
                <li><strong class="text-white">Nutrição Hormonal Inteligente:</strong> refeições saborosas, sem passar fome e sem cortar grupos alimentares essenciais.</li>
                <li><strong class="text-white">Equilíbrio do Sono e Estresse:</strong> estratégias práticas para baixar a ansiedade e controlar o apetite por doces.</li>
                <li><strong class="text-white">Comunidade e Acolhimento:</strong> você não estará sozinha nessa caminhada.</li>
            </ul>
        </div>

        <x-lesson::callout title="Compromisso Consigo Mesma">
            Não busque perfeição nos primeiros 7 dias; busque consistência. Cada refeição saudável e cada treino concluído é uma vitória sobre o cansaço e a rotina.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
