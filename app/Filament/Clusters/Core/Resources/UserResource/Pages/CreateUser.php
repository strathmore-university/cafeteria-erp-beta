<?php

namespace App\Filament\Clusters\Core\Resources\UserResource\Pages;

use App\Filament\Clusters\Core\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
