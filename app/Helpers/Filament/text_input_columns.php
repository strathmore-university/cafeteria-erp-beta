<?php

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;

if ( ! function_exists('numeric_stock_transfer_input_column')) {
    function numeric_stock_transfer_input_column(
        string $column,
        bool $showInput
    ) {
        if ($showInput) {
            return TextInputColumn::make($column)->rules(['numeric', 'required']);
        }

        return TextColumn::make($column)->numeric()->searchable()->sortable();
    }
}
