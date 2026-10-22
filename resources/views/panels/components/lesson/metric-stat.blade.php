@props([
    'value',
    'unit' => null,
    'label',
    'description' => null,
    'icon' => null,
    'color' => 'gold', // 'gold', 'brand', 'blue', 'rose'
])

@php
    $colorMap = [
        'gold' => ['bg' => 'bg-[#D4A843]/15', 'border' => 'border-[#D4A843]/30', 'text' => 'text-[#D4A843]'],
        'brand' => ['bg' => 'bg-[#5881a6]/15', 'border' => 'border-[#5881a6]/30', 'text' => 'text-[#9cbcd9]'],
        'blue' => ['bg' => 'bg-[#023059]/40', 'border' => 'border-[#5881a6]/30', 'text' => 'text-[#9cbcd9]'],
        'rose' => ['bg' => 'bg-rose-500/15', 'border' => 'border-rose-500/30', 'text' => 'text-rose-400'],
    ];
    $c = $colorMap[$color] ?? $colorMap['gold'];
@endphp

<div class="bg-[#010d26]/80 border border-[#5881a6]/20 p-6 sm:p-7 rounded-2xl shadow-xl flex items-center gap-5 hover:border-[#D4A843]/40 transition duration-300">
    @if ($icon)
        <div class="w-14 h-14 rounded-2xl {{ $c['bg'] }} border {{ $c['border'] }} flex items-center justify-center {{ $c['text'] }} shrink-0">
            <x-filament::icon :icon="$icon" class="h-7 w-7" />
        </div>
    @endif

    <div class="space-y-1">
        <span class="text-xs font-bold {{ $c['text'] }} uppercase tracking-wider block font-heading">{{ $label }}</span>
        <p class="text-2xl sm:text-3xl font-black text-white font-heading">
            {{ $value }}
            @if ($unit)
                <span class="text-sm sm:text-base font-normal text-gray-400">{{ $unit }}</span>
            @endif
        </p>
        @if ($description)
            <p class="text-xs text-gray-400 leading-relaxed">{{ $description }}</p>
        @endif
    </div>
</div>
