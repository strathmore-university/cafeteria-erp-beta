<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\ProductDispatchResource\Pages\EditProductDispatch;
use App\Filament\Clusters\Production\Resources\ProductDispatchResource\Pages\ListProductDispatches;
use App\Filament\Clusters\Production\Resources\ProductDispatchResource\Pages\ViewProductDispatch;
use App\Filament\Clusters\Production\Resources\ProductDispatchResource\RelationManagers\ItemsRelationManager;
use App\Models\Production\ProductDispatch;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductDispatchResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $model = ProductDispatch::class;

    protected static ?string $slug = 'product-dispatches';

    protected static ?string $cluster = Production::class;

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            Section::make([
                Placeholder::make('destination')
                    ->content(fn (ProductDispatch $record) => $record->destination->getAttribute('name')),
                Placeholder::make('Created By')
                    ->content(fn (ProductDispatch $record) => Str::title($record->dispatcher->getAttribute('name'))),
                Placeholder::make('From Store')
                    ->content(fn (ProductDispatch $record) => $record->from->getAttribute('name')),
                Placeholder::make('To Store')
                    ->content(fn (ProductDispatch $record) => $record->to->getAttribute('name')),
                Placeholder::make('Status')
                    ->content(fn (ProductDispatch $record) => $record->getAttribute('status')),
                Placeholder::make('Received By')
                    ->content(fn (ProductDispatch $record) => $record->receiver?->getAttribute('name'))
                    ->visible(fn ($record) => filled($record->received_by)),
            ])->columns($cols),
            Section::make([
                placeholder('created_at', 'Created at'),
                placeholder('updated_at', 'Last updated at'),
                placeholder('dispatched_at', 'Dispatched at'),
                placeholder('received_at', 'Received at'),
            ])->columns($cols),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('destination.name')->searchable()->sortable(),
            TextColumn::make('from.name')->searchable()->sortable(),
            TextColumn::make('to.name')->searchable()->sortable(),
            TextColumn::make('dispatcher.name')->searchable()->sortable()
                ->formatStateUsing(fn ($state) => Str::title($state)),
            TextColumn::make('receiver.name')->searchable()
                ->sortable()->toggleable(isToggledHiddenByDefault: true)
                ->formatStateUsing(fn ($state) => Str::title($state)),
            TextColumn::make('status')->searchable()->sortable()
                ->badge()->color(fn ($state) => match ($state) {
                    'received' => 'success',
                    default => 'warning'
                })->formatStateUsing(fn ($state) => Str::title($state)),
        ])->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductDispatches::route('/'),
            'edit' => EditProductDispatch::route('/{record}/edit'),
            'view' => ViewProductDispatch::route('/{record}/view'),
        ];
    }
}
