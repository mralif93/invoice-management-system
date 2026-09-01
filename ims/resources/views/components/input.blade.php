@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'error' => null,
    'icon' => null,
    'prefix' => null,
    'suffix' => null,
    'required' => false
])

<div class="w-full space-y-1">
    @if ($label)
        <label for="{{ $id ?? $name }}" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-lg shadow-sm">
        @if ($icon)
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5"></i>
            </div>
        @elseif ($prefix)
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-[11px] font-bold text-slate-500 font-mono">
                {{ $prefix }}
            </div>
        @endif

        <input 
            type="{{ $type }}"
            id="{{ $id ?? $name }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 dark:focus:border-indigo-500 transition-colors py-1.5 ' . 
                ($icon ? 'pl-8 ' : ($prefix ? 'pl-11 ' : 'pl-3 ')) . 
                ($suffix ? 'pr-11' : 'pr-3')
            ]) }}
        >

        @if ($suffix)
            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[11px] font-bold text-slate-500 font-mono">
                {{ $suffix }}
            </div>
        @endif
    </div>

    @if ($error)
        <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5 font-medium">{{ $error }}</p>
    @endif
</div>
