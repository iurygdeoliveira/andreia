@php
    use Illuminate\Support\Number;
    $coverUrl = $course->cover_url;
    $action = $addToCartAction ?? $buyAction ?? null;
@endphp

<!-- Course Card -->
<article
    data-curso-nome="{{ \Illuminate\Support\Str::lower($course->title) }}"
    class="course-card rounded-2xl bg-dark-card border border-dark-border overflow-hidden flex flex-col justify-between h-full relative transition-all duration-300 hover:border-brand/40 hover:shadow-xl hover:shadow-brand/10 hover:-translate-y-1 group">

    <div class="flex flex-col flex-grow">
        {{-- Cover Image Header --}}
        <div class="relative h-44 w-full overflow-hidden bg-dark-surface shrink-0">
            <div class="absolute inset-0 bg-dark/20 group-hover:bg-transparent transition-colors z-10" aria-hidden="true"></div>
            <img src="{{ $coverUrl }}"
                alt="Capa do curso {{ $course->title }}" loading="lazy" decoding="async"
                width="600" height="400"
                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
            @if($course->tags->isNotEmpty())
                <div class="absolute top-3 left-3 z-20 flex flex-wrap gap-1">
                    @foreach($course->tags as $tag)
                        <span class="bg-brand text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wide">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="p-5 flex flex-col flex-grow">
            <h3 class="text-base font-bold text-white group-hover:text-brand transition-colors line-clamp-2 min-h-[2.75rem] mb-2 leading-snug">
                <a href="{{ route('curso.show', $course) }}">
                    {{ $course->title }}
                </a>
            </h3>

            <p class="text-xs text-gray-400 line-clamp-2 mb-4 leading-relaxed">
                {{ $course->description ?? 'Capacitação completa com certificado aceito pelo MEC.' }}
            </p>

            <div class="mt-auto pt-4 border-t border-dark-border">
                <p class="flex items-baseline gap-x-1 mb-4">
                    <span class="text-2xl font-extrabold tracking-tight text-white">
                        {{ $course->formatted_price }}
                    </span>
                    <span class="text-xs text-gray-400">/à vista</span>
                </p>

                @if($action)
                    <div class="w-full">
                        {{ $action }}
                    </div>
                @endif

                <ul class="mt-4 space-y-2 text-xs text-gray-300">
                    @foreach (array_slice($course->getBenefits(), 0, 3) as $benefit)
                        <li class="flex items-center gap-x-2">
                            <span class="text-brand font-bold shrink-0">✔</span>
                            <span class="truncate">{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="px-5 pb-4 pt-2 border-t border-dark-border/50 text-center">
        <a href="{{ route('curso.show', $course) }}" class="text-xs text-gray-400 hover:text-brand transition-colors inline-flex items-center gap-1">
            <span>Ver detalhes da ementa</span>
            <i class="ph-bold ph-arrow-right text-[10px]" aria-hidden="true"></i>
        </a>
    </div>
</article>
