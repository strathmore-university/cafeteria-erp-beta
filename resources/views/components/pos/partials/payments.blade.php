@props([
    'payments',
    'saleTotal',
    'totalPaid',
    'balance',
    'change',
])

<section class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full overflow-hidden rounded-xl border border-blue-100 bg-white align-middle">
            <header class="flex items-center justify-between gap-2 p-4 font-medium">
                <h2 class="font-normal text-blue-700">Payment Entries</h2>

                <div class="flex gap-2 text-sm">
                    @if ($saleTotal > 0)
                        <h3 class="rounded-full border border-slate-300 bg-slate-50 px-4 py-1.5 text-slate-700">
                            Total: {{ number_format($saleTotal) }}
                        </h3>
                    @endif

                    @if ($totalPaid > 0)
                        <h3 class="rounded-full border border-blue-300 bg-blue-50 px-4 py-1.5 text-blue-700">
                            Paid: {{ number_format($totalPaid) }}
                        </h3>
                    @endif

                    @if ($balance > 0)
                        <h3 class="rounded-full border border-red-400 bg-red-50 px-4 py-1.5 text-red-700">
                            Due: {{ number_format($balance) }}
                        </h3>
                    @endif

                    @if ($change > 0)
                        <h3 class="rounded-full border border-green-300 bg-green-50 px-4 py-1.5 text-green-700">
                            Change: {{ number_format($change) }}
                        </h3>
                    @endif
                </div>
            </header>

            @if ($totalPaid > 0)
                <table
                    class="min-w-full divide-y divide-blue-100 whitespace-nowrap border-t border-blue-100 text-slate-700"
                >
                    <thead class="bg-blue-50 text-xs uppercase tracking-wide">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Mode</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Reference</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Tendered Amount</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Paid Amount</th>
                            <th scope="col" class="px-4 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-blue-100 text-sm">
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="size-px px-4">{{ Str::title($payment['mode']) }}</td>

                                <td class="size-px px-4">{{ $payment['reference'] }}</td>

                                <td class="size-px px-4 font-medium text-slate-900">
                                    <span class="text-xs font-normal text-slate-600">Ksh.</span>
                                    {{ number_format($payment['tendered_amount']) }}
                                </td>

                                <td class="size-px px-4 font-medium text-slate-900">
                                    <span class="text-xs font-normal text-slate-600">Ksh.</span>
                                    {{ number_format($payment['paid_amount']) }}
                                </td>

                                <td class="size-px py-1 pl-4 text-slate-500">
                                    <div class="pb-px">
                                        <button
                                            class="group flex items-center justify-center gap-2 px-2 py-1.5 hover:text-slate-700 focus:text-slate-700"
                                            wire:click.prevent="removePayment('{{ $payment['id'] }}')"
                                            type="button"
                                        >
                                            <svg
                                                class="size-4 text-slate-300 group-hover:text-slate-500 group-focus:text-slate-500"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                aria-hidden="true"
                                                fill="none"
                                            >
                                                <path
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                                    stroke-linejoin="round"
                                                    stroke-linecap="round"
                                                />
                                            </svg>

                                            <p class="group-hover:translate-x-0.5 group-focus:translate-x-0.5">
                                                Delete
                                            </p>
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
