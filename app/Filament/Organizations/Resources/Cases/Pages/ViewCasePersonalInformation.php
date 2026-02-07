<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\PersonalInfoInfolist;
use App\Models\Beneficiary;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewCasePersonalInformation extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.personal_information.title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        $breadcrumbs = [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
        ];

        if ($record instanceof Beneficiary) {
            $breadcrumbs[CaseResource::getUrl('view', ['record' => $record])] = $record->getBreadcrumb();
        }

        $breadcrumbs[''] = __('beneficiary.page.personal_information.title');

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $record])),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return PersonalInfoInfolist::configure($schema);
    }
}
