<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers;

use App\Models\Inventory\Article;
use App\Models\Production\RequestedIngredient;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RequestedIngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'requestedIngredients';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('foodOrderRecipe.recipe.name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('article.name')
                    ->label('Article Family')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('required_quantity')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('remaining_quantity')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('capacity_at_station'),
                Tables\Columns\TextColumn::make('unit.name')
                    ->searchable()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('dispatch_article')->button()
                    ->slideOver()
                    ->form([
                        Forms\Components\Select::make('article_id')->label('Article')->required()
                            ->options(function (RequestedIngredient $record) {
                                $article = $record->article;
                                $units = $record->getAttribute('remaining_quantity');
                                $viable = $article->viableDispatchArticles($units);

                                return $viable->pluck('name', 'id')->toArray();
                            })
                            ->searchable()->preload()->reactive()
                            ->afterStateUpdated(function (Set $set, ?string $state, RequestedIngredient $record): void {
                                if (blank($state)) {
                                    $set('dispatched_quantity', null);
                                    $set('article_unit_capacity', null);
                                    $set('article_unit_name', null);

                                    return;
                                }

                                $article = Article::with('unit:id,name')->find($state);
                                $remaining = $record->getAttribute('remaining_quantity');
                                $units = $article->unitsToDispatch($remaining);

                                $name = $article->unit->getAttribute('name');
                                $set('dispatched_quantity', $units);
                                $set('article_unit_capacity', $article->unit_capacity);
                                $set('article_unit_name', $name);
                            }),
                        Forms\Components\TextInput::make('article_unit_capacity')
                            ->reactive()->disabled(),
                        Forms\Components\TextInput::make('article_unit_name')
                            ->reactive()->disabled(),
                        Forms\Components\TextInput::make('dispatched_quantity')
                            ->label('Units to dispatch')
                            ->required()->reactive()->numeric(),
                    ])->action(function (RequestedIngredient $record, array $data): void {
                        $article = Article::find($data['article_id']);
                        $units = $data['dispatched_quantity'];
                        $record->createDispatchIngredient($article, $units);

                        $attributes = ['activeRelationManager' => 1];
                        $url = get_record_url($this->ownerRecord, $attributes);
                        $this->redirect($url, true);
                    })
                    ->visible(fn (RequestedIngredient $record) => $record->isPendingFulfilment()),
                // todo: allow dispatching less
                Tables\Actions\Action::make('use station stock')->button()
//                    ->action(fn (RequestedIngredient $record) => $record->useAvailableStationStock())
                    ->visible(fn (
                        RequestedIngredient $record
                    ) => $record->capacity_at_station > 0 && $record->isPendingFulfilment()),
                // todo: allow dispatching less
            ]);
    }
}
