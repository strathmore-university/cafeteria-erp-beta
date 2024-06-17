<?php

namespace App\Filament\Clusters\Inventory\Resources\ArticleResource\Pages;

use App\Filament\Clusters\Inventory\Resources\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
