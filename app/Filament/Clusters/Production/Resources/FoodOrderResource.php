<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\CreateFoodOrder;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\EditFoodOrder;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\ListFoodOrders;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\RecordByProducts;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\RecordRemainingStock;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages\ViewFoodOrder;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers\DispatchedIngredientsRelationManager;
use App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers\RequestedIngredientsRelationManager;
use App\Models\Production\FoodOrder;
use App\Models\Production\Station;
use App\Models\User;
use Exception;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FoodOrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $slug = 'production/food-orders';

    protected static ?string $cluster = Production::class;

    protected static ?string $model = FoodOrder::class;

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form
            ->schema([
                TextInput::make('code')->disabled(),
                TextInput::make('owner_id')->label('Order For')
                    ->formatStateUsing(fn ($record) => $record->ownerName()),
                Select::make('station_id')->label('Station')
                    ->searchable()->preload()
                    ->options(Station::get()->pluck('name', 'id')->toArray()),
                Select::make('prepared_by')
                    ->searchable()->preload()
                    ->visible(fn ($state) => filled($state))
                    ->options(User::pluck('name', 'id')->toArray()),
                TextInput::make('expected_portions')->disabled(),
                TextInput::make('produced_portions')->disabled(),
                TextInput::make('performance_rating')->disabled(),
                TextInput::make('status')->disabled(),
                Section::make([
                    placeholder('created_at', 'Created at'),
                    placeholder('updated_at', 'Last updated at'),
                ])->columns($cols),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('owner_id')->label('Destination')
                ->formatStateUsing(fn ($record) => $record->ownerName())
                ->searchable()->sortable(),
            TextColumn::make('station.name')->label('Prepared at')->searchable()->sortable(),
            TextColumn::make('expected_portions')->sortable(),
            TextColumn::make('produced_portions')->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('performance_rating')->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            flag_table_field()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('status')->badge()
                ->formatStateUsing(fn ($state) => Str::title($state))
                ->color(fn (string $state): string => match ($state) {
                    'prepared' => 'success',
                    'flagged' => 'danger',
                    default => 'warning'
                }),
        ])->filters([
            SelectFilter::make('station')->searchable()->preload()
                ->relationship('station', 'name'),
            SelectFilter::make('status')->options([
                'pending preparation' => 'Pending preparation',
                'prepared' => 'Prepared',
            ]),
        ])
            ->defaultSort('code', 'desc')
            ->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            RequestedIngredientsRelationManager::class,
            DispatchedIngredientsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return FoodOrder::with('owner');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFoodOrders::route('/'),
            'create' => CreateFoodOrder::route('/create'),
            'edit' => EditFoodOrder::route('/{record}/edit'),
            'view' => ViewFoodOrder::route('/{record}/view'),
            'record-stock' => RecordRemainingStock::route('/{record}/record-stock'),
            'record-by-products' => RecordByProducts::route('/{record}/record-by-products'),
        ];
    }
}
