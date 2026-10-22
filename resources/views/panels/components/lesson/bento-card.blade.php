@props([
    'variant' => 'default', // 'default', 'surface', 'highlight', 'danger', 'warning'
])

@php
    $variants = [
        'default' => 'bg-gradient-to-br from-[#021f3b]/90 via-[#010d26] to-[#000a1c] border-[#5881a6]/20',
        'surface' => 'bg-[#010d26]/90 border-[#5881a6]/20',
        'highlight' => 'bg-gradient-to-br from-[#D4A843]/15 via-[#021f3b] to-[#010d26] border-[#D4A843]/40 shadow-[0_0_30px_rgba(212,168,67,0.15)]',
        'danger' => 'bg-gradient-to-br from-rose-500/10 via-[#021f3b] to-[#010d26] border-rose-500/30',
        'warning' => 'bg-gradient-to-br from-[#D4A843]/10 via-[#021f3b] to-[#010d26] border-[#D4A843]/30',
    ];
    $baseClasses = 'border rounded-[2.5rem] p-8 sm:p-10 shadow-2xl backdrop-blur-xl relative overflow-hidden space-y-8';
    $classes = ($variants[$variant] ?? $variants['default']) . ' ' . $baseClasses;
@endphp

<section {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</section>
