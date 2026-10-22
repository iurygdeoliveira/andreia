@php
    $courses = $courses ?? \App\Models\Course::query()
        ->where('status', \App\Enums\CourseStatus::Published)
        ->with(['media', 'tags'])->latest()->get();
@endphp

<!-- Popular Courses (Portal Hub) -->
<section id="cursos" class="py-24 bg-dark relative" aria-labelledby="cursos-titulo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <p class="text-brand text-sm font-bold tracking-wider uppercase mb-2">Plataforma de Cursos</p>
                <h2 id="cursos-titulo" class="text-3xl md:text-4xl font-extrabold text-white">Catálogo de Cursos</h2>
            </div>
        </div>

        {{-- Atomic answer citável sobre o catálogo --}}
        <p class="text-gray-400 max-w-3xl mb-10 leading-relaxed">
            Os cursos da EAFS são 100% online, com aulas gravadas e material em PDF para download. Cada matrícula dá
            <strong class="text-gray-200">acesso imediato por 1 ano</strong> e emissão de certificado sem custo
            adicional, aceito como horas complementares em Instituições de Ensino Superior de todo o Brasil.
        </p>

        <livewire:public.courses-catalog :courses="$courses" />
    </div>
</section>
