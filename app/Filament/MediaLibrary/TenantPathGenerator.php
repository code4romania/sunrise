<?php

declare(strict_types=1);

namespace App\Filament\MediaLibrary;

use App\Models\Organization;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class TenantPathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $ulid = filament()->getTenant()?->ulid;

        $media->load('model');
        if (! $ulid && $media->model instanceof Organization) {
            $ulid = $media->model->ulid;
        }

        return collect(['org', $ulid, $media->getKey()])
            ->filter()
            ->join(\DIRECTORY_SEPARATOR);
    }
}
