<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\Production\ProductConversionResource\Pages\ListProductConversions;
use App\Filament\Clusters\Production\Resources\Production\ProductConversionResource\Pages\ViewProductConversion;
use App\Models\Production\ProductConversion;
use Exception;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductConversionResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $slug = 'product-conversions';
    protected static ?string $model = ProductConversion::class;
    protected static ?string $cluster = Production::class;
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            TextInput::make('station_id')->label('Station')
                ->formatStateUsing(fn($record) => $record->station->name),
            TextInput::make('created_by')->label('Station')
                ->formatStateUsing(fn($record) => $record->creator->name),
            TextInput::make('from_id')->label('Product to convert')
                ->formatStateUsing(fn($record) => $record->from->name),
            TextInput::make('to_id')->label('Target Product')
                ->formatStateUsing(fn($record) => $record->to->name),
            TextInput::make('quantity')->label('Quantity'),
            Section::make([
                placeholder('created_at', 'Created at'),
                placeholder('updated_at', 'Last updated at')
            ])->columns($cols)
        ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('station.name')->searchable()->sortable(),
            TextColumn::make('creator.name')
                ->label('Created By')->searchable()->sortable(),
            TextColumn::make('from.name')->searchable()->sortable(),
            TextColumn::make('to.name')->searchable()->sortable(),
            TextColumn::make('quantity')->searchable()->sortable(),
        ])->filters([
            SelectFilter::make('station')->searchable()->preload()
                ->relationship('station', 'name'),
        ])->actions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductConversions::route('/'),
            'view' => ViewProductConversion::route('/{record}/view'),
        ];
    }
}
