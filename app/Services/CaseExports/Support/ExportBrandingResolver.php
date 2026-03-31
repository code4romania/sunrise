<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Support;

use App\Models\Organization;
use Filament\Facades\Filament;

class ExportBrandingResolver
{
    /**
     * @return array{name:string,header_url:?string,logo_url:?string,app_name:string,printed_at:string}
     */
    public function resolve(): array
    {
        /** @var Organization|null $tenant */
        $tenant = Filament::getTenant();

        return [
            'name' => $tenant?->name ?? config('app.name'),
            'header_url' => $tenant?->getFirstMediaUrl('organization_header') ?: null,
            'logo_url' => $tenant?->getFirstMediaUrl('logo') ?: null,
            'app_name' => 'Management de caz VD',
            'printed_at' => now()->format('d/m/Y'),
        ];
    }
}
