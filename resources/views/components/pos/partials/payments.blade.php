@props([
    'payments',
    'saleTotal',
    'totalPaid',
    'balance',
    'change',
])

{{-- <div class="mx-auto max-w-[85rem]"> --}}
<div class="flex flex-col">
    <div class="-m-1.5 overflow-x-auto">
        <div class="inline-block min-w-full p-1.5 align-middle">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between gap-2 border-b p-4">
                    <h2 class="text-sm font-medium text-slate-900">Payment Entries</h2>

                    <div class="flex gap-3 text-sm">
                        @if ($saleTotal > 0)
                            <div class="rounded-full border border-blue-300 bg-blue-50 px-4 py-1.5">
                                <span class="font-medium text-slate-900">Sale Total:</span>
                                <span class="text-xs">Ksh.</span>
                                <span class="font-medium text-slate-900">{{ number_format($saleTotal) }}</span>
                            </div>
                        @endif

                        @if ($totalPaid > 0)
                            <div class="rounded-full border border-blue-300 bg-blue-50 px-4 py-1.5">
                                <span class="font-medium text-slate-900">Paid:</span>
                                <span class="text-xs">Ksh.</span>
                                <span class="font-medium text-slate-900">{{ number_format($totalPaid) }}</span>
                            </div>
                        @endif

                        @if ($balance > 0)
                            <div class="rounded-full border border-red-300 bg-red-50 px-4 py-1.5">
                                <span class="font-medium text-slate-900">Due:</span>
                                <span class="text-xs">Ksh.</span>
                                <span class="font-medium text-slate-900">{{ number_format($balance) }}</span>
                            </div>
                        @endif

                        @if ($change > 0)
                            <div class="rounded-full border border-green-300 bg-green-50 px-4 py-1.5">
                                <span class="font-medium text-slate-900">Change:</span>
                                <span class="text-xs">Ksh.</span>
                                <span class="font-medium text-slate-900">{{ number_format($change) }}</span>
                            </div>
                        @endif
                    </div>
                </header>
                <table class="min-w-full divide-y divide-slate-200 whitespace-nowrap">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-900">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Mode</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Reference</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Tendered Amount</th>
                            <th scope="col" class="px-4 py-3 text-start font-medium">Paid Amount</th>
                            <th scope="col" class="px-4 py-3 text-end"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="size-px px-4">{{ Str::title($payment['mode']) }}</td>
                                <td class="size-px px-4">{{ $payment['reference'] }}</td>
                                <td class="size-px px-4 font-medium">
                                    <span class="text-xs font-normal text-slate-600">Ksh.</span>
                                    {{ number_format($payment['tendered_amount']) }}
                                </td>
                                <td class="size-px px-4 font-medium">
                                    <span class="text-xs font-normal text-slate-600">Ksh.</span>
                                    {{ number_format($payment['paid_amount']) }}
                                </td>
                                <td class="size-px px-4 py-1 text-slate-500">
                                    <button
                                        class="group flex items-center justify-center gap-2 px-2 py-1.5 hover:text-slate-700 focus:text-slate-700"
                                        type="button"
                                        wire:click.prevent="removePayment('{{ $payment['id'] }}')"
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

                                        <span class="group-hover:translate-x-0.5 group-focus:translate-x-0.5">
                                            Delete
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- </div> --}}
