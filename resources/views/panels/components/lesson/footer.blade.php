@props([
    'lesson',
    'title' => 'Pronto para avançar?',
    'subtitle' => 'Consolide os conhecimentos desta aula antes de prosseguir para o próximo tópico.',
    'checklist' => [],
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

    $backToCourseUrl = match ($panelId) {
        'user' => $course ? url('/user/' . $tenantSlug . '/course/courses/' . $course->checkout_code) : '#',
        default => $course ? url('/student/' . $tenantSlug . '/course/student-courses/' . $course->checkout_code) : '#',
    };
@endphp

{{-- Standard Footer Action Card --}}
<footer class="bg-gradient-to-br from-[#021f3b] via-[#010d26] to-[#000a1c] border border-[#5881a6]/25 rounded-[2rem] p-6 sm:p-8 shadow-2xl backdrop-blur-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
    {{-- Decorative Ambient Light --}}
    <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-[#D4A843]/10 rounded-full blur-[70px] pointer-events-none"></div>

    <div class="space-y-1.5 text-center md:text-left z-10">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#D4A843]/10 border border-[#D4A843]/25 text-[#D4A843] text-xs font-bold uppercase tracking-wider">
            <x-filament::icon icon="heroicon-m-check-circle" class="h-4 w-4" />
            Lição Concluída
        </div>
        <h3 class="text-xl font-bold text-white tracking-tight font-heading">{{ $title }}</h3>
        <p class="text-sm text-gray-400">{{ $subtitle }}</p>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-3 z-10 w-full md:w-auto">
        @if ($prevLesson)
            <a href="{{ url('/' . $panelId . '/' . $tenantSlug . '/lesson/' . $prevLesson->id) }}" 
               class="px-5 py-3 rounded-full bg-[#010d26] border border-[#5881a6]/30 text-xs font-semibold text-gray-300 hover:text-white hover:border-[#D4A843]/40 hover:bg-[#021f3b] transition shadow-md flex items-center gap-2">
                <x-filament::icon icon="heroicon-m-chevron-left" class="h-4 w-4 text-gray-400" />
                Aula Anterior
            </a>
        @endif

        @if ($nextLesson)
            <a href="{{ url('/' . $panelId . '/' . $tenantSlug . '/lesson/' . $nextLesson->id) }}" 
               class="px-7 py-3 rounded-full bg-[#D4A843] hover:bg-[#b58e33] text-[#010d26] text-xs font-bold transition-all shadow-[0_0_20px_rgba(212,168,67,0.35)] hover:shadow-[0_0_30px_rgba(212,168,67,0.6)] flex items-center gap-2">
                Próxima Aula
                <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" />
            </a>
        @else
            <a href="{{ $backToCourseUrl }}" 
               class="px-7 py-3 rounded-full bg-[#D4A843] hover:bg-[#b58e33] text-[#010d26] text-xs font-bold transition-all shadow-[0_0_20px_rgba(212,168,67,0.35)] hover:shadow-[0_0_30px_rgba(212,168,67,0.6)] flex items-center gap-2">
                <x-filament::icon icon="heroicon-m-academic-cap" class="h-4 w-4" />
                Concluir e Retornar
            </a>
        @endif
    </div>
</footer>
