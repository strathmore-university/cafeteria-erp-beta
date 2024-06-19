<?php

namespace App\Filament\Clusters\Production\Resources\RecipeResource\Pages;

use App\Filament\Clusters\Production\Resources\RecipeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRecipes extends ListRecords
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
