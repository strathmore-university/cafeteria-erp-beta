<?php

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;

if ( ! function_exists('numeric_input_column')) {
    function numeric_input_column(
        string $column,
        bool $showInput
    ) {
        if ($showInput) {
            return TextInputColumn::make($column)->rules(['numeric', 'required']);
        }

        return TextColumn::make($column)->numeric()->searchable()->sortable();
    }
}

if ( ! function_exists('text_input')) {
    function text_input(string $column)
    {
        return TextInput::make($column)->required()
            ->string()->maxLength(255);
    }
}
