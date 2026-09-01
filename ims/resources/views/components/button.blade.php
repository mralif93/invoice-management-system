@props([
    'variant' => 'primary', // primary, secondary, success, danger, warning, outline
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'href' => null,
    'type' => 'button',
    'loading' => false
])

@php
    $baseStyles = 'inline-flex items-center justify-center font-bold rounded-xl transition-all duration-150 transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none cursor-pointer';

    $sizes = [
        'sm' => 'px-2.5 py-1 text-[11px] gap-1.5',
        'md' => 'px-3.5 py-1.5 text-xs gap-1.5',
        'lg' => 'px-4 py-2 text-sm gap-2',
    ];

    $variants = [
        'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm shadow-indigo-500/20 focus:ring-indigo-500',
        'secondary' => 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 focus:ring-slate-400',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-500/20 focus:ring-emerald-500',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm shadow-rose-500/20 focus:ring-rose-500',
        'warning' => 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm shadow-amber-500/20 focus:ring-amber-500',
        'outline' => 'bg-transparent border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 focus:ring-indigo-500',
    ];

    $classes = $baseStyles . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="{{ $size === 'sm' ? 'w-3 h-3' : 'w-3.5 h-3.5' }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="{{ $size === 'sm' ? 'w-3 h-3' : 'w-3.5 h-3.5' }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif
