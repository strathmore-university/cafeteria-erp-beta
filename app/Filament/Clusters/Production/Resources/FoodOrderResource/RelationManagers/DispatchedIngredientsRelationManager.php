<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers;

use App\Models\Production\DispatchedIngredient;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DispatchedIngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'dispatchedIngredients';

    public static function canViewForRecord(
        Model $ownerRecord,
        string $pageClass
    ): bool {
        $id = $ownerRecord->getKey();

        return DispatchedIngredient::whereFoodOrderId($id)->exists();
    }

    public function table(Table $table): Table
    {
        $owner = $this->ownerRecord;
        $value = $owner->getAttribute('initiated_at');
        $value = filled($value);

        return $table
            ->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('requestedIngredient.article.name')
                    ->label('Dispatched against')->searchable()->sortable()
                    ->toggleable(isToggledHiddenByDefault: $value),
                Tables\Columns\TextColumn::make('article.name'),
                Tables\Columns\TextColumn::make('article.unit_capacity')
                    ->label('Unit Capacity')->numeric(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit Name'),
                Tables\Columns\TextColumn::make('dispatcher.name')
                    ->formatStateUsing(fn ($record) => Str::title($record->dispatcher->name))
                    ->label('Dispatched by')
                    ->toggleable(isToggledHiddenByDefault: $value),
                //                TextInputColumn::make('initial_units')
                //                    ->rules(['numeric', 'required'])
                //                    ->disabled(function () {
                //                        $method = 'canExecuteDispatch';
                //
                //                        return !$this->ownerRecord->$method();
                //                    })
                //                    ->afterStateUpdated(function (DispatchedIngredient $record, $state) {
                //                        $record->update(['current_units' => $state]);
                //                    })
                numeric_alt_column($value, $this->ownerRecord),
                //                Tables\Columns\TextColumn::make('current_units')
                //                    ->numeric()->visible(function () {
                //                        $record = $this->ownerRecord;
                //                        $two = filled($record->getAttribute('initiated_at'));
                //                        $key = 'has_recorded_remaining_stock';
                //
                //                        return or_check($record->getAttribute($key), $two);
                //                    }),
                Tables\Columns\TextColumn::make('used_units')
                    ->numeric()->visible(function () {
                        $one = $this->ownerRecord->getAttribute('initiated_at');

                        return filled($one);
                    }),
                Tables\Columns\TextColumn::make('cost_of_production')
                    ->numeric()->visible(function () {
                        $one = $this->ownerRecord->getAttribute('initiated_at');

                        return filled($one);
                    }),
            ])->bulkActions([
                //                todo: when deleting we need to update the dispatched ingredients
                //                BulkAction::make('delete')
                //                    ->requiresConfirmation()
                //                    ->icon('heroicon-o-trash')
                //                    ->action(fn (Collection $records) => $records->each->delete())
            ])
            ->actions([
                //                Tables\Actions\Action::make('delete')
                //                    ->icon('heroicon-o-trash')
                //                    ->action(fn($record) => $record->delete())
                //                    ->requiresConfirmation()
            ]);
    }
}
