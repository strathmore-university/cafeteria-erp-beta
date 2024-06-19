<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static ?string $title = 'Recipes to Prepare';

    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('recipe.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('recipe.name')
            ->columns([
                Tables\Columns\TextColumn::make('recipe.name'),
                Tables\Columns\TextColumn::make('expected_portions'),
                Tables\Columns\TextColumn::make('produced_portions'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(false),
                Tables\Actions\DeleteAction::make()->visible(false),
                Tables\Actions\Action::make('view_recipe')
                    ->url(fn($record) => get_record_url($record->recipe))
                    ->icon('heroicon-o-eye'),
                Tables\Actions\Action::make('update')->button()
                    ->fillForm(fn($record) => $record->only('produced_portions'))
                    ->visible(fn() => $this->ownerRecord->getAttribute('status') === 'preparation started')
                    ->form([
                        Forms\Components\TextInput::make('produced_portions')
                            ->required()->numeric()
                    ])
                    ->action(
                        fn($record, array $data) => $record->fill($data)->update()
                    ),
            ]);
    }
}
