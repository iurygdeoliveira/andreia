<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-danger-50 border border-danger-200 dark:border-none rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="h-5 w-5 text-danger-600 dark:text-danger-500 text-center font-bold">⚠</div>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-danger-800 dark:text-danger-950">
                        Atenção: Exclusão Permanente
                    </h3>
                    <div class="mt-2 text-sm text-danger-700 dark:text-danger-900">
                        <p>
                            Você está prestes a excluir permanentemente este curso. 
                            Esta ação <strong class="dark:text-danger-400">não pode ser desfeita</strong> e todos os dados 
                            associados ao curso serão removidos completamente do sistema.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informações do Curso --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Informações do Curso
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título: {{ $this->getRecord()->title ?? 'Sem título' }}</label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status: {{ $this->getRecord()->status?->getLabel() ?? 'N/A' }}</label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preço: {{ $this->getRecord()->price_in_cents ? 'R$ ' . number_format($this->getRecord()->price_in_cents / 100, 2, ',', '.') : 'R$ 0,00' }}</label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Criado em: {{ $this->getRecord()->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
