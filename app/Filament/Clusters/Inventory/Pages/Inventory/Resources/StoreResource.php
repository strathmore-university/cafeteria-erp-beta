<?php

namespace App\Filament\Clusters\Inventory\Pages\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource\Pages\CreateStore;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource\Pages\EditStore;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource\Pages\ListStores;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource\Pages\ViewStore;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource\RelationManagers\StockLevelsRelationManager;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource\RelationManagers\StockTakesRelationManager;
use App\Models\Inventory\Store;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class StoreResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $cluster = Inventory::class;

    protected static ?string $model = Store::class;

    protected static ?string $slug = 'stores';

    protected static ?int $navigationSort = 1;

    /**
     * @throws Throwable
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('description')->required(),
            Select::make('category_id')->label('Type')
                ->options(store_types()->pluck('name', 'id')->toArray())
                ->searchable()
                ->preload(),
            Toggle::make('is_active')->default(true),
            common_fields(),
        ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Type'),
                TextColumn::make('owner_type')->label('Store Owner')
                    ->formatStateUsing(fn ($state) => class_basename($state)),
            ])
            ->filters([
                SelectFilter::make('owner_type')->options([
                    'App\Models\Core\Team' => 'Team',
                    'App\Models\Production\Station' => 'Station',
                ]),
            ])
            ->actions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStores::route('/'),
            'create' => CreateStore::route('/create'),
            'edit' => EditStore::route('/{record}/edit'),
            'view' => ViewStore::route('/{record}/view'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            StockLevelsRelationManager::class,
            StockTakesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return Store::with('category:id,name');
    }
}
