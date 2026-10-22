@php
    $lesson->loadMissing('module.course.modules.lessons');
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
    $currentLessonNumber = ($currentIndex !== false ? $currentIndex + 1 : 1);
    $progressPercent = round(($currentLessonNumber / $totalLessons) * 100);

    $backToCourseUrl = match ($panelId) {
        'user' => $course ? url('/user/' . $tenantSlug . '/course/courses/' . $course->checkout_code) : '#',
        default => $course ? url('/student/' . $tenantSlug . '/course/student-courses/' . $course->checkout_code) : '#',
    };
@endphp

{{-- Ambient Glow --}}
<div class="relative">
    <div class="absolute -top-16 -right-16 w-96 h-96 bg-[#10B981]/15 rounded-full blur-[100px] pointer-events-none -z-10"></div>
    <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-[#34D399]/10 rounded-full blur-[80px] pointer-events-none -z-10"></div>

    {{-- Header Container --}}
    <header class="bg-gradient-to-br from-[#13241d]/90 via-[#0d1a15] to-[#050a08] border border-[#1b3329] rounded-[2rem] p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden space-y-6">
        
        {{-- Top Bar: Navigation Breadcrumb & Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-[#1b3329]/80">
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <a href="{{ $backToCourseUrl }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#13241d] text-gray-300 border border-[#1b3329] hover:text-[#34D399] hover:border-[#10B981]/40 transition shadow-sm font-medium">
                    <x-filament::icon icon="heroicon-m-arrow-left" class="h-3.5 w-3.5 text-[#10B981]" />
                    {{ $course->title ?? 'Curso' }}
                </a>
                <span class="text-gray-600 font-bold">&bull;</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#10B981]/10 border border-[#10B981]/25 text-[#34D399] font-semibold">
                    <x-filament::icon icon="heroicon-m-bookmark" class="h-3.5 w-3.5 text-[#10B981]" />
                    {{ $module->title ?? 'Módulo' }}
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-purple-500/10 border border-purple-500/30 text-purple-300 font-bold tracking-wider uppercase text-[10px]">
                    Aula {{ $currentLessonNumber }} de {{ $totalLessons }}
                </span>
            </div>

            {{-- Fast Header Navigation Controls --}}
            <div class="flex items-center gap-2">
                @if ($prevLesson)
                    <a href="{{ url('/' . $panelId . '/' . $tenantSlug . '/lesson/' . $prevLesson->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#13241d] border border-[#1b3329] text-xs font-semibold text-gray-300 hover:text-white hover:border-[#10B981]/50 hover:bg-[#13241d]/80 transition">
                        <x-filament::icon icon="heroicon-m-chevron-left" class="h-4 w-4 text-gray-400" />
                        Anterior
                    </a>
                @endif
                @if ($nextLesson)
                    <a href="{{ url('/' . $panelId . '/' . $tenantSlug . '/lesson/' . $nextLesson->id) }}" 
                       class="inline-flex items-center gap-1.5 px-5 py-2 rounded-full bg-[#10B981] hover:bg-[#059669] text-white text-xs font-bold transition-all shadow-[0_0_15px_rgba(16,185,129,0.35)] hover:shadow-[0_0_25px_rgba(16,185,129,0.55)]">
                        Próxima Aula
                        <x-filament::icon icon="heroicon-m-chevron-right" class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ $backToCourseUrl }}" 
                       class="inline-flex items-center gap-1.5 px-5 py-2 rounded-full bg-[#10B981] hover:bg-[#059669] text-white text-xs font-bold transition-all shadow-[0_0_15px_rgba(16,185,129,0.35)] hover:shadow-[0_0_25px_rgba(16,185,129,0.55)]">
                        Concluir Curso
                        <x-filament::icon icon="heroicon-m-check-badge" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </div>

        {{-- Hero Title Section --}}
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#10B981]/10 border border-[#10B981]/20 text-[#10B981] text-xs font-bold tracking-wider uppercase">
                <x-filament::icon icon="heroicon-m-academic-cap" class="h-4 w-4" />
                EAFS &bull; Conteúdo Teórico e Prático
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.15]">
                {{ $lesson->title }}
            </h1>
        </div>

        {{-- Progress Bar Container --}}
        <div class="space-y-2 pt-2">
            <div class="flex justify-between items-center text-xs">
                <span class="font-medium text-gray-400">Progresso da Trilha de Aprendizagem</span>
                <span class="font-bold text-[#34D399] tracking-wider">{{ $progressPercent }}% Concluído</span>
            </div>
            <div class="h-2 w-full bg-[#050a08] rounded-full overflow-hidden border border-[#1b3329]">
                <div class="h-full bg-gradient-to-r from-[#34D399] via-[#10B981] to-[#059669] transition-all duration-700 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]" 
                     style="width: {{ $progressPercent }}%;"></div>
            </div>
        </div>
    </header>
</div>
