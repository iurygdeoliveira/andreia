<x-filament-panels::page>
    @php
        $course = $this->getRecord();
        $modules = $course->modules()->orderBy('sort')->with(['lessons' => fn ($q) => $q->orderBy('sort')])->get();
        $totalLessons = $modules->sum(fn ($module) => $module->lessons->count());
        $tenant = filament()->getTenant();
        $panelId = filament()->getCurrentPanel()?->getId() ?? 'student';
    @endphp

    <div class="space-y-6">
        {{-- Course Header Card --}}
        <div class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex flex-col gap-6 md:flex-row md:items-start">
                @if ($course->hasMedia('cover'))
                    <div class="h-40 w-full shrink-0 overflow-hidden rounded-xl md:w-64">
                        <img 
                            src="{{ $course->getFirstMediaUrl('cover') }}" 
                            alt="{{ $course->title }}"
                            class="h-full w-full object-cover"
                        />
                    </div>
                @endif

                <div class="flex-1 space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($panelId === 'student')
                            <span class="inline-flex items-center gap-1 rounded-md bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-m-academic-cap" class="h-4 w-4" />
                                Curso Matriculado
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-800/40">
                                <x-filament::icon icon="heroicon-m-eye" class="h-4 w-4" />
                                Visão do Aluno (Pré-visualização)
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            {{ $modules->count() }} {{ \Illuminate\Support\Str::plural('Módulo', $modules->count()) }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            {{ $totalLessons }} {{ \Illuminate\Support\Str::plural('Aula', $totalLessons) }}
                        </span>
                    </div>

                    <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                        {{ $course->title }}
                    </h1>

                    @if ($course->description)
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ $course->description }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Curriculum Section --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-queue-list" class="h-5 w-5 text-primary-500" />
                    Grade Curricular
                </h2>
            </div>

            @if ($modules->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                    <x-filament::icon icon="heroicon-o-book-open" class="mx-auto h-8 w-8 text-gray-400" />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Nenhum módulo disponibilizado para este curso ainda.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($modules as $index => $module)
                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            {{-- Module Header --}}
                            <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50/60 px-5 py-3.5 dark:border-gray-800/60 dark:bg-gray-800/40">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary-100 text-xs font-bold text-primary-700 dark:bg-primary-500/20 dark:text-primary-400">
                                        {{ $index + 1 }}
                                    </span>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $module->title }}
                                    </h3>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $module->lessons->count() }} {{ \Illuminate\Support\Str::plural('aula', $module->lessons->count()) }}
                                </span>
                            </div>

                            {{-- Lessons List --}}
                            @if ($module->lessons->isEmpty())
                                <div class="px-5 py-4 text-xs italic text-gray-400">
                                    Nenhuma aula cadastrada neste módulo.
                                </div>
                            @else
                                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach ($module->lessons as $lessonIndex => $lesson)
                                        <li class="flex flex-col gap-3 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                                    {{ $lessonIndex + 1 }}
                                                </span>
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $lesson->title }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2 self-end sm:self-center">
                                                <a 
                                                    href="{{ url('/' . $panelId . '/' . $tenant->slug . '/lesson/' . $lesson->id) }}"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary-50 px-3.5 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-100 dark:bg-primary-500/10 dark:text-primary-400 dark:hover:bg-primary-500/20 transition"
                                                >
                                                    <x-filament::icon icon="heroicon-m-play-circle" class="h-4 w-4" />
                                                    Acessar Aula
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
