<?php

namespace App\Filament\Clusters\Production\Resources\ProductDispatchResource\Pages;

use App\Filament\Clusters\Production\Resources\ProductDispatchResource;
use App\Models\Inventory\Store;
use App\Models\Production\ProductDispatch;
use App\Models\Production\Restaurant;
use App\Models\Production\Station;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListProductDispatches extends ListRecords
{
    protected static string $resource = ProductDispatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Create product dispatch')->form([
                Select::make('destination_id')
                    ->options(Restaurant::pluck('name', 'id')->toArray())
                    ->label('Destination')
                    ->required(),
                Select::make('from_store_id')
                    ->options($this->from())
                    ->label('From')
                    ->searchable()
                    ->required()
                    ->preload(),
            ])
                ->action(fn (array $data) => $this->create($data))
                ->slideOver(),
        ];
    }

    private function from(): array
    {
        return Store::whereOwnerType(Station::class)
            ->pluck('name', 'id')
            ->toArray();
    }

    private function create(array $data): void
    {
        $restaurant = Restaurant::find($data['destination_id']);
        $store = $restaurant->defaultStore();

        $dispatch = ProductDispatch::create([
            'from_store_id' => $data['from_store_id'],
            'destination_type' => Restaurant::class,
            'destination_id' => $restaurant->id,
            'dispatched_by' => auth_id(),
            'to_store_id' => $store->id,
            'status' => 'draft',
        ]);

        success();
        $this->redirect(get_record_url($dispatch), true);
    }
}
