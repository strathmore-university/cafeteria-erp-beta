<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers;

use App\Actions\Inventory\CreateDispatchIngredient;
use App\Concerns\HasBackRoute;
use App\Models\Inventory\Article;
use App\Models\Production\DispatchedIngredient;
use App\Models\Production\RequestedIngredient;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Throwable;

class RequestedIngredientsRelationManager extends RelationManager
{
    use HasBackRoute;

    protected static string $relationship = 'requestedIngredients';

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->label('Article Family')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('required_quantity')
                    ->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('remaining_quantity')
                    ->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('capacity_at_station'),
                Tables\Columns\TextColumn::make('unit.name')
                    ->searchable()->sortable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('dispatch_article')
                        ->hidden(fn() => !$this->ownerRecord->canExecuteDispatch())
                        ->icon('heroicon-o-truck')->slideOver()->form([
                            Forms\Components\Select::make('article_id')->label('Article')->required()
                                ->options(fn(RequestedIngredient $record) => $record->viableArticles())
                                ->searchable()->preload()->reactive()
                                ->afterStateUpdated(function (Set $set, ?string $state, $record): void {
                                    $this->fillFields($set, $state, $record);
                                }),
                            Forms\Components\TextInput::make('article_unit_capacity')
                                ->reactive()->disabled(),
                            Forms\Components\TextInput::make('article_unit_name')
                                ->reactive()->disabled(),
                            Forms\Components\TextInput::make('dispatched_quantity')
                                ->label('Units to dispatch')
                                ->required()->reactive()->numeric(),
                        ])->action(function ($record, array $data): void {
                            $this->dispatchIngredient($record, $data);
                        }),
                    //                Tables\Actions\Action::make('use station stock')->button()
                    ////                    ->action(fn (RequestedIngredient $record) => $record->useAvailableStationStock())
                    //                    ->visible(fn (
                    //                        RequestedIngredient $record
                    //                    ) => $record->capacity_at_station > 0 && $record->isPendingFulfilment()),
                    //                // todo: using trolley stock
                ]),
            ]);
    }

    /**
     * @throws Throwable
     */
    private function fillFields(
        Set $set,
        ?string $state,
        RequestedIngredient $record
    ): void {
        if (blank($state)) {
            $set('article_unit_capacity', null);
            $set('dispatched_quantity', null);
            $set('article_unit_name', null);

            return;
        }

        $article = Article::with('unit:id,name')->select([
            'id', 'name', 'unit_id', 'unit_capacity',
            'parent_id', 'is_reference', 'team_id',
        ])->find($state);

        $remaining = $record->getAttribute('remaining_quantity');
        $units = $article->unitsToDispatch($remaining);

        $name = $article->unit->getAttribute('name');
        $set('article_unit_capacity', $article->unit_capacity);
        $set('dispatched_quantity', $units);
        $set('article_unit_name', $name);
    }

    /**
     * @throws Throwable
     */
    private function dispatchIngredient(
        RequestedIngredient $record,
        array $data
    ): void {
        try {
            $article = Article::find($data['article_id']);
            $units = $data['dispatched_quantity'];

            $dispatch = (new CreateDispatchIngredient());
            $item = $dispatch->execute($record, $article, $units, true);
            DispatchedIngredient::create($item);

            success();
            $this->back($this->ownerRecord);
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }
}
