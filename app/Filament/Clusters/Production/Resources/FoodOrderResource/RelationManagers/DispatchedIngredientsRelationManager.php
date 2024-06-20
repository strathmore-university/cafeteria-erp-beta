<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DispatchedIngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'dispatchedIngredients';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('article.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('foodOrderRecipe.recipe.name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('article.name'),
                Tables\Columns\TextColumn::make('article.unit_capacity')->label('Unit Capacity')->numeric(),
                Tables\Columns\TextColumn::make('unit.name'),
                Tables\Columns\TextColumn::make('dispatcher.name')->label('Dispatched by'),
                Tables\Columns\TextColumn::make('units')->label('Dispatched Units')->numeric(),
            ]);
        //            ->filters([
        //                //
        //            ])
        //            ->headerActions([
        //                Tables\Actions\CreateAction::make(),
        //            ])
        //            ->actions([
        //                Tables\Actions\EditAction::make(),
        //                Tables\Actions\DeleteAction::make(),
        //            ]);
    }
}
