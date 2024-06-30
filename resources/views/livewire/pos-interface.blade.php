<div class="relative flex h-lvh flex-col leading-6 text-slate-600 antialiased">
    <x-pos.partials.header />

    <section class="grid grid-cols-2">
        <label class="relative flex items-center justify-between gap-6 p-6">
            <div class="flex flex-col">
                <h2 class="font-mediums text-slate-600">{{ $user?->name ?? 'Guest Customer' }}</h2>

                @if (filled($user))
                    <button
                        class="group flex gap-2 rounded-full py-1 text-sm text-red-600"
                        wire:click.prevent="resetCustomer"
                        type="button"
                    >
                        Reset Customer
                    </button>
                @endif
            </div>

            <div class="flex items-center gap-10">
                @if (filled($user))
                    <x-pos.partials.amount amount="1000" label="Meal Allowance" />
                    <x-pos.partials.amount amount="1000" label="E-Wallet" />
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

        {{-- <aside class="grid grid-cols-3 p-6"> --}}
        {{-- @if (filled($this->saleItems)) --}}
        {{-- <x-pos.partials.amount :amount="$this->totalDue" label="Total Due" /> --}}
        {{-- <x-pos.partials.amount :amount="$this->totalPaid" label="Paid" /> --}}
        {{-- <x-pos.partials.amount :amount="$this->balance" :label=" $this->balanceLabel " /> --}}
        {{-- @endif --}}
        {{-- </aside> --}}
    </section>

    <main class="flex flex-1 flex-col overflow-hidden border-y border-slate-200 bg-slate-50/50">
        <div class="grid flex-1 grid-cols-2 gap-12 overflow-y-scroll p-6 pt-12">
            <aside>
                <h3 class="text-sm font-medium uppercase tracking-wide text-slate-900">Items</h3>

                <ul class="mt-10 divide-y divide-dashed divide-slate-300">
                    @foreach ($this->saleItems as $item)
                        <x-pos.partials.sale-item :$item />
                    @endforeach
                </ul>
            </aside>

            <aside>
                {{-- <h3 class="text-sm font-medium uppercase tracking-wide text-slate-900">Payments</h3> --}}

                {{-- <ul class="mt-10 divide-y divide-dashed divide-slate-300 overflow-hidden"> --}}
                {{-- @foreach ($this->recordedPayments as $payment) --}}
                <x-pos.partials.payments
                    :$totalPaid
                    :$saleTotal
                    :$balance
                    :$change
                    :payments="$this->recordedPayments"
                />
                {{-- @endforeach --}}
                {{-- </ul> --}}
            </aside>
        </div>
    </main>

    <x-pos.partials.footer
            :canBeCancelled="or_check(filled($this->saleItems), filled($this->recordedPayments))"
            :can-be-submitted="$this->canSubmit()"
            :canAddPayment="$this->balance > 0"
    />

    <x-pos.modals.record-payment
            :payment-modes="$this->paymentModes"
            :$selectedMode
    />

    <x-pos.modals.mpesa-transaction />

    <x-pos.modals.search-items :items="$this->sellingPortions" />

    <script>
        // document.getElementById('barcode-input').focus();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Alpine.data('progressBar', () => ({
                // progressBarWidth: 1,
                // currentStatus: '',
                // init() {
                    {{--this.currentStatus = '{{$order->status}}';--}}
                    // this.updateProgressBar();

                    Echo.private('test')
                        .listen('Anthony', (e) => {
                            console.log(e.status);
                            // this.currentStatus = e.status;
                            // this.updateProgressBar();
                        });
                // },
                // updateProgressBar() {
                //     if (this.currentStatus === 'processing') {
                //         this.progressBarWidth = 40;
                //     }
                //     else if (this.currentStatus === 'shipped') {
                //         this.progressBarWidth = 65;
                //     }
                //     else if (this.currentStatus === 'delivered') {
                //         this.progressBarWidth = 100;
                //     }
                // }
            // }));
        });
    </script>
</div>
