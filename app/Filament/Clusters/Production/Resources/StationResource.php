<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\StationResource\Pages\CreateStation;
use App\Filament\Clusters\Production\Resources\StationResource\Pages\EditStation;
use App\Filament\Clusters\Production\Resources\StationResource\Pages\ListStations;
use App\Filament\Clusters\Production\Resources\StationResource\Pages\ViewStation;
use App\Filament\Clusters\Production\Resources\StationResource\RelationManagers\StoresRelationManager;
use App\Models\Production\Station;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StationResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $cluster = Production::class;

    protected static ?string $model = Station::class;

    protected static ?string $slug = 'stations';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            TextInput::make('name')->required()->string()->maxLength(255),
            TextInput::make('description')->required()->string()->maxLength(255),
            Toggle::make('is_active')->default(true),
            Section::make([
                placeholder('created_at', 'Created Date'),
                placeholder('updated_at', 'Last Modified Date'),
            ])->visible(fn ($record) => $record?->exists())->columns($cols),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('description')->searchable()->sortable(),
            IconColumn::make('is_active')->boolean(),
        ])->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            StoresRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStations::route('/'),
            'create' => CreateStation::route('/create'),
            'edit' => EditStation::route('/{record}/edit'),
            'view' => ViewStation::route('/{record}/view'),
        ];
    }
}
