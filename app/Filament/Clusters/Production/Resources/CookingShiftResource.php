<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\CookingShiftResource\Pages\ListCookingShifts;
use App\Filament\Clusters\Production\Resources\CookingShiftResource\Pages\ViewCookingShift;
use App\Filament\Clusters\Production\Resources\CookingShiftResource\Pages\ViewOrders;
use App\Models\Production\CookingShift;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CookingShiftResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $slug = 'cooking-shifts';

    protected static ?string $model = CookingShift::class;

    protected static ?string $cluster = Production::class;

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            TextInput::make('code'),
            TextInput::make('performance_rating'),
            TextInput::make('is_flagged'),
            Section::make([
                placeholder('created_at', 'Created at'),
                placeholder('updated_at', 'Last update at'),
            ])->columns($cols),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('station.name')->searchable()->sortable(),
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('performance_rating')->numeric()
                ->badge()->searchable()->sortable(),
            flag_table_field(),
            TextColumn::make('created_at')
                ->dateTime()->searchable()->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()->searchable()->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCookingShifts::route('/'),
            'view' => ViewCookingShift::route('/{record}/view'),
            'orders' => ViewOrders::route('/{record}/orders'),
        ];
    }
}
