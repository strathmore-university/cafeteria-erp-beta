<?php

namespace App\Filament\Clusters\Core\Resources\ReviewResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reviewed-record')
                ->url(fn ($record) => get_record_url($record)),
        ];
    }
}
