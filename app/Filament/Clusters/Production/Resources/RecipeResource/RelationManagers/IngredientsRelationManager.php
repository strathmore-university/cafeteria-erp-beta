<?php

namespace App\Filament\Clusters\Production\Resources\RecipeResource\RelationManagers;

use App\Models\Inventory\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class IngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'ingredients';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('article_id')
                    ->label('Article')->preload()->searchable()->reactive()
                    ->options(Article::whereIsIngredient(true)->isReference()->get()->pluck('name', 'id')->toArray())
                    ->afterStateUpdated(
                        fn (Set $set, string $state) => $set('unit_id', Article::find($state)->getAttribute('unit_id'))
                    ),
                Forms\Components\TextInput::make('quantity')
                    ->required()->numeric(),
                Forms\Components\Hidden::make('unit_id'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->searchable()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
