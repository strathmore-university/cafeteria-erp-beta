<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\RestaurantResource\Pages\CreateRestaurant;
use App\Filament\Clusters\Production\Resources\RestaurantResource\Pages\EditRestaurant;
use App\Filament\Clusters\Production\Resources\RestaurantResource\Pages\ListRestaurants;
use App\Filament\Clusters\Production\Resources\RestaurantResource\Pages\ViewRestaurant;
use App\Filament\Clusters\Production\Resources\RestaurantResource\RelationManagers\MenusRelationManager;
use App\Filament\Clusters\Production\Resources\RestaurantResource\RelationManagers\StoresRelationManager;
use App\Models\Production\Restaurant;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RestaurantResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $slug = 'restaurants';

    protected static ?string $cluster = Production::class;

    protected static ?string $model = Restaurant::class;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            TextInput::make('name')->rules('string|required|max:255'),
            TextInput::make('description')->rules('string|required|max:255'),
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
            TextColumn::make('name')->searchable(),
            TextColumn::make('description'),
            IconColumn::make('is_active')->boolean(),
        ])->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            StoresRelationManager::class,
            MenusRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurants::route('/'),
            'create' => CreateRestaurant::route('/create'),
            'edit' => EditRestaurant::route('/{record}/edit'),
            'view' => ViewRestaurant::route('/{record}/view'),
        ];
    }
}
