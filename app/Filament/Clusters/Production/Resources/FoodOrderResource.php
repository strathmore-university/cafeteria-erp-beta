<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\CreateFoodOrder;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\EditFoodOrder;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\ListFoodOrders;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\ViewFoodOrder;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers\DispatchedIngredientsRelationManager;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers\RequestedIngredientsRelationManager;
use App\Models\Production\FoodOrder;
use App\Models\Production\Restaurant;
use App\Models\Production\Station;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FoodOrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $slug = 'production/food-orders';

    protected static ?string $cluster = Production::class;

    protected static ?string $model = FoodOrder::class;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form
            ->schema([
                TextInput::make('code')->disabled(),
                Select::make('restaurant_id')->label('Restaurant')
                    ->searchable()->preload()
                    ->options(Restaurant::get()->pluck('name', 'id')->toArray()),
                Select::make('station_id')->label('Station')
                    ->searchable()->preload()
                    ->options(Station::get()->pluck('name', 'id')->toArray()),
                Select::make('prepared_by')
                    ->searchable()->preload()
                    ->options(User::get()->pluck('name', 'id')->toArray()),
                TextInput::make('status')->disabled(),
                Section::make([
                    placeholder('created_at', 'Created at'),
                    placeholder('updated_at', 'Last updated at'),
                ])->columns($cols)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('restaurant.name')->label('Destination')->searchable()->sortable(),
                TextColumn::make('station.name')->label('Prepared at')->searchable()->sortable(),
                TextColumn::make('preparer.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Prepared by')->searchable()->sortable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn($state) => Str::title($state))
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        default => 'warning'
                    }),
            ])
            ->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            RequestedIngredientsRelationManager::class,
            DispatchedIngredientsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFoodOrders::route('/'),
            'create' => CreateFoodOrder::route('/create'),
            'edit' => EditFoodOrder::route('/{record}/edit'),
            'view' => ViewFoodOrder::route('/{record}/view'),
        ];
    }
}
