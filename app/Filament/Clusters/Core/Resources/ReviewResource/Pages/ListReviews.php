<?php

namespace App\Filament\Clusters\Core\Resources\ReviewResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
