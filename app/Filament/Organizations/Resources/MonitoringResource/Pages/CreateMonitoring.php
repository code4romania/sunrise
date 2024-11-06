<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Models\Monitoring;
use App\Models\Specialist;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class CreateMonitoring extends CreateRecord
{
    use HasWizard;
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    public ?Monitoring $lastFile;

    public array $children = [];

    public array $specialistTeam = [];

    public function getBreadcrumbs(): array
    {
        $breadcrumb = __('monitoring.breadcrumbs.file', ['file_number' => null]);

        return array_merge(
            BeneficiaryBreadcrumb::make($this->parent)
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
        return static::getParentResource()::getUrl('monitorings.view', [
            'parent' => $this->parent,
            'record' => $this->record,
        ]);
    }

    protected function configureAction(Action $action): void
    {
        $action->hidden();
    }

    protected function afterFill(): void
    {
        $copyLastFile = (bool) request('copyLastFile');
        $this->lastFile = self::getParent()
            ?->monitoring
            ->sortByDesc('id')
            ->first()
            ?->load(['children', 'specialistsTeam']);
        $this->children = $this->getChildren();
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

            Wizard\Step::make(__('monitoring.headings.details'))
                ->schema(EditDetails::getSchema())
                ->afterStateHydrated(fn (Set $set) => $set('specialistsTeam', $this->specialistTeam)),

            Wizard\Step::make(__('monitoring.headings.child_info'))
                ->schema(EditChildren::getSchema())
                ->afterStateHydrated(function (Set $set) {
                    $set('children', $this->children);
                }),

            Wizard\Step::make(__('monitoring.headings.general'))
                ->schema(EditGeneral::getSchema()),

        ];
    }

    protected function getSubmitFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = ' . Js::from($this->previousUrl ?? static::getParentResource()::getUrl('monitorings.index', ['parent' => $this->parent])) . ')')
            ->color('gray');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the parent relationship key to the parent resource's ID.
        $data[$this->getParentRelationshipKey()] = $this->parent->id;

        return $data;
    }

    private function getChildren(): array
    {
        if ($this->lastFile && $this->lastFile->children->isNotEmpty()) {
            return $this->lastFile->children->toArray();
        }

        return $this->parent->children->toArray();
    }

    private function getSpecialists(): array
    {
        if ($this->lastFile && $this->lastFile->specialistsTeam->isNotEmpty()) {
            return $this->lastFile->specialistsTeam->toArray();
        }

        return $this->parent
            ->specialistsTeam
            ?->each(
                fn (Specialist $item) => $item->specialistable_type = (new Monitoring())->getMorphClass()
            )
            ->toArray();
    }
}
