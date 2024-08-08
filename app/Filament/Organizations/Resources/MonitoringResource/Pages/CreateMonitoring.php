<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CreateMonitoring extends CreateRecord
{
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        $breadcrumb = __('beneficiary.section.monitoring.breadcrumbs.file', ['file_number' => null]);

        return array_merge(
            BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoring(),
            [$breadcrumb],
        );
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.monitoring.titles.create');
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

    public function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make()
                ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                    <x-filament::button
                        type="submit"
                        size="sm"
                    >
                        {{__('filament-panels::resources/pages/create-record.form.actions.create.label')}}
                    </x-filament::button>
                BLADE)))
                ->columnSpanFull()
                ->steps([
                    Wizard\Step::make(__('beneficiary.section.monitoring.headings.details'))
                        ->schema(EditDetails::getSchema()),

                    Wizard\Step::make(__('beneficiary.section.monitoring.headings.child_info'))
                        ->schema(EditChildren::getSchema()),

                    Wizard\Step::make(__('beneficiary.section.monitoring.headings.general'))
                        ->schema(EditGeneral::getSchema()),

                ]),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the parent relationship key to the parent resource's ID.
        $data[$this->getParentRelationshipKey()] = $this->parent->id;

        return $data;
    }
}
