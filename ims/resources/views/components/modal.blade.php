@props([
    'name' => null,
    'show' => null,
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'maxWidth' => '2xl'
])

@php
    $maxWidthClasses = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '4xl' => 'sm:max-w-4xl',
        'full' => 'sm:max-w-5xl',
    ];
    $sizeClass = $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['2xl'];
    $isDirect = !empty($show);
    $showVar = $isDirect ? $show : 'show';
@endphp

<div
    @if (!$isDirect)
        x-data="{ show: false }"
        x-on:open-modal.window="$event.detail == '{{ $name }}' ? (show = true, $nextTick(() => { if (window.initLucideIcons) window.initLucideIcons(); })) : null"
        x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    @endif
    x-show="{{ $showVar }}"
    x-on:keydown.escape.window="{{ $showVar }} = false"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto p-3 sm:p-6 flex items-center justify-center bg-slate-900/75 backdrop-blur-xs"
    style="display: none;"
>
    <div
        @click.away="{{ $showVar }} = false"
        x-show="{{ $showVar }}"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="w-full bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] overflow-hidden transform transition-all z-10 {{ $sizeClass }}"
    >
        @if ($title)
            <div class="px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/90 flex-shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    @if ($icon)
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm truncate">{{ $title }}</h3>
                        @if ($subtitle)
                            <p class="text-[10px] text-slate-500 truncate">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if (isset($headerAction))
                        {{ $headerAction }}
                    @endif
                    <button type="button" 
                        @click="{{ $showVar }} = false"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="flex-1 overflow-y-auto p-5 sm:p-6 text-xs text-slate-800 dark:text-slate-200">
            {{ $slot }}
        </div>

        @if (isset($footer))
            <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90 flex items-center justify-end gap-2 flex-shrink-0">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
