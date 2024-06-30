@props([
    'items',
])

<section
    class="fixed left-0 top-0 z-[99] flex h-screen w-screen items-center justify-center"
    @keydown.window.escape="showItems=false"
    x-trap="showItems"
    x-show="showItems"
    x-cloak
>
    <div
        class="absolute inset-0 h-full w-full bg-white bg-opacity-70 backdrop-blur-sm"
        x-transition:enter="duration-300 ease-out"
        x-transition:leave="duration-300 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="showItems=false"
        x-show="showItems"
    ></div>

    <div
        class="relative flex h-3/4 w-full flex-col overflow-hidden border border-neutral-200 bg-white px-7 py-6 shadow-lg sm:max-w-5xl sm:rounded-xl"
        x-trap.inert.noscroll="showItems"
        x-show="showItems"
        x-transition:enter="duration-300 ease-out"
        x-transition:enter-start="-translate-y-2 opacity-0 sm:scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
        x-transition:leave="duration-200 ease-in"
        x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
        x-transition:leave-end="-translate-y-2 opacity-0 sm:scale-95"
    >
        <header class="flex items-center justify-between pb-10">
            <div class="relative flex w-auto items-center justify-between gap-6">
                <h3 class="text-lg text-slate-900">Menu Items</h3>

                <div
                    class="flex items-center gap-2 rounded-full border bg-slate-50 px-3 hover:border-slate-400 focus:border-slate-400"
                >
                    <svg
                        class="size-5 text-slate-400"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        fill="none"
                    >
                        <path
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    <input
                        class="w-full max-w-xl border-0 py-2 text-sm outline-none focus:outline-none"
                        wire:model.live="searchPortions"
                        placeholder="search"
                        type="text"
                    />
                </div>
            </div>

            <x-pos.button.close name="showItems" />
        </header>

        <main class="-mx-6 flex flex-1 flex-col overflow-y-auto rounded-md px-6">
            <div class="grid grid-cols-5 gap-2">
                @foreach ($items as $item)
                    <button
                        wire:click.prevent="addFromSelect({{ $item }})"
                        class="rounded-md border p-4 text-left"
                        type="button"
                    >
                        <h4 class="text-xl font-medium text-slate-900">{{ $item->code }}</h4>

                        <div class="mt-2 gap-3 text-sm text-slate-600">
                            <p>{{ $item->menuItem->name }}</p>
                            <p>Ksh. {{ number_format($item->selling_price) }}</p>
                        </div>
                    </button>
                @endforeach
            </div>
        </main>
    </div>
</section>
