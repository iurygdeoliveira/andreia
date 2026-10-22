{{-- Faixa de autoridade ao lado do formulario.
     Pilar 6: o trust signal precisa estar no mesmo campo visual do CTA. Na home
     o bloco do professor vem depois do catalogo, ou seja, o usuario decide
     comprar antes de saber de quem esta comprando. --}}
<div class="mt-6 bg-dark-surface border border-dark-border rounded-2xl p-6">
    <div class="flex items-center gap-4 mb-4">
        <img src="{{ asset('images/professor-celso.webp') }}"
            alt="Retrato do Professor Celso"
            width="64" height="64" loading="lazy" decoding="async"
            class="w-16 h-16 rounded-full object-cover object-top border border-dark-border shrink-0">
        <div>
            <p class="text-white font-bold">Professor Celso</p>
            <p class="text-sm text-gray-400">CREF ativo &middot; 40 anos de mercado</p>
        </div>
    </div>
    <p class="text-sm text-gray-400 italic border-l-2 border-brand pl-3">
        "Eu sei exatamente o que funciona no mercado, porque eu vivo isso há 4 décadas.
        Meu objetivo é encurtar o seu caminho."
    </p>
    <dl class="grid grid-cols-2 gap-4 mt-5 pt-5 border-t border-dark-border">
        <div>
            <dt class="text-xs text-gray-400 uppercase tracking-wide">Alunos formados</dt>
            <dd class="text-xl font-extrabold text-white">+8.000</dd>
        </div>
        <div>
            <dt class="text-xs text-gray-400 uppercase tracking-wide">Cursos ministrados</dt>
            <dd class="text-xl font-extrabold text-white">+200</dd>
        </div>
    </dl>
</div>
