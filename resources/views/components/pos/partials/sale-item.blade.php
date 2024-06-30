@props([
    'item',
])

<li class="flex justify-between gap-6 py-3">
    <div>
        <h4 class="flex gap-2 text-slate-900">
            {{ $item['name'] }}
            <span>x{{ $item['units'] }}</span>
        </h4>

        <div class="mt-0.5 flex text-sm text-slate-700">
            <p>Ksh. {{ number_format($item['price']) }} /- each</p>

            @if ($item['units'] > 1)
                <p class="ml-auto">, total of Ksh. {{ number_format($item['price'] * $item['units']) }}</p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2.5 text-slate-500">
        <div class="flex items-center gap-2">
            <button
                class="rounded-md border p-1.5 text-slate-500 hover:border-slate-500/80 hover:text-slate-900 focus:border-slate-500/80 focus:text-slate-900"
                type="button"
                wire:click.prevent="increase({{ $item['code'] }})"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    class="size-4"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>

            <button
                class="rounded-md border p-1.5 text-slate-500 hover:border-slate-500/80 hover:text-slate-900 focus:border-slate-500/80 focus:text-slate-900"
                type="button"
                wire:click.prevent="decrease({{ $item['code'] }})"
                aria-hidden="true"
            >
                <svg fill="none" stroke="currentColor" stroke-width="1.5" class="size-4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                </svg>
            </button>
        </div>

        <button
            class="p-1.5 text-slate-400 hover:text-slate-600 focus:text-slate-600"
            type="button"
            wire:click.prevent="remove({{ $item['code'] }})"
        >
            <svg
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                class="size-4"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                />
            </svg>
        </button>
    </div>
</li>
