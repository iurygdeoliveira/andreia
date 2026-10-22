<div>
    <!-- Barra de Pesquisa de Cursos (quando há volume suficiente ou busca ativa) -->
    @if($courses->count() >= 8 || filled($search))
        <div class="mb-12 relative w-full">
            <label for="busca-cursos" class="sr-only">Buscar cursos por nome</label>
            <i class="ph-bold ph-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 text-xl" aria-hidden="true"></i>
            <input
                wire:model.live.debounce.300ms="search"
                id="busca-cursos"
                type="search"
                autocomplete="off"
                placeholder="Nome do curso"
                aria-controls="lista-cursos"
                class="w-full bg-dark-surface border border-dark-border rounded-full py-4 pl-14 pr-6 text-white placeholder-gray-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-all shadow-lg text-lg">
        </div>
    @endif

    <div id="lista-cursos" class="mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($courses as $course)
            <x-public::course-card
                :course="$course"
                :addToCartAction="($this->addToCartAction)(['course' => $course])"
                :buyAction="($this->buyAction)(['course' => $course])"
            />
        @empty
            <p class="col-span-full text-center py-12 text-gray-400">
                Nenhum curso disponível no momento.
            </p>
        @endforelse
    </div>

    <x-filament-actions::modals />
</div>
