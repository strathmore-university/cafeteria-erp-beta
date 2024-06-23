<?php

namespace App\Filament\Clusters\Production\Resources\MenuItemResource\Pages;

use App\Filament\Clusters\Production\Resources\MenuItemResource;
use Filament\Resources\Pages\ListRecords;

class ListMenuItems extends ListRecords
{
    protected static string $resource = MenuItemResource::class;
}
