<header class="flex items-center justify-between gap-8 border-b border-slate-200 p-6">
    <h1 class="text-lg font-medium text-slate-900">{{ config('app.name') }}</h1>

    <section class="flex w-full max-w-md items-center gap-6">
        <div
            class="flex w-full items-center gap-2 rounded-full border bg-slate-50 px-3 hover:border-slate-400 focus:border-slate-400"
        >
            <svg fill="none" stroke="currentColor" stroke-width="1.5" class="size-5 text-slate-400" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                />
            </svg>

            <input
                class="w-full max-w-xl border-0 py-2 text-sm outline-none focus:outline-none"
                placeholder="enter item code"
                wire:keydown.enter="addItemByCode"
                wire:model="itemCode"
                type="text"
            />
        </div>

        <button
            class="group flex flex-shrink-0 items-center gap-2.5 whitespace-nowrap rounded-lg py-2 pl-2.5 pr-4"
            @click="showItems = true"
            type="button"
        >
            <svg
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="size-5 text-slate-400 group-hover:text-slate-300 group-focus:text-slate-300"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122"
                />
            </svg>

            <span class="text-slate-800 group-hover:translate-x-0.5 group-focus:translate-x-0.5">All Items</span>
        </button>
    </section>

    <section class="flex items-center gap-4">
        <button
            class="rounded-lg border bg-slate-50 px-4 py-2 text-slate-700 hover:border-slate-400 hover:bg-slate-100"
            wire:click.prevent=""
            type="button"
        >
            Reverse Sale
        </button>

        <button wire:click.prevent="" type="button" class="rounded-full border p-2">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" class="size-6" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </section>
</header>
