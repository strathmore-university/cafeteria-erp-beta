<?php

namespace App\Filament\Clusters\Procurement\Resources;

use App\Filament\Clusters\Procurement;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages\CreateSupplier;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages\EditSupplier;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages\ListSuppliers;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages\ViewSupplier;
use App\Filament\Clusters\Procurement\Resources\SupplierResource\RelationManagers\PurchaseOrdersRelationManager;
use App\Models\Procurement\KfsVendor;
use App\Models\Procurement\Supplier;
use Exception;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
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

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form
            ->schema([
                Section::make('Supplier Info')->schema([
                    TextInput::make('name')->required()->string()->maxLength(255),
                    TextInput::make('phone_number')->required()->maxLength(20),
                    TextInput::make('email')->email()->nullable()->maxLength(255),
                    TextInput::make('address')->required()->string()->maxLength(255),
                    TextInput::make('description')->nullable()->maxLength(255)->columnSpan(2),
                ])->columns($cols),
                Section::make('KFS Info')->schema([
                    TextInput::make('kfs_vendor_number')->reactive()
                        ->afterStateUpdated(function (string $state, Set $set): void {
                            try {
                                $kfsVendor = KfsVendor::whereVendorNumber($state)->firstOrFail();

                                $value = $kfsVendor->pre_format_description;
                                $set('kfs_preformat_description', $value);
                                $set('kfs_preformat_code', $kfsVendor->pre_format_code);
                                $set('kfs_vendor_id', $kfsVendor->id);
                            } catch (Exception $exception) {
                                $set('kfs_preformat_description', null);
                                $set('kfs_preformat_code', null);
                                $set('kfs_vendor_id', null);

                                error_notification($exception);
                            }
                        }),
                    TextInput::make('percentage_vat')->required()->numeric()
                        ->maxValue(100)->minValue(0),
                    Hidden::make('kfs_vendor_id'),
                    TextInput::make('kfs_preformat_code')->readOnly(),
                    TextInput::make('kfs_preformat_description')->readOnly(),
                    Toggle::make('is_active')->default(true),
                ])->columns($cols),
                common_fields(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('phone_number'),
                TextColumn::make('email'),
                TextColumn::make('kfs_vendor_number'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->actions([ViewAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
            'view' => ViewSupplier::route('/{record}/view'),
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
