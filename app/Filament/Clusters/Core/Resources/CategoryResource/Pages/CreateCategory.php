<?php

namespace App\Filament\Clusters\Core\Resources\CategoryResource\Pages;

use App\Filament\Clusters\Core\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
