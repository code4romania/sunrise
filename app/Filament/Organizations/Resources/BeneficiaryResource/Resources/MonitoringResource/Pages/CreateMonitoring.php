<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Resources\MonitoringResource\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\BeneficiaryResource\Resources\MonitoringResource;
use App\Models\Monitoring;
use App\Models\Specialist;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class CreateMonitoring extends CreateRecord
{
    use HasWizard;
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = MonitoringResource::class;

    public ?Monitoring $lastFile;

    public array $children = [];

    public array $specialistTeam = [];

    public function getBreadcrumbs(): array
    {
        $breadcrumb = __('monitoring.breadcrumbs.file', ['file_number' => null]);
        $ownerRecord = $this->getParentRecord();

        return array_merge(
            BeneficiaryBreadcrumb::make($ownerRecord)
                ->getBreadcrumbsForCreateMonitoring(),
            [$breadcrumb],
        );
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.create');
    }

    protected function getRedirectUrl(): string
    {
        $parentRecord = $this->getParentRecord();

        return static::getResource()::getUrl('view', [
            'beneficiary' => $parentRecord,
            'record' => $this->record,
        ]);
    }

    protected function getHeaderActions(): array
    {
        $parentRecord = $this->getParentRecord();

        return [
            BackAction::make()
                ->url(static::getResource()::getUrl('index', ['beneficiary' => $parentRecord])),
        ];
    }

    protected function afterFill(): void
    {
        $copyLastFile = (bool) request('copyLastFile');
        $ownerRecord = $this->getParentRecord();
        $this->lastFile = $ownerRecord
            ->monitorings
            ->sortByDesc('id')
            ->first()
            ?->loadMissing(['children', 'specialistsTeam']);

        $this->children = $this->getChildren();
        foreach ($this->children as &$child) {
            $child['birthdate'] = $child['birthdate'] ? Carbon::parse($child['birthdate'])->format('d.m.Y') : null;
        }

        $this->specialistTeam = $this->getSpecialists();

        $data = [
            'date' => now(),
        ];

        if ($copyLastFile && $this->lastFile) {
            $data = array_merge($data, $this->lastFile->toArray());
        }
        $this->form->fill($data);
    }

    public function getSteps(): array
    {
        return [

            Step::make(__('monitoring.headings.details'))
                ->schema(EditDetails::getFormSchemaStatic())
                ->afterStateHydrated(fn (Set $set) => $set('specialistsTeam', $this->specialistTeam)),

            Step::make(__('monitoring.headings.child_info'))
                ->schema(EditChildren::getFormSchemaStatic())
                ->afterStateHydrated(function (Set $set) {
                    $set('children', $this->children);
                }),

            Step::make(__('monitoring.headings.general'))
                ->schema(EditGeneral::getFormSchemaStatic()),

        ];
    }

    protected function getSubmitFormAction(): Action
    {
        return Action::make('create')
            ->label(__('general.action.finish'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from($this->previousUrl ?? static::getResource()::getUrl('index', ['beneficiary' => $this->getParentRecord()])) . ')')
            ->color('gray');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the parent relationship key to the parent resource's ID.
        $data['beneficiary_id'] = $this->getParentRecord()->id;

        return $data;
    }

    private function getChildren(): array
    {
        $ownerRecord = $this->getParentRecord();

        if ($this->lastFile && $this->lastFile->children->isNotEmpty()) {
            return $this->lastFile->children->toArray();
        }

        return $ownerRecord->children->toArray();
    }

    private function getSpecialists(): array
    {
        $ownerRecord = $this->getParentRecord();

        if ($this->lastFile && $this->lastFile->specialistsTeam->isNotEmpty()) {
            return $this->lastFile
                ->specialistsTeam
                ->filter(fn (Specialist $specialist) => $specialist->role_id)
                ->toArray();
        }

        return $ownerRecord
            ->specialistsTeam
            ?->filter(fn (Specialist $specialist) => $specialist->role_id)
            ->each(
                fn (Specialist $item) => $item->specialistable_type = (new Monitoring())->getMorphClass()
            )
            ->toArray();
    }
}
