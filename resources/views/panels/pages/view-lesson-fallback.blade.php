<x-filament-panels::page>
    <div class="fi-ta-empty-state px-6 py-12">
        <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
            <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                <x-filament::icon
                    icon="heroicon-o-document-magnifying-glass"
                    class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400"
                />
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $lesson->title }}
            </h3>
            
            <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Conteúdo não encontrado
            </h4>
            
            <p class="fi-ta-empty-state-description mt-1 text-sm text-gray-500 dark:text-gray-400">
                O arquivo blade esperado para esta aula (<code>{{ $lesson->view_path }}</code>) não foi encontrado. <br>
                Por favor, solicite ao desenvolvedor a criação deste arquivo no diretório <code>resources/views</code>.
            </p>
        </div>
    </div>
</x-filament-panels::page>
