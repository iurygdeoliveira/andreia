<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 4 &bull; Mentalidade e Hábitos" badge-icon="heroicon-m-moon" estimated-time="7 min de leitura" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Sono Reparador &bull; <span class="text-[#D4A843]">O Freio do Cortisol</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                Mulheres que dormem mal produzem mais grelina (hormônio da fome) e menos leptina (hormônio da saciedade). Aprenda a preparar seu descanso biológico.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <x-lesson::metric-stat 
                value="+25%" 
                label="Fome no Dia Seguinte" 
                description="Uma noite mal dormida aumenta a atração biológica por carboidratos refinados e doces." 
                icon="heroicon-m-exclamation-triangle" 
                color="rose" />

            <x-lesson::metric-stat 
                value="7h a 8h" 
                label="Sono de Qualidade" 
                description="Janela em que ocorre a síntese de melatonina e a reciclagem hormonal noturna." 
                icon="heroicon-m-moon" 
                color="gold" />
        </div>

        <div class="space-y-6">
            <h3 class="text-2xl font-bold text-white font-heading">Protocolo Noturno de Higiene do Sono</h3>
            <ul class="space-y-3 text-gray-300">
                <li class="flex items-start gap-3">
                    <span class="text-[#D4A843] font-bold">1.</span>
                    <span><strong>Desconexão de Telas:</strong> reduza o uso de celular e computadores pelo menos 45 minutos antes de dormir para não bloquear a produção de melatonina.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-[#D4A843] font-bold">2.</span>
                    <span><strong>Chás Calmantes:</strong> camomila, mulungu, melissa ou passiflora após as 20h auxiliam na diminuição dos batimentos cardíacos.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-[#D4A843] font-bold">3.</span>
                    <span><strong>Quarto Escuro e Fresco:</strong> o corpo diminui a temperatura central para entrar nas fases profundas do sono (REM e sono de ondas lentas).</span>
                </li>
            </ul>
        </div>

        <x-lesson::callout title="Lembrete da Andreia">
            O emagrecimento não acontece durante o treino; o treino é o estímulo. O emagrecimento e a regeneração muscular acontecem enquanto você dorme.
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" />
</div>
</x-filament-panels::page>
