<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Support;

use App\Models\Organization;
use Filament\Facades\Filament;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ExportBrandingResolver
{
    /**
     * @return array{name:string,header_url:?string,header_src:?string,logo_url:?string,app_name:string,printed_at:string}
     */
    public function resolve(): array
    {
        /** @var Organization|null $tenant */
        $tenant = Filament::getTenant();
        $headerMedia = $tenant?->getFirstMedia('organization_header');
        $headerUrl = $headerMedia?->getUrl() ?: null;

        return [
            'name' => $tenant?->name ?? config('app.name'),
            'header_url' => $headerUrl,
            'header_src' => $this->buildInlineImageSource($headerMedia) ?? $headerUrl,
            'logo_url' => $tenant?->getFirstMediaUrl('logo') ?: null,
            'app_name' => 'Management de caz VD',
            'printed_at' => now()->format('d/m/Y'),
        ];
    }

    private function buildInlineImageSource(?Media $media): ?string
    {
        if (! $media instanceof Media) {
            return null;
        }

        $path = $media->getPath();
        if ($path === '' || ! is_readable($path)) {
            return null;
        }

        $contents = @file_get_contents($path);
        if ($contents === false || $contents === '') {
            return null;
        }

        $mimeType = $media->mime_type ?: 'image/png';

        return 'data:'.$mimeType.';base64,'.base64_encode($contents);
    }
}
