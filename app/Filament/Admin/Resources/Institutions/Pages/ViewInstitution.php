<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Pages;

use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewInstitution extends ViewRecord
{
    protected static string $resource = InstitutionResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('institution.headings.institution_details');
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        $record = $this->getRecord();
        if ($record->isActivated()) {
            $actions[] = Action::make('inactivate')
                ->label(__('institution.actions.inactivate'))
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('institution.actions.inactivate'))
                ->modalDescription(__('institution.labels.inactivate'))
                ->action(fn () => $record->inactivate())
                ->after(fn () => $this->redirect(InstitutionResource::getUrl('view', ['record' => $record])));
        }

        return $actions;
    }
}
