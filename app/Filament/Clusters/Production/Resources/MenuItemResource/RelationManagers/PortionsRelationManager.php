<?php

namespace App\Filament\Clusters\Production\Resources\MenuItemResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PortionsRelationManager extends RelationManager
{
    protected static string $relationship = 'portions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->required()->maxLength(255),
            Forms\Components\TextInput::make('selling_price')
                ->required()->numeric(),
            Forms\Components\Select::make('unit_id')->label('Unit')
                ->options(unit_descendants('Portion')->pluck('name', 'id')->toArray())
                ->searchable()->preload()->required(),
            Forms\Components\Hidden::make('article_id')
                ->default($this->ownerRecord->getAttribute('article_id')),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('code')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->searchable()->sortable(),
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
