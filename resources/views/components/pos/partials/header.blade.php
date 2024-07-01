<header class="flex items-center justify-between gap-8 border-b border-slate-200 p-6">
    <h1 class="text-lg font-medium text-slate-900">{{ config('app.name') }}</h1>

    <section class="flex w-full max-w-md items-center gap-6">
        <div
            class="group flex w-full items-center gap-2 rounded-full border bg-slate-50 px-3 focus-within:border-blue-300 focus-within:bg-transparent"
        >
            <svg
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                class="size-5 text-slate-400 group-focus-within:text-blue-400 group-hover:text-blue-400"
                viewBox="0 0 24 24"
            >
                <path
                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>

            <input
                class="w-full max-w-xl border-0 py-2"
                wire:keydown.enter="addItemByCode"
                placeholder="Enter item code"
                wire:model="itemCode"
                id="addItemByCode"
                type="text"
            />
        </div>

        <button
            class="group flex flex-shrink-0 items-center gap-2.5 whitespace-nowrap rounded-lg py-2 pl-2.5 pr-4"
            @click="showItems = true"
            type="button"
        >
            <svg
                class="size-5 text-slate-400 group-hover:text-slate-300 group-focus:text-slate-300"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                fill="none"
            >
                <path
                    d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122"
                    stroke-linejoin="round"
                    stroke-linecap="round"
                />
            </svg>

            <span class="text-slate-800 group-hover:translate-x-0.5 group-focus:translate-x-0.5">All Items</span>
        </button>
    </section>

    <section class="flex items-center gap-4">
        <button
            class="group flex items-center gap-2 rounded-full border bg-slate-50 px-4 py-2 text-slate-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 focus:border-blue-300 focus:bg-blue-50 focus:text-blue-600"
            wire:click.prevent=""
            type="button"
        >
            <svg
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                class="size-5 text-slate-400 group-hover:text-blue-400 group-focus:text-blue-400"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8.25 9.75h4.875a2.625 2.625 0 0 1 0 5.25H12M8.25 9.75 10.5 7.5M8.25 9.75 10.5 12m9-7.243V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z"
                />
            </svg>

            <span class="group-hover:translate-x-0.5 group-focus:translate-x-0.5">Cancel a sale</span>
        </button>

        <button wire:click.prevent="" type="button" class="rounded-full border p-2">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" class="size-5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </section>
</header>
