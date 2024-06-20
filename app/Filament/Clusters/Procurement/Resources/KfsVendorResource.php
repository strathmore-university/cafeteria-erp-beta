<?php

namespace App\Filament\Clusters\Procurement\Resources;

use App\Filament\Clusters\Procurement;
use App\Filament\Clusters\Procurement\Resources\KfsVendorResource\Pages\ListKfsVendors;
use App\Models\Procurement\KfsVendor;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KfsVendorResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Procurement::class;

    protected static ?string $model = KfsVendor::class;

    protected static ?string $slug = 'kfs-vendors';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('vendor_number')->searchable()->sortable(),
            TextColumn::make('vendor_name')->searchable()->sortable(),
            TextColumn::make('pre_format_code')->searchable()->sortable(),
            TextColumn::make('pre_format_description')->searchable()->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKfsVendors::route('/'),
        ];
    }
}
