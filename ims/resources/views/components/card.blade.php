@props([
    'title' => null,
    'subtitle' => null,
    'action' => null
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800/80 rounded-xl shadow-sm transition-all duration-200']) }}>
    @if ($title || $subtitle || isset($header) || $action)
        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3">
            <div>
                @if ($title)
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>
                @endif
                {{ $header ?? '' }}
            </div>
            @if ($action)
                <div>
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-4">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="px-4 py-2.5 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800/80 rounded-b-xl">
            {{ $footer }}
        </div>
    @endif
</div>
