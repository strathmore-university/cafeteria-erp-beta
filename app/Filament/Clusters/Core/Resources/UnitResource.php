<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\UnitResource\Pages\CreateUnit;
use App\Filament\Clusters\Core\Resources\UnitResource\Pages\EditUnit;
use App\Filament\Clusters\Core\Resources\UnitResource\Pages\ListUnits;
use App\Filament\Clusters\Core\Resources\UnitResource\RelationManagers\DescendantsRelationManager;
use App\Models\Core\Unit;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $cluster = Core::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'units';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('code')->nullable(),
                Select::make('parent_id')->label('Parent Unit')
                    ->options(Unit::isReference()->pluck('name', 'id')->toArray())
                    ->afterStateUpdated(fn (Set $set, Get $get) => $set('is_reference', blank($get('parent_id'))))
                    ->reactive()
                    ->nullable(),
                Hidden::make('is_reference')->default(true),
                placeholder('created_at', 'Created Date'),
                placeholder('updated_at', 'Last Modified Date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([TextColumn::make('name')])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUnits::route('/'),
            'create' => CreateUnit::route('/create'),
            'edit' => EditUnit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return Unit::isReference();
    }

    public static function getRelations(): array
    {
        return [
            DescendantsRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
