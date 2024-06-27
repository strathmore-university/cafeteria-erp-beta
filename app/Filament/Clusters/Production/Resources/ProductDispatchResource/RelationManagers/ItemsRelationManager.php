<?php

namespace App\Filament\Clusters\Production\Resources\ProductDispatchResource\RelationManagers;

use App\Models\Inventory\Article;
use App\Models\Inventory\StockLevel;
use App\Models\Inventory\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Throwable;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form->columns(1)->schema([
            Forms\Components\Select::make('article_id')
                ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                    if (blank($state)) {
                        $set('dispatched_quantity', null);

                        return;
                    }

                    $set('dispatched_quantity', $this->maxQuantity($get));
                })
                ->options($this->articles())->label('Product')
                ->searchable()->reactive()->required(),
            Forms\Components\TextInput::make('dispatched_quantity')
                ->maxValue(fn (Get $get) => $this->maxQuantity($get))
                ->required()->reactive()->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        $owner = $this->ownerRecord;
        $dispatched = filled($owner->getAttribute('dispatched_at'));
        $canReceive = filled($owner->getAttribute('dispatched_at'));
        $received = blank($owner->getAttribute('received_at'));
        $canReceive = and_check($canReceive, $received);

        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->label('Product'),
                Tables\Columns\TextColumn::make('dispatched_quantity')
                    ->numeric()->label('Dispatched Portions'),
                numeric_input_column('received_quantity', $canReceive)
                    ->visible($dispatched)->label('Received Portions'),
            ])->headerActions([
                Tables\Actions\CreateAction::make()->slideOver(),
            ])->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make()->requiresConfirmation(),
                ]),
            ]);
    }

    private function articles(): array
    {
        $storeId = $this->ownerRecord->getAttribute('from_store_id');

        if (blank($storeId)) {
            return [];
        }

        $ids = StockLevel::where('store_id', '=', $storeId)
            ->where('current_units', '>', 0)
            ->pluck('article_id');

        return Article::whereIsProduct(true)
            ->isDescendant()
            ->whereIn('id', $ids)
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * @throws Throwable
     */
    private function maxQuantity(Get $get): ?int
    {
        $articleId = $get('article_id');

        if (blank($articleId)) {
            return null;
        }

        $article = Article::find($get('article_id'));
        $store = Store::find($this->ownerRecord->getAttribute('from_store_id'));

        return article_units($article, $store);
    }
}
