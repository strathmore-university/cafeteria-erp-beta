<?php

namespace App\Filament\Clusters\Inventory\Resources\StockTransferResource\RelationManagers;

use App\Models\Inventory\Article;
use App\Models\Inventory\StockLevel;
use App\Models\Inventory\StockTransferItem;
use App\Models\Inventory\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Throwable;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('article_id')
                ->options(fn () => $this->fetchAvailableArticles())
                ->label('Article')->required()->reactive()->searchable()
                ->afterStateUpdated(function (Forms\Set $set, Get $get, $state): void {
                    if (blank($state)) {
                        $set('units', null);

                        return;
                    }

                    $set('units', $this->maxQuantity($get));
                }),
            Forms\Components\TextInput::make('units')
                ->maxValue(fn (Get $get) => $this->maxQuantity($get))
                ->required()->minValue(1)->reactive()
                ->integer()->label('Quantity'),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        // todo: add permissions
        $owner = $this->ownerRecord;
        $approved = filled($owner->getAttribute('approved_at'));
        $actioned = filled($owner->getAttribute('actioned_at'));
        $received = filled($owner->getAttribute('received_at'));

        $canDispatch = and_check($approved, ! $actioned);
        $canReceive = and_check($actioned, ! $received);

        return $table
            ->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('article.unit_capacity')
                    ->label('Unit Capacity')->sortable()->numeric(),
                Tables\Columns\TextColumn::make('units')
                    ->sortable()->numeric(),
                numeric_input_column(
                    'dispatched_units', $canDispatch
                )->visible($approved),
                numeric_input_column(
                    'received_units', $canReceive
                )->visible($actioned),
                Tables\Columns\TextColumn::make('article.unit.name')
                    ->searchable()->sortable(),
            ])->headerActions([
                Tables\Actions\CreateAction::make()->slideOver()->link(),
            ])->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }

    private function fetchAvailableArticles(): array
    {
        $owner = $this->ownerRecord;
        $ids = StockTransferItem::whereStockTransferId($owner->getKey())
            ->pluck('article_id')
            ->toArray();

        $id = $owner->getAttribute('from_store_id');
        $articleIds = StockLevel::where('store_id', '=', $id)
            ->where('current_units', '>', 0)
            ->pluck('article_id')
            ->toArray();

        return Article::whereIn('id', array_diff($articleIds, $ids))
            ->whereIsProduct(false)
            ->isDescendant()
            ->get()
            ->sortBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * @throws Throwable
     */
    private function maxQuantity(Get $get): int
    {
        if (blank($get('article_id'))) {
            return 0;
        }

        $owner = $this->ownerRecord;
        $id = $owner->getAttribute('from_store_id');
        $store = Store::select('id')->find($id);
        $article = Article::find($get('article_id'));

        return article_units($article, $store);
    }
}
