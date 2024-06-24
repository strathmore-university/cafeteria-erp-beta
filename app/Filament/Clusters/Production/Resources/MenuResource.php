<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\MenuResource\Pages\CreateMenu;
use App\Filament\Clusters\Production\Resources\MenuResource\Pages\EditMenu;
use App\Filament\Clusters\Production\Resources\MenuResource\Pages\ListMenus;
use App\Filament\Clusters\Production\Resources\MenuResource\Pages\ViewMenu;
use App\Filament\Clusters\Production\Resources\MenuResource\RelationManagers\ItemsRelationManager;
use App\Models\Production\Menu;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $cluster = Production::class;

    protected static ?string $slug = 'menus';

    protected static ?string $model = Menu::class;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->string()->maxLength(255),
            TextInput::make('owner_id')->label('Recipe for')
                ->formatStateUsing(fn (Menu $record) => $record->ownerName())
                ->required()->string()->maxLength(255),
            Select::make('active_day')->required()->options([
                'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday',
                'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday',
            ]),
            DatePicker::make('active_date')->required()
                ->hidden(fn ($record) => filled($record->active_day))
                ->rules('after:yesterday'),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('owner.name')->searchable()->sortable()
                ->formatStateUsing(fn (Menu $record) => $record->ownerName()),
            TextColumn::make('active_day')->searchable()->sortable(),
            TextColumn::make('active_date')->searchable()->sortable(),
            IconColumn::make('is_active')->boolean(),
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
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
            'view' => ViewMenu::route('/{record}/view'),
        ];
    }
}
