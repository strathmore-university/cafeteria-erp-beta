<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\UnitResource\Pages\CreateUnit;
use App\Filament\Clusters\Core\Resources\UnitResource\Pages\EditUnit;
use App\Filament\Clusters\Core\Resources\UnitResource\Pages\ListUnits;
use App\Filament\Clusters\Core\Resources\UnitResource\RelationManagers\DescendantsRelationManager;
use App\Models\Core\Unit;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $cluster = Core::class;

    protected static ?string $model = Unit::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'units';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            Hidden::make('is_reference')->default(true),
            placeholder('created_at', 'Created Date'),
            placeholder('updated_at', 'Last Modified Date'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')])
            ->actions([EditAction::make()]);
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
        return [DescendantsRelationManager::class];
    }
}
