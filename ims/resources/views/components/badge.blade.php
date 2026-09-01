@props([
    'variant' => 'slate', // slate, indigo, emerald, amber, rose
    'size' => 'md', // sm, md
    'pulse' => false,
    'icon' => null
])

@php
    $baseStyles = 'inline-flex items-center font-bold tracking-tight rounded-full border';

    $sizes = [
        'sm' => 'px-2 py-0.5 text-[10px] gap-1',
        'md' => 'px-2.5 py-1 text-xs gap-1.5',
    ];

    $variants = [
        'slate' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        'indigo' => 'bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
        'emerald' => 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
        'amber' => 'bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
        'rose' => 'bg-rose-50 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
    ];

    $pulseDots = [
        'slate' => 'bg-slate-400',
        'indigo' => 'bg-indigo-500',
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'rose' => 'bg-rose-500',
    ];

    $classes = $baseStyles . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['slate']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($pulse)
        <span class="w-1.5 h-1.5 rounded-full {{ $pulseDots[$variant] ?? 'bg-slate-400' }} animate-pulse"></span>
    @elseif ($icon)
        <i data-lucide="{{ $icon }}" class="{{ $size === 'sm' ? 'w-3 h-3' : 'w-3.5 h-3.5' }}"></i>
    @endif
    <span>{{ $slot }}</span>
</span>
