<?php

namespace App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form->columns(1)->schema([
            Forms\Components\Select::make('article_id')->label('Article')
                ->options(fn () => articles_that_can_be_added($this->ownerRecord->getKey()))
                ->searchable()->preload()->required()->reactive()
                ->afterStateUpdated(function (Set $set, ?string $state): void {
                    if (blank($state)) {
                        $set('price', null);

                        return;
                    }

                    $id = $this->ownerRecord->getAttribute('supplier_id');
                    $set('price', query_price_quotes((int) $state, $id));
                }),
            Forms\Components\TextInput::make('price')
                ->label('Ordering Price')
                ->required()->numeric(),
            Forms\Components\TextInput::make('ordered_units')
                ->label('Quantity to order')->required()->integer(),
        ]);
    }

    public function table(Table $table): Table
    {
        $allowEdits = $this->ownerRecord->allowEdits();

        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('ordered_units')
                    ->numeric()->sortable(),
                Tables\Columns\TextColumn::make('delivered_units')
                    ->formatStateUsing(fn ($record) => $record->delivered_units)
                    ->label('Delivered units')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('remaining_units')
                    ->numeric()->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()->numeric()->prefix('Ksh. '),
                Tables\Columns\TextColumn::make('total_value')
                    ->sortable()->numeric()->prefix('Ksh. '),
            ])->headerActions([
                Tables\Actions\CreateAction::make()->label('Add an item')
                    ->visible($allowEdits)->slideOver(),
            ])->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->visible($allowEdits),
            ])->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
