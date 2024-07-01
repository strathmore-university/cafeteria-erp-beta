<div class="relative flex h-lvh flex-col leading-6 text-slate-600 antialiased">
    <x-pos.partials.header />

    <section class="flex items-center justify-between gap-6 pr-6">
        <label class="relative flex flex-1 items-center justify-between gap-6 p-6">
            <h2 class="text-lg text-blue-700">{{ Str::title($user?->name ?? 'Guest Customer') }}</h2>

            <div class="flex items-end gap-10">
                @if (filled($user))
                    <x-pos.partials.amount amount="1000" label="Allowance Balance" />
                    <x-pos.partials.amount amount="1000" label="Wallet Balance" />
                @endif

                <input
                    wire:model.live.debounce.2000ms="user_number"
                    class="absolute left-0 top-full -z-10 opacity-0"
                    @if(blank($this->user)) autofocus @endif
                    autocomplete="off"
                    id="barcode-input"
                    type="text"
                />
            </div>
        </label>

        @if (filled($user))
            <button
                class="group flex items-center gap-2 rounded-full border bg-slate-50 py-1.5 pl-2 pr-3 text-sm hover:text-slate-700 focus:text-slate-700"
                wire:click.prevent="resetCustomer"
                type="button"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    aria-hidden="true"
                    class="size-5 text-slate-400 group-hover:rotate-90 group-hover:text-slate-500 group-focus:rotate-90 group-focus:text-slate-500"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                </svg>

                <span class="group-hover:translate-x-0.5 group-focus:translate-x-0.5">Reset Customer</span>
            </button>
        @endif
    </section>

    <main class="flex flex-1 flex-col overflow-hidden border-y border-slate-200 bg-slate-50/50">
        <section class="grid flex-1 grid-cols-2 gap-4 overflow-y-scroll p-6">
            <x-pos.partials.items :items="$this->saleItems" :$saleTotal />

            <x-pos.partials.payments :payments="$this->recordedPayments" :$totalPaid :$saleTotal :$balance :$change />
        </section>
    </main>

    @php
        $canBeCancelled = or_check(filled($this->saleItems), filled($this->recordedPayments));
        $canBeSubmitted = $this->canSubmit();
        $canAddPayment = $this->balance > 0;
    @endphp

    @if ($canBeCancelled || $canBeSubmitted || $canAddPayment)
        <x-pos.partials.footer :$canBeCancelled :$canBeSubmitted :$canAddPayment />
    @endif

    @if ($canAddPayment)
        <x-pos.modals.record-payment :payment-modes="$this->paymentModes" :$selectedMode />

        <x-pos.modals.mpesa-transaction />
    @endif

    <x-pos.modals.search-items :items="$this->sellingPortions" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Echo.private('test').listen('Anthony', (e) => {
                console.log(e.status);
            });
        });
    </script>

    {{-- <script> --}}
    {{-- document.addEventListener('DOMContentLoaded', function () { --}}
    {{-- Echo.channel('test') --}}
    {{-- .listen('Anthony', (e) => { --}}
    {{-- Livewire.emit('handleTestEvent', e); --}}
    {{-- }); --}}
    {{-- }); --}}
    {{-- </script> --}}
</div>
