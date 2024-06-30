<x-pos.Layout.pos>
    <main class="bg-blue-50 h-lvh flex flex-col justify-center items-center">
        <section class="">
            <h1 class="text-3xl font-bold">{{ config('app.name') }}</h1>
        </section>

        <form
                class="flex flex-col bg-white p-10 rounded-lg max-w-lg w-full shadow-sm gap-10 mt-16"
                action="{{ route('retail.session.create') }}"
                method="POST"
        >
            @csrf
            <h2 class="font-medium text-2xl text-center">Create Retail Session</h2>

            <x-pos.partials.input type="number" model="initial_cash_float" label="Cash Float"/>

            <fieldset class="flex flex-wrap gap-2 text-sm">
                @foreach($restaurants as $restaurant)
                    <label class="">
                        <input
                                type="radio"
                                class="peer hidden"
                                name="restaurant_id"
                                id="{{ Str::snake($restaurant->name) }}"
                                value="{{ $restaurant->id }}"
                        >

                        <div class="px-4 py-1 bg-slate-50 rounded-full border peer-checked:border-green-300 peer-checked:bg-green-50 peer-checked:text-green-700">
                            <p class="">{{ $restaurant->name }}</p>
                        </div>
                    </label>
                @endforeach
            </fieldset>

            <button
                    class="rounded-md border border-blue-300 bg-blue-100 p-3 text-center text-blue-700"
            >
                Submit
            </button>
        </form>
    </main>
</x-pos.Layout.pos>