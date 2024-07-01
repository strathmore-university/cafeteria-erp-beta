<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages\CreateUnitConversion;
use App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages\EditUnitConversion;
use App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages\ListUnitConversions;
use App\Models\Core\Unit;
use App\Models\Core\UnitConversion;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnitConversionResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $model = UnitConversion::class;

    protected static ?string $slug = 'unit-conversions';

    protected static ?string $cluster = Core::class;

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            Section::make([
                Select::make('from_unit_id')->label('From Unit')
                    ->reactive()
                    ->options(
                        fn () => Unit::isDescendant()->select(['id', 'name']) // todo: refactor
                            ->pluck('name', 'id')
                    )
                    ->afterStateUpdated(fn (Set $set) => $set('to_unit_id', null))
                    ->required(),
                Select::make('to_unit_id')->label('To Unit')->reactive()
                    ->options(function (Get $get) {
                        $fromUnitId = $get('from_unit_id');
                        $check = filled($fromUnitId);

                        return Unit::isDescendant()->select(['id', 'name'])// todo: refactor
                            ->when($check, function (Builder $query) use ($fromUnitId): void {
                                $unit = Unit::select(['id', 'parent_id'])->find($fromUnitId);
                                $id = $unit->getAttribute('parent_id');

                                $query->where('id', '!=', $fromUnitId)
                                    ->where('parent_id', '=', $id);
                            })->pluck('name', 'id');
                    })
                    ->required(),
                TextInput::make('factor')->numeric()->required(),
            ])->columns(3),
            Section::make([
                placeholder('created_at', 'Created Date'),
                placeholder('updated_at', 'Last Modified Date'),
            ])->columns($cols),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('from.name'),
            TextColumn::make('to.name'),
            TextColumn::make('factor'),
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
}
