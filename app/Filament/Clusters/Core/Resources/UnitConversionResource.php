<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages\CreateUnitConversion;
use App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages\EditUnitConversion;
use App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages\ListUnitConversions;
use App\Models\Core\Unit;
use App\Models\Core\UnitConversion;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitConversionResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $model = UnitConversion::class;

    protected static ?string $slug = 'unit-conversions';

    protected static ?string $cluster = Core::class;

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('from_unit_id')
                    ->label('From Unit')
                    ->reactive()
                    ->options(fn () => Unit::isDescendant()->select(['id', 'name'])->pluck('name', 'id'))
                    ->afterStateUpdated(fn (Set $set) => $set('to_unit_id', null))
                    ->required(),
                Select::make('to_unit_id')
                    ->label('From Unit')
                    ->reactive()
                    ->options(function (Get $get) {
                        $fromUnitId = $get('from_unit_id');

                        return Unit::isDescendant()->select(['id', 'name'])
                            ->when(filled($fromUnitId), function (Builder $query) use ($fromUnitId): void {
                                $unit = Unit::find($fromUnitId);

                                $query
                                    ->where('parent_id', '=', $unit->parent_id)
                                    ->where('id', '!=', $fromUnitId);
                            })->pluck('name', 'id');
                    })
                    ->required(),
                TextInput::make('factor')
                    ->numeric()->required()
                    ->required(),
                placeholder('created_at', 'Created Date'),
                placeholder('updated_at', 'Last Modified Date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from.name'),
                TextColumn::make('to.name'),
                TextColumn::make('factor'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUnitConversions::route('/'),
            'create' => CreateUnitConversion::route('/create'),
            'edit' => EditUnitConversion::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
