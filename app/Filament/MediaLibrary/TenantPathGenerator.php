<?php

declare(strict_types=1);

namespace App\Filament\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class TenantPathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        return collect([
            filament()->getTenant()?->ulid,
            $media->getKey(),
        ])
            ->filter()
            ->join(\DIRECTORY_SEPARATOR);
    }
}
