<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\IdentityInfolist;
use App\Models\Beneficiary;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ViewCaseIdentity extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.identity.title');
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

        $breadcrumbs[''] = __('beneficiary.page.identity.title');

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $record])),
            Action::make('view_case')
                ->label(__('case.view.identity_page.fab_beneficiary_details'))
                ->icon(Heroicon::OutlinedUser)
                ->url(CaseResource::getUrl('view', ['record' => $record]))
                ->color('primary'),
            Action::make('download_sheet')
                ->label(__('case.view.identity_page.download_sheet'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->url('#'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return IdentityInfolist::configure($schema);
    }
}
