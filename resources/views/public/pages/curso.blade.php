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
<section class="pt-32 pb-24 lg:pt-40 relative z-10" aria-labelledby="curso-titulo">
    <div class="max-w-7xl mx-auto px-6 relative">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">

            {{-- Coluna de argumentacao (7 colunas, seguindo o Padrao A/B do Design System) --}}
            <div class="lg:col-span-7">
                <a href="{{ route('home') }}#planos"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-brand-soft hover:text-white transition-colors mb-8">
                    <i class="ph-bold ph-caret-right rotate-180" aria-hidden="true"></i>
                    Ver todos os planos
                </a>

                <div class="mb-6">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-gold/10 border border-accent-gold/25 text-accent-gold text-xs font-semibold tracking-wider uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent-gold"></span>
                        Método Andreia 360
                    </span>
                </div>

                <h1 id="curso-titulo" class="font-heading text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tighter leading-[0.95] uppercase mb-8">
                    {{ $course->title }}
                </h1>

                @if($course->description)
                    <p class="text-lg sm:text-xl text-brand-light font-light leading-relaxed border-l-4 border-accent-gold pl-6 mb-10">
                        {{ $course->description }}
                    </p>
                @endif

                <div class="rounded-[2rem] overflow-hidden glass-card border border-brand-medium/40 shadow-2xl mb-10">
                    <img src="{{ $coverUrl }}" alt="Capa {{ $course->title }}"
                        width="600" height="400" loading="lazy" decoding="async"
                        class="w-full object-cover aspect-[3/2]">
                </div>

                <div class="glass-card rounded-[2rem] p-8 md:p-10">
                    <span class="text-accent-gold font-bold text-xs tracking-widest uppercase mb-6 block">
                        01 / O que você recebe
                    </span>
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-brand-navy/90 border border-accent-gold/30 shadow-[0_0_25px_rgba(212,168,67,0.12)] flex items-center justify-center text-accent-gold shrink-0" aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                            <div>
                                <strong class="font-heading font-bold text-white text-lg block mb-1">7 dias de garantia incondicional</strong>
                                <span class="text-brand-light/85 text-sm leading-relaxed">Satisfação garantida ou seu investimento de volta, sem burocracia.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-4 border-t border-white/10 pt-5">
                            <div class="w-11 h-11 rounded-2xl bg-brand-navy/90 border border-accent-gold/30 shadow-[0_0_25px_rgba(212,168,67,0.12)] flex items-center justify-center text-accent-gold shrink-0" aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <strong class="font-heading font-bold text-white text-lg block mb-1">Acesso imediato à plataforma</strong>
                                <span class="text-brand-light/85 text-sm leading-relaxed">Treinos, cardápios e aulas liberados logo após a confirmação.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-4 border-t border-white/10 pt-5">
                            <div class="w-11 h-11 rounded-2xl bg-brand-navy/90 border border-accent-gold/30 shadow-[0_0_25px_rgba(212,168,67,0.12)] flex items-center justify-center text-accent-gold shrink-0" aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                </svg>
                            </div>
                            <div>
                                <strong class="font-heading font-bold text-white text-lg block mb-1">Acesso no celular, tablet ou computador</strong>
                                <span class="text-brand-light/85 text-sm leading-relaxed">Assista e treine quando e onde quiser, no seu ritmo.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-4 border-t border-white/10 pt-5">
                            <div class="w-11 h-11 rounded-2xl bg-brand-navy/90 border border-accent-gold/30 shadow-[0_0_25px_rgba(212,168,67,0.12)] flex items-center justify-center text-accent-gold shrink-0" aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </div>
                            <div>
                                <strong class="font-heading font-bold text-white text-lg block mb-1">Acompanhamento focado na mulher 40+</strong>
                                <span class="text-brand-light/85 text-sm leading-relaxed">Respeita o seu metabolismo, a sua rotina e a sua saúde hormonal.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Coluna de conversao (sticky no desktop, 5 colunas) --}}
            <div class="lg:col-span-5 lg:sticky lg:top-28">
                <div class="pricing-premium rounded-[2rem]">
                    <div class="bg-[#021f3b] border border-accent-gold/40 rounded-[2rem] p-8 md:p-10 shadow-2xl relative z-10">
                        @php
                            $activePlan = $selectedPlan ?? \App\Enums\PurchaseType::Premium;
                        @endphp

                        <div class="inline-block bg-accent-gold text-brand-navy text-xs font-black uppercase tracking-widest py-1.5 px-5 rounded-full mb-6">
                            Inscrição Imediata via PIX
                        </div>

                        {{-- Seleção Visual de Planos / Tipos de Compra --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-accent-gold uppercase tracking-widest mb-3">
                                Modalidade de Compra:
                            </label>
                            <div class="grid grid-cols-3 gap-2.5">
                                @foreach (\App\Enums\PurchaseType::cases() as $planOption)
                                    <a href="{{ route('curso.show', ['cursoPublicado' => $course, 'plano' => $planOption->value]) }}"
                                       class="text-center p-3 rounded-xl border text-xs transition-all duration-300 {{ $activePlan === $planOption ? 'bg-accent-gold border-accent-gold text-brand-navy shadow-[0_0_20px_rgba(212,168,67,0.3)] font-black' : 'border-white/10 bg-brand-navy/80 text-brand-light hover:border-accent-gold/40 hover:text-white font-semibold' }}">
                                        <div class="font-heading font-black text-sm uppercase tracking-tight">{{ $planOption->getShortLabel() }}</div>
                                        <div class="text-[11px] {{ $activePlan === $planOption ? 'text-brand-navy/80 font-bold' : 'text-brand-soft' }} mt-0.5">{{ $planOption->formattedPrice() }}</div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-brand-navy/80 border border-white/10 mb-6">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs uppercase tracking-widest font-bold text-accent-gold">
                                    {{ $activePlan->getLabel() }}
                                </span>
                                <span class="text-xs font-semibold text-brand-soft">Mensal</span>
                            </div>
                            <p class="text-xs text-brand-light/90 leading-relaxed">{{ $activePlan->description() }}</p>
                        </div>

                        <div class="mb-6">
                            <p class="text-xs text-brand-light font-bold uppercase tracking-widest mb-1">Investimento</p>
                            <p class="font-heading text-4xl sm:text-5xl font-black tracking-tighter text-white leading-none">
                                {{ $activePlan->formattedPrice() }}
                            </p>
                        </div>

                        {{-- Compra imediata via PIX (UC-04). --}}
                        <div class="mb-4">
                            <livewire:public.course-purchase :course="$course" :plan="$activePlan" :key="$activePlan->value" />
                        </div>

                        <p class="text-center text-xs text-brand-soft mb-6">
                            Acesso liberado automaticamente após a confirmação
                        </p>

                        <ul class="pt-6 border-t border-white/10 space-y-3 text-xs text-brand-soft">
                            <li class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-accent-gold shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                                <span>7 dias de garantia incondicional</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-accent-gold shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Acesso liberado após a confirmação</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-accent-gold shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                <span>Seus dados protegidos conforme a LGPD</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <x-public::autoridade-compacta />
            </div>
        </div>
    </div>
</section>
@endsection
