<?php

namespace App\Filament\Clusters\Procurement\Resources\KfsVendorResource\Pages;

use App\Filament\Clusters\Procurement\Resources\KfsVendorResource;
use App\Models\Procurement\KfsVendor;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListKfsVendors extends ListRecords
{
    protected static string $resource = KfsVendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retrieve_a_vendor_from_kfs')->form([
                TextInput::make('vendor_number')
                    ->placeholder('xxxxx')
                    ->maxLength(5)
                    ->required()
                    ->string(),
            ])->button()->action(
                fn (array $data) => KfsVendor::retrieve($data['vendor_number'])
            ),
            Action::make('refresh_kfs_vendors')
                ->action(fn (array $data) => KfsVendor::refreshEntries())
                ->label('Refresh KFS Vendors')
//                ->message('Are you sure you want to refresh KFS Vendors? This action will fetch all vendors from KFS and update the database.')
                ->requiresConfirmation()
//                ->visible(false)
                ->color('danger')
                ->button(),
        ];
    }
}
