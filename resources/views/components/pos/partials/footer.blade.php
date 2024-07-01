@props([
    'canBeCancelled',
    'canAddPayment',
    'canBeSubmitted',
])

<footer class="mt-auto flex justify-between gap-8 p-6 text-sm tracking-wide">
    @if ($canBeCancelled)
        <button
            class="group flex items-center gap-2.5 rounded-lg border border-red-200 bg-red-50 py-3 pl-3 pr-4 text-red-700"
            wire:click.prevent="cancel"
            type="button"
        >
            <svg
                class="size-5 text-red-400 group-hover:rotate-90 group-focus:rotate-90"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                aria-hidden="true"
                fill="none"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>

            <span class="group-hover:translate-x-0.5 group-focus:translate-x-0.5">Cancel</span>
        </button>
    @endif

    <div class="ml-auto flex gap-4">
        @if ($canAddPayment)
            <button
                class="group flex items-center gap-2.5 rounded-lg border border-slate-200 bg-slate-50 py-3 pl-3 pr-4 text-slate-700 hover:border-slate-400"
                @click="slideOverOpen=true"
                type="button"
            >
                <svg
                    class="size-5 text-slate-400"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    aria-hidden="true"
                    fill="none"
                >
                    <path
                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

                <span class="group-hover:translate-x-0.5 group-focus:translate-x-0.5">Record Payment</span>
            </button>
        @endif

        @if ($canBeSubmitted)
            <button
                class="group flex items-center gap-2.5 rounded-lg border border-green-200 bg-green-100 py-3 pl-3 pr-4 text-green-700"
                wire:click.prevent="submitSale"
                type="button"
            >
                Submit sale

                <svg
                    class="size-5 group-hover:-rotate-45 group-focus:-rotate-45"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    aria-hidden="true"
                    fill="none"
                >
                    <path
                        d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </button>
        @endif
    </div>
</footer>
