@props([])

<section
    class="fixed left-0 top-0 z-[99] flex h-screen w-screen items-center justify-center"
    @keydown.window.escape="modalOpen=false"
    x-trap="modalOpen"
    x-show="modalOpen"
    x-cloak
>
    <div
        x-show="modalOpen"
        x-transition:enter="duration-300 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="duration-300 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="modalOpen=false"
        class="absolute inset-0 h-full w-full bg-white bg-opacity-70 backdrop-blur-sm"
    ></div>
    <div
        x-show="modalOpen"
        x-trap.inert.noscroll="modalOpen"
        x-transition:enter="duration-300 ease-out"
        x-transition:enter-start="-translate-y-2 opacity-0 sm:scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
        x-transition:leave="duration-200 ease-in"
        x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
        x-transition:leave-end="-translate-y-2 opacity-0 sm:scale-95"
        class="relative w-full border border-neutral-200 bg-white px-7 py-6 shadow-lg sm:max-w-lg sm:rounded-lg"
    >
        <div class="flex items-center justify-between pb-3">
            <h3 class="text-lg font-semibold">Modal Title</h3>
            <x-pos.button.close name="modalOpen" />
        </div>
        <div class="relative w-auto pb-8">
            <p>mpesa</p>
        </div>
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2">
            <button
                @click="modalOpen=false"
                type="button"
                class="inline-flex h-10 items-center justify-center rounded-md border px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-neutral-100 focus:ring-offset-2"
            >
                Cancel
            </button>
            <button
                @click="modalOpen=false"
                type="button"
                class="inline-flex h-10 items-center justify-center rounded-md border border-transparent bg-neutral-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-neutral-900 focus:outline-none focus:ring-2 focus:ring-neutral-900 focus:ring-offset-2"
            >
                Continue
            </button>
        </div>
    </div>
</section>
