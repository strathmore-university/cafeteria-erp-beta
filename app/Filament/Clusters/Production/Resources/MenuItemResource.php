<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\MenuItemResource\Pages;
use App\Filament\Clusters\Production\Resources\MenuItemResource\RelationManagers\PortionsRelationManager;
use App\Models\Inventory\Article;
use App\Models\Production\MenuItem;
use App\Models\Production\Station;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $cluster = Production::class;

    protected static ?string $model = MenuItem::class;

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name'),
            TextInput::make('selling_price')->prefix('Ksh. ')->numeric(),
            TextInput::make('portions_to_prepare')->prefix('Ksh. ')->numeric(),
            TextInput::make('code'),
            Select::make('article_id')->label('Article')
                ->options(Article::whereIsProduct(true)->isDescendant()->pluck('name', 'id')->toArray())
                ->preload()->searchable()->required(),
            Select::make('station_id')->label('Station')
                ->options(Station::pluck('name', 'id')->toArray())
                ->preload()->searchable()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('menu.name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('selling_price')
                ->prefix('Ksh. ')->numeric()->searchable()->sortable(),
            Tables\Columns\TextColumn::make('portions_to_prepare')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('article.name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('station.name')->searchable()
                ->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->actions([
            Tables\Actions\ViewAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            PortionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
            'view' => Pages\ViewMenuItem::route('/{record}/view'),
        ];
    }
}
