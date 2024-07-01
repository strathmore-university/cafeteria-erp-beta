<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\SettingResource\Pages\CreateSetting;
use App\Filament\Clusters\Core\Resources\SettingResource\Pages\EditSetting;
use App\Filament\Clusters\Core\Resources\SettingResource\Pages\ListSettings;
use App\Filament\Clusters\Core\Resources\SettingResource\Pages\ViewSetting;
use App\Models\Core\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $model = Setting::class;
    protected static ?string $cluster = Core::class;
    protected static ?string $slug = 'settings';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('group')->required()->string()
                ->maxLength(255),
            TextInput::make('key')->required()->string()
                ->maxLength(255),
            TextInput::make('value')->required()->string()
                ->visible(fn ($record) => !$record->is_encrypted)
                ->maxLength(255),
            TextInput::make('encrypted_value')->required()
                ->visible(fn ($record) => $record->is_encrypted)
                ->string()->maxLength(255),
            Toggle::make('is_encrypted')->required(),
            placeholder('created_at', 'Created at'),
            placeholder('updated_at', 'Last updated at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')->searchable()->sortable(),
                TextColumn::make('key')->searchable()->sortable(),
                IconColumn::make('is_encrypted')
                    ->label('Is Encrypted')->boolean()
            ])->actions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit' => EditSetting::route('/{record}/edit'),
            'view' => ViewSetting::route('/{record}/view'),
        ];
    }
}
