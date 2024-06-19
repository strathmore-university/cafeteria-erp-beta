<?php

namespace App\Filament\Clusters\Inventory\Resources\ArticleResource\Pages;

use App\Filament\Clusters\Inventory\Resources\ArticleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArticle extends ViewRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
