<?php

namespace App\Filament\Clusters\Procurement\Resources;

use App\Filament\Clusters\Procurement;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages\CreateSupplier;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages\EditSupplier;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages\ListSuppliers;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\RelationManagers\PurchaseOrdersRelationManager;
use App\Models\Procurement\Supplier;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupplierResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Procurement::class;

    protected static ?string $model = Supplier::class;

    protected static ?string $slug = 'suppliers';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Bio')->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('description')->required(),
                    TextInput::make('email')->required(),
                    TextInput::make('phone_number')->required(),
                    TextInput::make('address')->required(),
                ]),
                Section::make('KFS Info')->schema([
                    TextInput::make('kfs_vendor_number')
                        ->hint('Fill this out to automatically fetch the preformat from KFS.'),
                    TextInput::make('kfs_preformat_code'),
                    TextInput::make('kfs_preformat_description'),
                    TextInput::make('percentage_vat'),
                ]),
                common_fields(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('phone_number'),
                TextColumn::make('kfs_vendor_number'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getRelations(): array
    {
        return [
            PurchaseOrdersRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
