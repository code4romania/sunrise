<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\ResultResource\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\ResultResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateResult extends CreateRecord
{
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = ResultResource::class;

    protected static bool $canCreateAnother = false;

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('create') => __('nomenclature.headings.add_result'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.headings.add_result');
    }

    protected function getRedirectUrl(): string
    {
        return self::$resource::getUrl();
    }

    public function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(ResultResource::getUrl()),
        ];
    }
}
