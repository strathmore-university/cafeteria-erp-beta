<?php

namespace App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\RelationManagers;

use App\Models\Inventory\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('article_id')->label('Article')
                    ->options(Article::canBeOrdered()->pluck('name', 'id')->toArray())
                    ->afterStateUpdated(function (Set $set, string $state): void {
                        $id = $this->ownerRecord->getAttribute('supplier_id');
                        $set('price', query_price_quotes((int) $state, $id));
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->preload(),
                Forms\Components\TextInput::make('ordered_units')->required()->integer(),
                Forms\Components\TextInput::make('price')->reactive()->required()->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        $allowEdits = $this->ownerRecord->allowEdits();

        return $table
            ->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name'),
                Tables\Columns\TextColumn::make('ordered_units')->numeric(),
                Tables\Columns\TextColumn::make('remaining_units')->numeric(),
                Tables\Columns\TextColumn::make('total_value')->numeric()->prefix('Ksh. '),
                Tables\Columns\TextColumn::make('price')->numeric(),
            ])
            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible($allowEdits),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible($allowEdits),
                Tables\Actions\DeleteAction::make()->visible($allowEdits),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
