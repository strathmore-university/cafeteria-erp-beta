@props([
    'paymentModes',
    'selectedMode',
])

<section
    @keydown.window.escape="slideOverOpen=false"
    class="relative z-[99]"
    x-trap="slideOverOpen"
    x-show="slideOverOpen"
    x-cloak
>
    <div
        class="fixed inset-0 bg-black bg-opacity-10"
        x-transition.opacity.duration.600ms
        @click="slideOverOpen = false"
        x-show="slideOverOpen"
    ></div>

    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
        <div
            x-show="slideOverOpen"
            @click.away="slideOverOpen = false"
            x-transition:enter="transform transition duration-500 ease-in-out sm:duration-700"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition duration-500 ease-in-out sm:duration-700"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-screen max-w-md"
        >
            <div class="flex h-full flex-col overflow-y-scroll border-l border-neutral-100/70 bg-white shadow-lg">
                <header class="flex translate-y-px items-start justify-between border-b px-6 py-8">
                    <h2 class="font-medium text-slate-900">Record Payment</h2>

                    <x-pos.button.close name="slideOverOpen" />
                </header>

                <form class="flex h-full flex-col gap-6 rounded-md" wire:submit.prevent="recordPayment">
                    <section class="whitespace-nowrap p-6">
                        <p>Select a payment mode</p>

                        <div class="mt-2 flex flex-wrap gap-2 text-sm">
                            @foreach ($paymentModes as $mode)
                                <button
                                    :class="'{{ $selectedMode }}' === '{{ $mode }}' ? 'bg-blue-100 border-blue-400 text-blue-700' : 'bg-slate-50'"
                                    wire:click.prevent="selectPaymentMode('{{ $mode }}')"
                                    class="flex-1 rounded-full border px-4 py-1"
                                    type="button"
                                >
                                    {{ $mode }}
                                </button>
                            @endforeach
                        </div>
                    </section>

                    @if (filled($selectedMode))
                        <fieldset class="flex flex-col gap-8 px-6">
                            <x-pos.partials.input model="tenderedAmount" label="Tendered Amount" />

                            @if ($selectedMode === 'mpesa-offline')
                                <x-pos.partials.input model="mpesaReceipt" label="Mpesa Receipt" />
                            @endif

                            @if ($selectedMode === 'mpesa')
                                <x-pos.partials.input model="phoneNumber" label="Phone Number" />
                            @endif
                        </fieldset>
                    @endif

                    @if (filled($selectedMode))
                        <footer class="sbg-blue-50/50 mt-auto flex translate-y-1 flex-col gap-2 border-t p-6">
                            @if ($selectedMode !== 'mpesa')
                                <button
                                    class="rounded-md border border-blue-300 bg-blue-100 p-3 text-center text-blue-700"
                                >
                                    Submit
                                </button>
                            @endif

                            @if ($selectedMode === 'mpesa')
                                <button
                                    class="rounded-md border border-blue-300 bg-blue-100 p-3 text-center text-blue-700"
                                    type="button"
                                >
                                    Send STK Push
                                </button>

                                <button
                                    type="button"
                                    @click="modalOpen = !modalOpen, slideOverOpen = false"
                                    class="rounded-md border p-3 text-center"
                                >
                                    Open Mpesa Transaction
                                </button>
                            @endif
                        </footer>
                    @endif
                </form>
            </div>
        </div>
    </div>
</section>
