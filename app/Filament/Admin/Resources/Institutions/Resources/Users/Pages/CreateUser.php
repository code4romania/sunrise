<?php

namespace App\Filament\Admin\Resources\Institutions\Resources\Users\Pages;

use App\Filament\Admin\Resources\Institutions\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
