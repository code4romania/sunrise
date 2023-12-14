<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Filament\Organizations\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
