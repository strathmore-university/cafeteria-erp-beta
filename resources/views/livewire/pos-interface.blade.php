<div class="relative flex h-lvh flex-col leading-6 text-slate-600 antialiased">
    <header class="p-6 flex justify-between items-center gap-8 border-b border-slate-200">
        <h1 class="font-medium text-lg text-slate-900">{{ config('app.name') }}</h1>

        <section class="flex items-center gap-6 w-full max-w-md">
            <div class="bg-slate-50 border rounded-full flex items-center gap-2 px-3 w-full hover:border-slate-400 focus:border-slate-400">
                <svg fill="none" stroke="currentColor" stroke-width="1.5"
                     class="size-5 text-slate-400" viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>

                <input
                        class="py-2 text-sm outline-none focus:outline-none border-0 w-full max-w-xl"
                        placeholder="enter item code"
                        wire:keydown.enter="attempt"
                        wire:model="attemptAddItem"
                        type="text"
                >
            </div>

            <button
                    class="flex items-center gap-2.5 py-2 pl-2.5 pr-4 rounded-lg whitespace-nowrap flex-shrink-0 group"
                    @click="showItems = true" type="button"
            >
                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor"
                     class="size-5 text-slate-400 group-hover:text-slate-300 group-focus:text-slate-300"
                >
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122"
                    />
                </svg>

                <span class="text-slate-800 group-hover:translate-x-0.5 group-focus:translate-x-0.5">All Items</span>
            </button>
        </section>

        <section class="flex items-center gap-4">
            <button
                    class="border rounded-lg px-4 py-2 bg-slate-50 text-slate-700 hover:bg-slate-100 hover:border-slate-400"
                    wire:click.prevent="" type="button"
            >
                Reverse Sale
            </button>

            <button wire:click.prevent="" type="button" class="border p-2 rounded-full">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" class="size-6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>
        </section>
    </header>

    <section class="grid grid-cols-2">
        <label class="p-6 flex items-center justify-between gap-6 relative">
            <h2 class="font-mediums text-slate-600">
                {{ $user?->name ?? 'Guest Customer' }}
            </h2>

            <div class="flex items-center gap-10">
                {{--                @if(filled($user))--}}
                <div class="flex items-end gap-2">
                    <p class="text-sm text-slate-700 tracking-wide">E-Wallet: Ksh.</p>
                    <p class="font-mediums text-slate-900 text-lg">
                        {{ number_format(1000) }}
                    </p>
                </div>

                <div class="flex items-end gap-2">
                    <p class="text-sm text-slate-700 tracking-wide">Meal Allowance: Ksh.</p>
                    <p class="font-mediums text-slate-900 text-lg">
                        {{ number_format(1000) }}
                    </p>
                </div>

                <button
                        wire:click.prevent="resetCustomer" type="button"
                        class="flex gap-2 text-sm text-red-600 bg-red-50 hover:bg-white focus:bg-white border border-red-100 hover:border-red-400  focus:border-red-400 rounded-full pl-2 py-1.5 pr-3 group"
                >
                    <svg fill="none" stroke="currentColor" stroke-width="1.5"
                         class="size-5 text-red-400 group-hover:rotate-180 group-focus:rotate-180" viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>

                    Reset Customer
                </button>
                {{--                @endif--}}

                <input
                        wire:model.live.debounce.2000ms="user_number"
                        class="absolute left-0 top-full -z-10 opacity-0"
                        @if(blank($this->user)) autofocus @endif
                        autocomplete="off"
                        id="barcode-input"
                        type="text"
                >
            </div>
        </label>

        <aside class="grid grid-cols-3 p-6">
            @if(filled($this->saleItems))
                <div class="flex items-end gap-2">
                    <p class="text-sm text-slate-700 tracking-wide">Total Due: Ksh.</p>
                    <p class="font-mediums text-slate-900 text-lg">
                        {{ number_format($this->totalDue) }}
                    </p>
                </div>

                <div class="flex items-end gap-2">
                    <p class="text-sm text-slate-700 tracking-wide">Paid: Ksh.</p>
                    <p class="font-mediums text-slate-900 text-lg">
                        {{ number_format($this->totalPaid) }}
                    </p>
                </div>

                <div class="flex items-end gap-2">
                    <p class="text-sm text-slate-700 tracking-wide">{{ $this->balanceLabel }}: Ksh.</p>
                    <p class="font-mediums text-slate-900 text-lg">
                        {{ (number_format($this->balance)) }}
                    </p>
                </div>
            @endif
        </aside>
    </section>

    <main class="overflow-hidden flex flex-col flex-1 border-y border-slate-200 bg-slate-50/50">
        <div class="grid grid-cols-2 flex-1 overflow-y-scroll p-6 gap-12">
            <aside class="flex flex-col">
                <h3 class="text-slate-900 tracking-wide font-inter text-sm pt-4 pb-10 font-medium uppercase">Sale
                    Items</h3>

                <ul class="divide-y divide-dashed divide-slate-300">
                    @foreach($this->saleItems as $item)
                        <li class="flex justify-between gap-6 py-3">
                            <div>
                                <h4 class="text-slate-900 flex gap-2">
                                    {{ $item['name'] }}
                                    <p>x{{ $item['units'] }}</p>
                                </h4>

                                <div class="flex text-slate-700 mt-0.5 text-sm">
                                    <p class="">Ksh. {{ number_format($item['price']) }} /- each</p>

                                    @if($item['units'] > 1)
                                        <p class="ml-auto">, total of
                                            Ksh. {{ number_format($item['price'] * $item['units']) }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-4 text-slate-500">
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click.prevent="increase({{ $item['code'] }})"
                                            class="p-1.5 rounded-md border text-slate-500 hover:border-slate-500/80 focus:border-slate-500/80 hover:text-slate-900 focus:text-slate-900"
                                    >
                                        <svg fill="none" stroke="currentColor" stroke-width="1.5" class="size-4"
                                             viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M12 4.5v15m7.5-7.5h-15"/>
                                        </svg>
                                    </button>

                                    <button type="button" wire:click.prevent="decrease({{ $item['code'] }})"
                                            class="p-1.5 rounded-md border text-slate-500 hover:border-slate-500/80 focus:border-slate-500/80 hover:text-slate-900 focus:text-slate-900"
                                    >
                                        <svg fill="none" stroke="currentColor" stroke-width="1.5" class="size-4"
                                             viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                        </svg>
                                    </button>
                                </div>

                                <button type="button" wire:click.prevent="remove({{ $item['code'] }})"
                                        class="p-1 -ml-1 text-slate-400 hover:text-slate-600 focus:text-slate-600"
                                >
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5"
                                         class="size-5 " viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <aside class="">
                <h3 class="text-slate-900 tracking-wide font-inter text-sm pt-4 pb-10 font-medium uppercase">
                    Payments</h3>

                <ul class="">
                    @foreach($this->recordedPayments as $payments)
                        <li class="flex justify-between p-2">
                            <div class="">{{ $payments['reference'] }}</div>
                            <div class="">{{ $payments['amount'] }}</div>
                            <div class="">{{ $payments['mode'] }}</div>
                        </li>
                    @endforeach
                </ul>
            </aside>
        </div>
    </main>

    <footer class="mt-auto justify-between p-6 flex gap-8 tracking-wide text-sm">
        <button type="button" wire:click.prevent="cancel"
                class="bg-red-50 pl-3 pr-4 rounded-lg border border-red-200 py-3 text-red-700 flex items-center gap-2.5 group"
        >
            <svg fill="none" stroke="currentColor" stroke-width="1.5"
                 class="size-5 text-red-400 group-focus:rotate-90 group-hover:rotate-90" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>

            <span class="group-hover:translate-x-0.5 group-focus:translate-x-0.5">Cancel</span>
        </button>

        <div class="flex gap-4">
            <button
                    class="bg-slate-50 group pl-3 pr-4 rounded-lg border border-slate-200 py-3 text-slate-700 flex items-center gap-2.5 hover:border-slate-400"
                    type="button" @click="slideOverOpen=true"
            >
                <svg
                        fill="none" stroke="currentColor" stroke-width="1.5"
                        class="size-5 text-slate-400" viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                </svg>

                <span class="group-hover:translate-x-0.5 group-focus:translate-x-0.5">Record Payment</span>
            </button>

            <button
                    class="bg-green-100 pl-3 pr-4 rounded-lg border border-green-200 py-3 group text-green-700 flex items-center gap-2.5"
                    type="button" wire:click.prevent=""
            >
                Submit sale

                <svg
                        class="size-5 group-hover:-rotate-45 group-focus:-rotate-45" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5"
                >
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                </svg>
            </button>
        </div>
    </footer>

    <div
            x-show="slideOverOpen"
            x-trap="slideOverOpen"
            @keydown.window.escape="slideOverOpen=false"
            class="relative z-[99]">
        <div x-show="slideOverOpen" x-transition.opacity.duration.600ms @click="slideOverOpen = false"
             class="fixed inset-0 bg-black bg-opacity-10"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div
                            x-show="slideOverOpen"
                            @click.away="slideOverOpen = false"
                            x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                            x-transition:enter-start="translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full"
                            class="w-screen max-w-md">
                        <div class="flex flex-col h-full py-5 overflow-y-scroll bg-white border-l shadow-lg border-neutral-100/70">
                            <div class="px-4 sm:px-5">
                                <div class="flex items-start justify-between pb-1">
                                    <h2 class="text-base font-semibold leading-6 text-gray-900" id="slide-over-title">
                                        Record Payment
                                    </h2>

                                    <div class="flex items-center h-auto ml-3">
                                        <button @click="slideOverOpen=false" type="button"
                                                class="absolute top-0 right-0 z-30 flex items-center justify-center px-3 py-2 mt-4 mr-5 space-x-1 text-xs font-medium uppercase border rounded-md border-neutral-200 text-neutral-600 hover:bg-neutral-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>

                                            <span>Close</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex-1 px-4 mt-5 sm:px-5">
                                <div class="absolute inset-0 px-4 sm:px-5">
                                    <form wire:submit.prevent="recordPayment"
                                          class="relative h-full flex gap-8 flex-col overflow-hidden rounded-md border-neutral-300"
                                    >
                                        <div class="flex gap-2 flex-wrap whitespace-nowrap">
                                            @foreach($paymentModes as $mode)
                                                <button type="button"
                                                        class="px-4 py-2 flex-1 rounded-md border border-slate-100 bg-slate-50"
                                                        wire:click.prevent="selectPaymentMode('{{ $mode }}')"
                                                >
                                                    {{ $mode }}
                                                </button>
                                            @endforeach
                                        </div>

                                        <div class="space-y-2">
                                            @if($this->showAmount())
                                                <input type="text" wire:model.live.debounce.100ms="recordedAmount"
                                                       class="border p-2 w-full" placeholder="amount">
                                            @endif

                                            @if($this->mpesaOfflineModeSelected())
                                                <input placeholder="mpesa receipt" wire:model="reference"
                                                       class="border p-2 w-full" type="text">
                                            @endif

                                            @if($this->showPhoneNumber())
                                                <input type="text" class="border p-2 w-full" placeholder="phone number">
                                            @endif
                                        </div>

                                        <div class="flex flex-col gap-2 mt-auto">
                                            @if($this->canSubmitPayment())
                                                <button class="border text-center p-3">Submit</button>
                                            @endif

                                            @if($this->showPhoneNumber())
                                                <button type="button" class="border text-center p-3">
                                                    Send STK Push
                                                </button>

                                                <button
                                                        type="button"
                                                        @click="modalOpen = !modalOpen, slideOverOpen = false"
                                                        class="border text-center p-3"
                                                >
                                                    Open Mpesa Transaction
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="modalOpen" class="fixed top-0 left-0 z-[99] flex items-center justify-center w-screen h-screen"
         x-cloak>
        <div x-show="modalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="modalOpen=false"
             class="absolute inset-0 w-full h-full bg-white backdrop-blur-sm bg-opacity-70"></div>
        <div x-show="modalOpen"
             x-trap.inert.noscroll="modalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 sm:scale-95"
             class="relative w-full py-6 bg-white border shadow-lg px-7 border-neutral-200 sm:max-w-lg sm:rounded-lg">
            <div class="flex items-center justify-between pb-3">
                <h3 class="text-lg font-semibold">Modal Title</h3>
                <button @click="modalOpen=false"
                        class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 mt-5 mr-5 text-gray-600 rounded-full hover:text-gray-800 hover:bg-gray-50">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="relative w-auto pb-8">
                <p>mpesa</p>
            </div>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
                <button @click="modalOpen=false" type="button"
                        class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors border rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-100 focus:ring-offset-2">
                    Cancel
                </button>
                <button @click="modalOpen=false" type="button"
                        class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-white transition-colors border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:ring-offset-2 bg-neutral-950 hover:bg-neutral-900">
                    Continue
                </button>
            </div>
        </div>
    </div>

    <div x-show="showItems" class="fixed top-0 left-0 z-[99] flex items-center justify-center w-screen h-screen"
         x-cloak>
        <div x-show="showItems"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showItems=false"
             class="absolute inset-0 w-full h-full bg-white backdrop-blur-sm bg-opacity-70"></div>
        <div x-show="showItems"
             x-trap.inert.noscroll="showItems"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 sm:scale-95"
             class="relative w-full py-6 bg-white border shadow-lg px-7 border-neutral-200 sm:max-w-5xl sm:rounded-lg h-3/4 flex flex-col overflow-hidden"
        >
            <div class="flex items-center justify-between pb-3">
                <h3 class="text-lg font-semibold">Modal Title</h3>
                <button @click="showItems=false"
                        class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 mt-5 mr-5 text-gray-600 rounded-full hover:text-gray-800 hover:bg-gray-50">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="relative w-auto pb-8 flex justify-between items-center">
                <p>items</p>
                <div class="">
                    {{--                    <p>{{ $this->searchPortions }}</p>--}}
                    <input type="text" class="p-2 border"
                           wire:model.live="searchPortions"
                           {{--                           value="{{ $this->searchPortions }}"--}}
                           {{--                           x-model=""--}}

                           {{--                           wire:model.live.debounce.100ms="searchPortions" --}}
                           placeholder="search">
                </div>
            </div>

            <div class="overflow-y-auto flex flex-col -mx-6 px-6 rounded-md flex-1">
                <div class="grid grid-cols-4 gap-2 ">
                    @foreach($this->sellingPortions as $portion)
                        <button wire:click="addFromSelect({{ $portion }})" class="border rounded-md p-2">
                            <div class="">{{ $portion->code }}</div>
                            <div class="">{{ $portion->menuItem->name }}</div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        // document.getElementById('barcode-input').focus();
    </script>
</div>