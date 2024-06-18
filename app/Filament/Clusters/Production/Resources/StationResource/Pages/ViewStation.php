<?php

namespace App\Filament\Clusters\Production\Resources\StationResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource\Pages\ViewReview;
use App\Filament\Clusters\Production\Resources\StationResource;
use Filament\Actions\EditAction;

class ViewStation extends ViewReview
{
    protected static string $resource = StationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
