<?php

use App\Models\Production\DispatchedIngredient;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('flag_table_field')) {
    function flag_table_field()
    {
        return TextColumn::make('is_flagged')->label('Flagged')->badge()
            ->formatStateUsing(fn ($state) => tannery($state, 'Yes', 'No'))
            ->color(fn ($state) => match ($state) {
                default => 'success',
                true => 'danger',
            })->sortable();
    }
}

if (! function_exists('numeric_alt_column')) {
    function numeric_alt_column(bool $condition, Model $model)
    {
        if ($condition) {
            return TextColumn::make('initial_units')->numeric()
                ->searchable()->sortable();
        }

        return TextInputColumn::make('initial_units')
            ->rules(['numeric', 'required'])
            ->disabled(function () use ($model) {
                $method = 'canExecuteDispatch';

                return ! $model->$method();
            })
            ->afterStateUpdated(function (DispatchedIngredient $record, $state): void {
                $record->update(['current_units' => $state]);
            });
    }
}
