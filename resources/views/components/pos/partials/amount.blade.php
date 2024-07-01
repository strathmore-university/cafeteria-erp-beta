@props([
    'amount',
    'label',
])

<div class="flex items-end gap-2 text-slate-500">
    <p>{{ $label }}: Ksh.</p>
    <p class="text-lg font-medium text-slate-900">{{ number_format($amount) }}</p>
</div>
