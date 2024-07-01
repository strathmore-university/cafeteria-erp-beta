@props([
    'model',
    'label',
    'type' => 'text',
])

<label class="">
    <p class="mb-2 text-sm">{{ $label }}</p>

    <input
        class="w-full rounded-md border p-2"
        placeholder="enter amount"
        wire:model="{{ $model }}"
        name="{{ $model }}"
        type="{{ $type }}"
    />

    @error($model)
        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
    @enderror
</label>
