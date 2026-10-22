@extends('public.layouts.app')

@section('title', $course->title . ' | Método Andreia 360')
@section('description', \Illuminate\Support\Str::limit($course->description ?? 'Transformação feminina completa para mulheres acima dos 40 anos.', 155))

{{-- Pagina de conversao: nao deve ser indexada como concorrente da home,
     e o conteudo depende de um curso especifico que muda. --}}
@section('robots', 'noindex, follow')

@php
    $coverUrl = $course->getFirstMediaUrl('cover')
        ?: ($course->cover_image_path
            ? \Illuminate\Support\Facades\Storage::url($course->cover_image_path)
            : asset('images/andreia360/andreia-hero.jpg'));
@endphp

@section('content')
<section class="pt-32 pb-24 lg:pt-40" aria-labelledby="curso-titulo">
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start">

            {{-- Coluna de argumentacao --}}
            <div>
                <a href="{{ route('home') }}#planos"
                    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-brand transition-colors mb-6">
                    <i class="ph-bold ph-caret-right rotate-180" aria-hidden="true"></i>
                    Ver todos os planos
                </a>

                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-muted border border-brand/20 text-brand text-sm font-semibold mb-6">
                    <i class="ph-fill ph-sparkle text-lg text-accent-gold" aria-hidden="true"></i>
                    Método Andreia 360
                </div>

                <h1 id="curso-titulo" class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight mb-6">
                    {{ $course->title }}
                </h1>

                @if($course->description)
                    <p class="text-lg text-gray-400 mb-8 leading-relaxed">{{ $course->description }}</p>
                @endif

                <img src="{{ $coverUrl }}" alt="Capa {{ $course->title }}"
                    width="600" height="400" loading="lazy" decoding="async"
                    class="w-full rounded-2xl border border-dark-border mb-8 object-cover aspect-[3/2]">

                <ul class="space-y-4">
                    @foreach ([
                        ['ph-shield-check', '7 dias de garantia incondicional', 'Satisfação garantida ou seu investimento de volta, sem burocracia.'],
                        ['ph-clock-countdown', 'Acesso imediato à plataforma', 'Treinos, cardápios e aulas liberados logo após a confirmação.'],
                        ['ph-devices', 'Acesso no celular, tablet ou computador', 'Assista e treine quando e onde quiser, no seu ritmo.'],
                        ['ph-heart', 'Acompanhamento focado na mulher 40+', 'Respeita o seu metabolismo, a sua rotina e a sua saúde hormonal.'],
                    ] as [$icone, $titulo, $texto])
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand-muted flex items-center justify-center text-brand shrink-0">
                                <i class="ph-fill {{ $icone }} text-xl text-accent-gold" aria-hidden="true"></i>
                            </div>
                            <div>
                                <strong class="text-white block mb-1">{{ $titulo }}</strong>
                                <span class="text-gray-400 text-sm">{{ $texto }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Coluna de conversao (sticky no desktop) --}}
            <div class="lg:sticky lg:top-28">
                <div class="bg-dark-card border border-dark-border rounded-2xl p-6 md:p-8 shadow-2xl">
                    @php
                        $activePlan = $selectedPlan ?? \App\Enums\PurchaseType::Premium;
                    @endphp

                    {{-- Seleção Visual de Planos / Tipos de Compra --}}
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">
                            Modalidade de Compra:
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach (\App\Enums\PurchaseType::cases() as $planOption)
                                <a href="{{ route('curso.show', ['cursoPublicado' => $course, 'plano' => $planOption->value]) }}"
                                   class="text-center p-2.5 rounded-xl border text-xs font-semibold transition-all {{ $activePlan === $planOption ? 'bg-brand/20 border-accent-gold text-white shadow-sm ring-1 ring-accent-gold/40' : 'border-dark-border bg-dark-bg/60 text-gray-400 hover:text-white hover:border-gray-600' }}">
                                    <div class="font-bold text-sm">{{ $planOption->getShortLabel() }}</div>
                                    <div class="text-[11px] opacity-80 mt-0.5">{{ $planOption->formattedPrice() }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-dark-bg/80 border border-dark-border mb-6">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs uppercase tracking-wider font-bold text-accent-gold">
                                {{ $activePlan->getLabel() }}
                            </span>
                            <span class="text-xs font-semibold text-gray-400">Mensal</span>
                        </div>
                        <p class="text-xs text-gray-300 leading-relaxed">{{ $activePlan->description() }}</p>
                    </div>

                    <p class="text-sm text-gray-400 mb-1">Investimento</p>
                    <p class="text-4xl font-extrabold text-white mb-6">
                        {{ $activePlan->formattedPrice() }}
                    </p>

                    {{-- Caminho primario: compra imediata via PIX (UC-04). O
                         formulario de lead abaixo continua para quem prefere
                         falar antes de comprar (DEC-01). --}}
                    <div class="mb-4">
                        <livewire:public.course-purchase :course="$course" :key="$activePlan->value" />
                    </div>

                    <p class="text-center text-xs text-gray-500 mb-8">
                        Acesso liberado automaticamente após a confirmação
                    </p>

                    <div class="relative mb-8">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-dark-border"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-dark-card px-3 text-xs text-gray-500 uppercase tracking-wide">ou</span>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-white mb-2">Prefere falar antes?</h2>
                    <p class="text-sm text-gray-400 mb-6">
                        Preencha os dados abaixo e nossa equipe entra em contato pelo WhatsApp para tirar
                        suas dúvidas.
                    </p>

                    @if ($errors->any())
                        <div role="alert"
                            class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-200">
                            <p class="font-semibold mb-1">Confira os campos abaixo:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('curso.lead', $course) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="lead-name" class="block text-sm font-medium text-gray-300 mb-1.5">
                                Nome completo
                            </label>
                            <input id="lead-name" name="name" type="text" required autocomplete="name"
                                value="{{ old('name') }}"
                                @error('name') aria-invalid="true" @enderror
                                class="w-full bg-dark-surface border border-dark-border rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors">
                            <p class="mt-1.5 text-xs text-gray-500">Como deve aparecer no seu certificado.</p>
                        </div>

                        <div>
                            <label for="lead-email" class="block text-sm font-medium text-gray-300 mb-1.5">
                                E-mail
                            </label>
                            <input id="lead-email" name="email" type="email" required
                                autocomplete="email" inputmode="email" value="{{ old('email') }}"
                                @error('email') aria-invalid="true" @enderror
                                class="w-full bg-dark-surface border border-dark-border rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors">
                        </div>

                        <div>
                            <label for="lead-phone" class="block text-sm font-medium text-gray-300 mb-1.5">
                                WhatsApp
                            </label>
                            <input id="lead-phone" name="phone" type="tel" required
                                autocomplete="tel" inputmode="tel" placeholder="(00) 00000-0000"
                                value="{{ old('phone') }}"
                                @error('phone') aria-invalid="true" @enderror
                                class="w-full bg-dark-surface border border-dark-border rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors">
                        </div>

                        <div>
                            <label for="lead-profile" class="block text-sm font-medium text-gray-300 mb-1.5">
                                Você é <span class="text-gray-500 font-normal">(opcional)</span>
                            </label>
                            <select id="lead-profile" name="profile"
                                class="w-full bg-dark-surface border border-dark-border rounded-xl px-4 py-3 text-white focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-colors">
                                <option value="">Selecione</option>
                                @foreach (['Estudante', 'Profissional de Educação Física', 'Fisioterapeuta', 'Nutricionista', 'Outra área da saúde'] as $opcao)
                                    <option value="{{ $opcao }}" @selected(old('profile') === $opcao)>{{ $opcao }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                            class="w-full bg-brand-cta hover:bg-brand-cta-hover active:scale-[0.98] text-white font-semibold py-4 px-8 rounded-full transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] flex items-center justify-center gap-2">
                            Garantir Meu Acesso Agora
                            <i class="ph-bold ph-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>

                    <ul class="mt-5 space-y-2 text-xs text-gray-400">
                        @foreach ([
                            'ph-shield-check' => '7 dias de garantia incondicional',
                            'ph-clock-countdown' => 'Acesso liberado após a confirmação',
                            'ph-lock-simple' => 'Seus dados protegidos conforme a LGPD',
                        ] as $icone => $texto)
                            <li class="flex items-center gap-2">
                                <i class="ph-fill {{ $icone }} text-brand text-base" aria-hidden="true"></i>
                                {{ $texto }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <x-public::autoridade-compacta />
            </div>
        </div>
    </div>
</section>
@endsection
