<?php

namespace App\Filament\Clusters\Production\Resources\RestaurantResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenusRelationManager extends RelationManager
{
    protected static string $relationship = 'menus';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->string()->maxLength(255),
            Select::make('active_day')->required()->options([
                'Monday' => 'Monday',
                'Tuesday' => 'Tuesday',
                'Wednesday' => 'Wednesday',
                'Thursday' => 'Thursday',
                'Friday' => 'Friday',
                'Saturday' => 'Saturday',
            ]),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('active_day')->label('Type'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->slideOver(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn ($record) => get_record_url($record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
