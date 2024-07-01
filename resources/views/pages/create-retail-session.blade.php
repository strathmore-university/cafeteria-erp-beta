<x-pos.Layout.pos>
    <main class="flex h-lvh flex-col items-center justify-center bg-blue-50">
        <section class="">
            <h1 class="text-3xl font-bold">{{ config('App.name') }}</h1>
        </section>

        <form
            class="mt-16 flex w-full max-w-lg flex-col gap-10 rounded-lg bg-white p-10 shadow-sm"
            action="{{ route('retail.session.create') }}"
            method="POST"
        >
            @csrf
            <h2 class="text-center text-2xl font-medium">Create Retail Session</h2>

            <x-pos.partials.input type="number" model="initial_cash_float" label="Cash Float" />

            <fieldset class="flex flex-wrap gap-2 text-sm">
                @foreach ($restaurants as $restaurant)
                    <label class="">
                        <input
                            type="radio"
                            class="peer hidden"
                            name="restaurant_id"
                            id="{{ Str::snake($restaurant->name) }}"
                            value="{{ $restaurant->id }}"
                        />

                        <div
                            class="rounded-full border bg-slate-50 px-4 py-1 peer-checked:border-green-300 peer-checked:bg-green-50 peer-checked:text-green-700"
                        >
                            <p class="">{{ $restaurant->name }}</p>
                        </div>
                    </label>
                @endforeach
            </fieldset>

            <button class="rounded-md border border-blue-300 bg-blue-100 p-3 text-center text-blue-700">Submit</button>
        </form>
    </main>
</x-pos.Layout.pos>
