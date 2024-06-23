<?php

namespace App\Filament\Clusters\Production\Resources\RecipeResource\RelationManagers;

use App\Models\Core\Unit;
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
        return $form->schema([
            Forms\Components\Select::make('article_id')
                ->label('Ingredient Family')->preload()->searchable()
                ->reactive()->options(ingredient_articles())
                ->afterStateUpdated(function (Set $set, string $state): void {
                    $article = Article::select('unit_id')->find($state);
                    $id = $article->getAttribute('unit_id');
                    $unit = Unit::select(['id', 'name'])->find($id);

                    $set('unit_name', $unit->getAttribute('name'));
                    $set('unit_id', $unit->id);
                }),
            Forms\Components\TextInput::make('unit_name')
                ->label('Unit name')->disabled(),
            Forms\Components\TextInput::make('quantity')
                ->required()->numeric(),
            Forms\Components\Hidden::make('unit_id'),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->label('Ingredient Family')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()->sortable()->numeric(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->searchable()->sortable(),
            ])->headerActions([
                Tables\Actions\CreateAction::make()->slideOver(),
            ])->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }
}
