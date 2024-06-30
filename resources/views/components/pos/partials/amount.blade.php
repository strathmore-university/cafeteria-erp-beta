@props([
    'amount',
    'label',
])

<div class="flex items-end gap-2">
    <h2 class="text-sm tracking-wide text-slate-700">{{ $label }}: Ksh.</h2>

    <p class="font-mediums text-lg text-slate-900">
        {{ number_format($amount) }}
    </p>
</div>
