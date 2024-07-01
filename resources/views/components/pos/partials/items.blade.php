@props([
    'items',
    'saleTotal',
])

<section class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full overflow-hidden rounded-xl border border-blue-100 bg-white align-middle">
            <header class="flex items-center justify-between gap-2 p-4">
                <h2 class="text-blue-700">Sale Items</h2>

                @if ($saleTotal > 0)
                    <div class="px-4 py-1.5 text-sm font-medium opacity-0">Total</div>
                @endif
            </header>

            @if (filled($items))
                <table
                    class="min-w-full divide-y divide-blue-100 whitespace-nowrap border-t border-blue-100 text-slate-700"
                >
                    <thead class="bg-blue-50 text-xs uppercase tracking-wide">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Name</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Portion</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Unit Price</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Total</th>
                            <th scope="col" class="px-4 py-3"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-blue-100 text-sm">
                        @foreach ($items as $item)
                            <tr>
                                <td class="size-px px-4">{{ Str::title($item['name']) }} x{{ $item['units'] }}</td>

                                <td class="size-px px-4"></td>

                                <td class="size-px px-4 font-medium text-slate-900">
                                    <span class="text-xs font-normal text-slate-600">Ksh.</span>
                                    {{ number_format($item['price']) }}
                                </td>

                                <td class="size-px px-4 font-medium text-slate-900">
                                    <span class="text-xs font-normal text-slate-600">Ksh.</span>
                                    {{ number_format($item['price'] * $item['units']) }}
                                </td>

                                <td class="size-px py-1.5 pl-4 text-slate-500">
                                    <div class="flex items-center gap-2.5 text-slate-500">
                                        <button
                                            class="group rounded-md border p-1.5 text-slate-500 hover:border-slate-500/80 hover:text-slate-900 focus:border-slate-500/80 focus:text-slate-900"
                                            wire:click.prevent="increase({{ $item['code'] }})"
                                            type="button"
                                        >
                                            <svg
                                                class="size-4 group-hover:rotate-90 group-focus:rotate-90"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                aria-hidden="true"
                                                fill="none"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15"
                                                />
                                            </svg>
                                        </button>

                                        <button
                                            class="group rounded-md border p-1.5 text-slate-500 hover:border-slate-500/80 hover:text-slate-900 focus:border-slate-500/80 focus:text-slate-900"
                                            wire:click.prevent="decrease({{ $item['code'] }})"
                                            aria-hidden="true"
                                            type="button"
                                        >
                                            <svg
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                                class="size-4 group-hover:rotate-180 group-focus:rotate-180"
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                            </svg>
                                        </button>

                                        <button
                                            class="rounded-md border p-1.5 text-slate-500 hover:border-slate-500/80 hover:text-slate-900 focus:border-slate-500/80 focus:text-slate-900"
                                            wire:click.prevent="remove({{ $item['code'] }})"
                                            type="button"
                                        >
                                            <svg
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                aria-hidden="true"
                                                class="size-4"
                                                fill="none"
                                            >
                                                <path
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                                    stroke-linejoin="round"
                                                    stroke-linecap="round"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</section>
