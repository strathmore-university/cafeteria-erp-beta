<?php

namespace App\Filament\Clusters\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Resources\StoreResource\Pages\CreateStore;
use App\Filament\Clusters\Inventory\Resources\StoreResource\Pages\EditStore;
use App\Filament\Clusters\Inventory\Resources\StoreResource\Pages\ListStores;
use App\Models\Inventory\Store;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $cluster = Inventory::class;

    protected static ?string $slug = 'stores';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * @throws Throwable
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('description')->required(),
                Select::make('category_id')->label('Type')
                    ->options(store_types()->pluck('name', 'id')->toArray())
                    ->preload()
                    ->searchable(),
                common_fields(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')->label('Type'),
                TextColumn::make('owner_type')->label('Store Owner'),
            ])
            ->filters([])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStores::route('/'),
            'create' => CreateStore::route('/create'),
            'edit' => EditStore::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return Store::with('category:id,name');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
