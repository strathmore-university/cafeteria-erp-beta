<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages;

use App\Concerns\HasBackRoute;
use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use App\Models\Production\DispatchedIngredient;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class RecordRemainingStock extends ManageRelatedRecords
{
    use HasBackRoute;

    protected static string $resource = FoodOrderResource::class;

    protected static string $relationship = 'dispatchedIngredients';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Dispatched Ingredients';
    }

    public function getSubheading(): string|Htmlable|null
    {
        $record = $this->getOwnerRecord();
        $code = $record->getAttribute('code');

        return 'For Food Order: '.$code;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Complete')->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->hidden($this->getOwnerRecord()->getAttribute('has_recorded_remaining_stock'))
                ->action(function () {
                    $record = $this->getOwnerRecord();
                    $record->setAttribute('has_recorded_remaining_stock', true);
                    $record->update();

                    $method = 'populateByProducts';
                    $this->redirect($record->$method(), true);
                }),
            Action::make('view-food-order')
                ->url(get_record_url($this->getOwnerRecord()))
                ->icon('heroicon-o-eye')
        ];
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('initial_units')->numeric(),
                Tables\Columns\TextInputColumn::make('current_units')
                    ->rules(function (DispatchedIngredient $record) {
                        return ['required', 'numeric', 'min:0', 'max:'.$record->initial_units];
                    })
                    ->afterStateUpdated(function (DispatchedIngredient $record) {
                        $diff = $record->initial_units - $record->current_units;
                        $record->used_units = $diff;
                        $record->update();
                    })->disabled(fn() => $this->getOwnerRecord()->getAttribute('has_recorded_remaining_stock')),
                Tables\Columns\TextColumn::make('article.unit_capacity')
                    ->label('Unit Capacity'),
                Tables\Columns\TextColumn::make('unit.name'),
            ]);
    }
}
