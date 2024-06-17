<?php

namespace App\Filament\Clusters\Core\Resources\ReviewResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
