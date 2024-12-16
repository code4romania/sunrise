<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = UserResource::class;

    protected static bool $canCreateAnother = false;
}
