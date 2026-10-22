<x-filament-panels::page>
@php
    $module = $lesson->module;
    $course = $module?->course;
    $allLessons = $course ? $course->modules()->orderBy('sort')->with(['lessons' => fn($q) => $q->orderBy('sort')])->get()->flatMap->lessons : collect([$lesson]);
    $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
    $prevLesson = $currentIndex > 0 ? $allLessons->get($currentIndex - 1) : null;
    $nextLesson = ($currentIndex !== false && $currentIndex < $allLessons->count() - 1) ? $allLessons->get($currentIndex + 1) : null;
    $tenant = filament()->getTenant();
    $tenantSlug = $tenant?->slug ?? 'eafs';
    $panelId = filament()->getCurrentPanel()?->getId() ?? 'student';
    $totalLessons = $allLessons->count() ?: 1;
    $progressPercent = round((($currentIndex !== false ? $currentIndex + 1 : 1) / $totalLessons) * 100);

    $backToCourseUrl = match ($panelId) {
        'user' => $course ? url('/user/' . $tenantSlug . '/course/courses/' . $course->checkout_code) : '#',
        default => $course ? url('/student/' . $tenantSlug . '/course/student-courses/' . $course->checkout_code) : '#',
    };
@endphp

<div class="space-y-8 max-w-5xl mx-auto text-gray-100">
    {{-- Header & Breadcrumbs --}}
    <div class="rounded-2xl border border-[#1b3329] bg-[#0d1a15] p-6 shadow-xl relative overflow-hidden">
        {{-- Background Glow --}}
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ $backToCourseUrl }}" 
                       class="inline-flex items-center gap-1.5 rounded-md bg-[#13241d] px-2.5 py-1 text-xs font-semibold text-emerald-400 border border-[#1b3329] hover:border-emerald-500/40 hover:text-emerald-300 transition">
                        <x-filament::icon icon="heroicon-m-arrow-left" class="h-3.5 w-3.5" />
                        {{ $course->title ?? 'Curso' }}
                    </a>
                    <span class="text-xs text-gray-400">/</span>
                    <span class="inline-flex items-center gap-1 rounded-md bg-[#13241d] px-2.5 py-1 text-xs font-medium text-gray-300 border border-[#1b3329]">
                        {{ $module->title ?? 'Módulo' }}
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-400 border border-emerald-500/20">
                        Aula {{ ($currentIndex !== false ? $currentIndex + 1 : 1) }} de {{ $totalLessons }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    {{ $lesson->title }}
                </h1>
            </div>

            {{-- Navigation Buttons --}}
            <div class="flex items-center gap-2 shrink-0">
                @if ($prevLesson)
                    <a href="{{ url('/' . $panelId . '/' . $tenantSlug . '/lesson/' . $prevLesson->id) }}" 
                       class="inline-flex items-center gap-1.5 rounded-xl border border-[#1b3329] bg-[#13241d] px-3.5 py-2 text-xs font-semibold text-gray-200 hover:border-emerald-500/50 hover:text-emerald-400 transition shadow-sm">
                        <x-filament::icon icon="heroicon-m-chevron-left" class="h-4 w-4" />
                        Aula Anterior
                    </a>
                @endif
                @if ($nextLesson)
                    <a href="{{ url('/' . $panelId . '/' . $tenantSlug . '/lesson/' . $nextLesson->id) }}" 
                       class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 px-4 py-2 text-xs font-bold text-[#050a08] hover:bg-emerald-400 transition shadow-lg shadow-emerald-500/20">
                        Próxima Aula
                        <x-filament::icon icon="heroicon-m-chevron-right" class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ $backToCourseUrl }}" 
                       class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 px-4 py-2 text-xs font-bold text-[#050a08] hover:bg-emerald-400 transition shadow-lg shadow-emerald-500/20">
                        Concluir Curso
                        <x-filament::icon icon="heroicon-m-check-badge" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-5 space-y-1.5">
            <div class="flex justify-between text-[11px] font-medium text-gray-400">
                <span>Progresso no Curso</span>
                <span class="text-emerald-400 font-semibold">{{ $progressPercent }}%</span>
            </div>
            <div class="h-1.5 w-full bg-[#13241d] rounded-full overflow-hidden border border-[#1b3329]">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500 rounded-full" 
                     style="width: {{ $progressPercent }}%;"></div>
            </div>
        </div>
    </div>

    {{-- Main Lesson Content Slot --}}
    <article class="space-y-8">
        {{ $slot }}
    </article>

    {{-- Footer Navigation Card --}}
    <div class="rounded-2xl border border-[#1b3329] bg-[#0d1a15] p-6 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-white">Finalizou o estudo desta aula?</p>
            <p class="text-xs text-gray-400">Revise os conceitos-chave ou prossiga para a próxima lição.</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            @if ($prevLesson)
                <a href="{{ url('/student/' . $tenantSlug . '/lesson/' . $prevLesson->id) }}" 
                   class="inline-flex items-center gap-1.5 rounded-xl border border-[#1b3329] bg-[#13241d] px-4 py-2.5 text-xs font-semibold text-gray-300 hover:border-emerald-500/40 hover:text-white transition">
                    <x-filament::icon icon="heroicon-m-chevron-left" class="h-4 w-4" />
                    Aula Anterior
                </a>
            @endif

            @if ($nextLesson)
                <a href="{{ url('/student/' . $tenantSlug . '/lesson/' . $nextLesson->id) }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-xs font-bold text-[#050a08] hover:bg-emerald-400 transition shadow-lg shadow-emerald-500/20">
                    Avançar para Próxima Aula
                    <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" />
                </a>
            @else
                <a href="{{ $course ? url('/student/' . $tenantSlug . '/course/' . $course->id) : '#' }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-xs font-bold text-[#050a08] hover:bg-emerald-400 transition shadow-lg shadow-emerald-500/20">
                    <x-filament::icon icon="heroicon-m-academic-cap" class="h-4 w-4" />
                    Retornar à Visão Geral do Curso
                </a>
            @endif
        </div>
    </div>
</div>
</x-filament-panels::page>
