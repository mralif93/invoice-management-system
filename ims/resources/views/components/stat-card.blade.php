@props([
    'title',
    'value',
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'icon' => null,
    'iconVariant' => 'indigo', // indigo, emerald, amber, rose
    'subtitle' => null
])

@php
    $iconBg = [
        'indigo' => 'bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400',
        'emerald' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400',
        'amber' => 'bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400',
        'rose' => 'bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400',
    ];
@endphp

<div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 shadow-sm transition-all duration-200 hover:shadow-md">
    <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ $title }}</span>
        @if ($icon)
            <span class="p-2 rounded-lg {{ $iconBg[$iconVariant] ?? $iconBg['indigo'] }}">
                <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5"></i>
            </span>
        @endif
    </div>

    <div class="mt-2.5 flex items-baseline gap-2">
        <span class="text-xl font-extrabold text-slate-900 dark:text-white font-mono tracking-tight">{{ $value }}</span>
    </div>

    @if ($subtitle || $change)
        <div class="mt-1.5 flex items-center gap-1.5 text-[11px]">
            @if ($change)
                <span class="font-bold flex items-center gap-0.5 {{ $changeType === 'positive' ? 'text-emerald-600 dark:text-emerald-400' : ($changeType === 'negative' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500') }}">
                    <i data-lucide="{{ $changeType === 'positive' ? 'trending-up' : ($changeType === 'negative' ? 'trending-down' : 'minus') }}" class="w-3 h-3"></i>
                    {{ $change }}
                </span>
            @endif
            @if ($subtitle)
                <span class="text-slate-500 dark:text-slate-400 truncate">{{ $subtitle }}</span>
            @endif
        </div>
    @endif
</div>
