@props([
    'title' => 'Síntese Prática • Método Andreia 360',
    'color' => 'gold', // 'gold', 'blue', 'amber', 'rose'
])

@php
    $borderColor = match($color) {
        'blue' => 'border-[#5881a6]',
        'rose' => 'border-rose-500',
        'amber' => 'border-amber-500',
        default => 'border-[#D4A843]',
    };
    $titleColor = match($color) {
        'blue' => 'text-[#9cbcd9]',
        'rose' => 'text-rose-300',
        'amber' => 'text-amber-300',
        default => 'text-[#D4A843]',
    };
@endphp

<div class="border-l-4 {{ $borderColor }} pl-6 py-3 bg-[#023059]/40 rounded-r-2xl space-y-2">
    @if ($title)
        <h4 class="{{ $titleColor }} font-bold text-xs uppercase tracking-wider font-heading">{{ $title }}</h4>
    @endif
    <div class="text-gray-300 text-sm leading-relaxed">
        {{ $slot }}
    </div>
</div>
