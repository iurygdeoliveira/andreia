@props([
    'lesson',
    'badge' => null,
    'badgeIcon' => 'heroicon-m-academic-cap',
    'estimatedTime' => '8 min de leitura',
])

@php
    $lesson->loadMissing('module.course.modules.lessons');
    $module = $lesson->module;
    $course = $module?->course;
    $allLessons = $course ? $course->modules()->orderBy('sort')->with(['lessons' => fn($q) => $q->orderBy('sort')])->get()->flatMap->lessons : collect([$lesson]);
    $currentIndex = $allLessons->search(fn($l) => $l->id === $lesson->id);
    $prevLesson = $currentIndex > 0 ? $allLessons->get($currentIndex - 1) : null;
    $nextLesson = ($currentIndex !== false && $currentIndex < $allLessons->count() - 1) ? $allLessons->get($currentIndex + 1) : null;
    $tenant = filament()->getTenant();
    $tenantSlug = $tenant?->slug ?? 'andreia360';
    $panelId = filament()->getCurrentPanel()?->getId() ?? 'student';
    $totalLessons = $allLessons->count() ?: 1;
    $currentLessonNumber = ($currentIndex !== false ? $currentIndex + 1 : 1);
    $progressPercent = round(($currentLessonNumber / $totalLessons) * 100);

    $backToCourseUrl = match ($panelId) {
        'user' => $course ? url('/user/' . $tenantSlug . '/course/courses/' . $course->checkout_code) : '#',
        default => $course ? url('/student/' . $tenantSlug . '/course/student-courses/' . $course->checkout_code) : '#',
    };
@endphp

{{-- Ambient Radial Glow --}}
<div class="relative">
    <div class="absolute -top-16 -right-16 w-96 h-96 bg-[#5881a6]/15 rounded-full blur-[100px] pointer-events-none -z-10"></div>
    <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-[#D4A843]/10 rounded-full blur-[80px] pointer-events-none -z-10"></div>

    {{-- Header Container --}}
    <header class="bg-gradient-to-br from-[#021f3b]/90 via-[#010d26] to-[#000a1c] border border-[#5881a6]/25 rounded-[2rem] p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden space-y-6">
        
        {{-- Top Bar: Navigation Breadcrumb & Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-[#5881a6]/20">
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <a href="{{ $backToCourseUrl }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#023059]/50 text-gray-300 border border-[#5881a6]/30 hover:text-[#D4A843] hover:border-[#D4A843]/40 transition shadow-sm font-medium">
                    <x-filament::icon icon="heroicon-m-arrow-left" class="h-3.5 w-3.5 text-[#D4A843]" />
                    {{ $course->title ?? 'Curso' }}
                </a>
                <span class="text-gray-600 font-bold">&bull;</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#5881a6]/15 border border-[#5881a6]/30 text-[#9cbcd9] font-semibold">
                    <x-filament::icon icon="heroicon-m-bookmark" class="h-3.5 w-3.5 text-[#5881a6]" />
                    {{ $module->title ?? 'Módulo' }}
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-[#D4A843]/10 border border-[#D4A843]/30 text-[#D4A843] font-bold tracking-wider uppercase text-[10px]">
                    Aula {{ $currentLessonNumber }} de {{ $totalLessons }}
                </span>
            </div>

            {{-- Fast Header Navigation Controls --}}
            <div class="flex items-center gap-2">
                @if ($prevLesson)
                    <a href="{{ url('/' . $panelId . '/' . $tenantSlug . '/lesson/' . $prevLesson->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#023059]/40 border border-[#5881a6]/30 text-xs font-semibold text-gray-300 hover:text-white hover:border-[#D4A843]/50 transition">
                        <x-filament::icon icon="heroicon-m-chevron-left" class="h-4 w-4 text-gray-400" />
                        <span class="hidden sm:inline">Anterior</span>
                    </a>
                @endif

                @if ($nextLesson)
                    <a href="{{ url('/' . $panelId . '/' . $tenantSlug . '/lesson/' . $nextLesson->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#D4A843] text-[#010d26] text-xs font-bold hover:bg-[#b58e33] transition shadow-md">
                        <span class="hidden sm:inline">Próxima</span>
                        <x-filament::icon icon="heroicon-m-chevron-right" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </div>

        {{-- Main Lesson Title & Progress --}}
        <div class="space-y-4">
            @if ($badge)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#D4A843]/10 border border-[#D4A843]/20 text-[#D4A843] text-xs font-semibold uppercase tracking-wider">
                    <x-filament::icon :icon="$badgeIcon" class="h-3.5 w-3.5" />
                    {!! $badge !!}
                </div>
            @endif

            <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-heading">
                {{ $lesson->title }}
            </h1>

            {{-- Micro Progress Bar --}}
            <div class="pt-2">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-1.5 font-medium">
                    <span>Progresso do Curso</span>
                    <span class="font-bold text-[#D4A843]">{{ $progressPercent }}%</span>
                </div>
                <div class="w-full bg-[#010d26] h-2 rounded-full overflow-hidden border border-[#5881a6]/20">
                    <div class="bg-gradient-to-r from-[#5881a6] to-[#D4A843] h-full transition-all duration-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </div>
    </header>
</div>
