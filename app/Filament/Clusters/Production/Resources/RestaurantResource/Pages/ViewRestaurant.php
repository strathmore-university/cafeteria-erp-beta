<?php

namespace App\Filament\Clusters\Production\Resources\RestaurantResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource\Pages\ViewReview;
use App\Filament\Clusters\Production\Resources\RestaurantResource;
use Filament\Actions\EditAction;

class ViewRestaurant extends ViewReview
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
