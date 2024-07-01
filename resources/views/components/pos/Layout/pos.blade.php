<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ config('app.name') . ' | POS' }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
            rel="stylesheet"
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body
        x-data="{ slideOverOpen: false, modalOpen: false, showItems: false }"
        @keydown.window="if ($event.key === 'Backquote' || $event.key === '`') { $event.preventDefault(); showItems = false; slideOverOpen = false; document.getElementById('barcode-input').focus(); }"
        @keydown.period.prevent="showItems = false; slideOverOpen = false; document.getElementById('addItemByCode').focus()"
        @keydown.comma.prevent="slideOverOpen = !slideOverOpen, showItems = false"
        @keydown.slash.prevent="showItems = !showItems, slideOverOpen = false"
        @close-payment-mode-modal="slideOverOpen = false"
        @close-items-modal="showItems = false"
        @open-items-modal="showItems = true"
        class="font-primary"
    >
        {{ $slot }}

        @livewireScripts
    </body>
</html>
