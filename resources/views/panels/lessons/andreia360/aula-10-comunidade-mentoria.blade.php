<x-filament-panels::page>
<div class="space-y-10 max-w-5xl mx-auto text-gray-100">
    <x-lesson::header :lesson="$lesson" badge="Módulo 4 &bull; Mentalidade e Hábitos" badge-icon="heroicon-m-user-group" estimated-time="5 min de leitura" />

    <x-lesson::bento-card>
        <div class="space-y-4 max-w-3xl">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                Comunidade VIP &bull; <span class="text-[#D4A843]">Nunca Mais Caminhar Sozinha</span>
            </h2>
            <p class="text-base sm:text-lg text-gray-300 leading-relaxed font-light">
                O ambiente molda seus resultados. No grupo exclusivo de alunas, trocamos experiências, receitas, fotos de pratos e celebramos cada quilo eliminado.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <x-lesson::metric-stat 
                value="Quinzenal" 
                label="Mentorias ao Vivo" 
                description="Tire suas dúvidas direto comigo pelo Zoom e receba ajustes pontuais para sua rotina." 
                icon="heroicon-m-video-camera" 
                color="gold" />

            <x-lesson::metric-stat 
                value="Suporte VIP" 
                label="Comunidade WhatsApp" 
                description="Canal diário para dúvidas rápidas, incentivo e celebração de vitórias." 
                icon="heroicon-m-chat-bubble-left-right" 
                color="brand" />
        </div>

        <div class="p-6 rounded-2xl bg-[#021f3b] border border-[#5881a6]/30 space-y-4 text-center sm:text-left">
            <h3 class="text-xl font-bold text-white font-heading">Acesso aos Canais Oficiais</h3>
            <p class="text-sm text-gray-300">
                Se você ainda não entrou no nosso grupo de WhatsApp das alunas, clique no botão abaixo para ingressar diretamente:
            </p>
            <a href="https://wa.me/55XX9XXXXXXX" target="_blank" 
               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-[#25D366] text-brand-navy font-bold hover:bg-[#20bd5a] transition shadow-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Entrar no Grupo VIP de Alunas
            </a>
        </div>

        <x-lesson::callout title="Mensagem Final da Andreia">
            Sua nova história começa agora. Confie no método, execute um dia de cada vez e sinta o orgulho de se olhar no espelho com alegria e energia renovada!
        </x-lesson::callout>
    </x-lesson::bento-card>

    <x-lesson::footer :lesson="$lesson" title="Parabéns! Módulos Concluídos" subtitle="Você tem todo o direcionamento para conquistar sua transformação." />
</div>
</x-filament-panels::page>
