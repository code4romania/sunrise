<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use App\Enums\Role;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\CaseTeam;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateCloseFile extends EditRecord
{
    use HasWizard;

    protected static string $resource = BeneficiaryResource::class;

    private array $prefillFormData = [];

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.close_file.titles.create');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())->getBreadcrumbsCreateCloseFile();
    }

    protected function getRedirectUrl(): ?string
    {
        return self::getResource()::getUrl('view_close_file', ['record' => $this->getRecord()]);
    }

    public function beforeFill(): void
    {
        $this->prefillFormData = [
            'date' => now()->format('Y-m-d'),
            'admittance_date' => $this->getRecord()->created_at->format('Y-m-d'),
            'exit_date' => now()->format('Y-m-d'),
            'case_team_id' => $this->getRecord()
                ->team
                ->filter(
                    fn (CaseTeam $item) => $item->roles
                        ?->filter(fn (Role $role) => Role::isValue($role, Role::MANGER))
                        ->count()
                )
                ->first()
                ?->id,
        ];
    }

    protected function getSteps(): array
    {
        return [
            Step::make(__('beneficiary.section.close_file.headings.file_details'))
                ->schema([
                    Group::make()
                        ->label(__('beneficiary.section.close_file.headings.file_details_simple'))
                        ->maxWidth('3xl')
                        ->columns()
                        ->relationship('closeFile')
                        ->schema(EditCloseFileDetails::getSchema($this->getRecord())),
                ])
                ->afterStateHydrated(function (Set $set) {
                    $set('closeFile.date', $this->prefillFormData['date']);
                    $set('closeFile.admittance_date', $this->prefillFormData['admittance_date']);
                    $set('closeFile.exit_date', $this->prefillFormData['exit_date']);
                    $set('closeFile.case_team_id', $this->prefillFormData['case_team_id']);
                }),

            Step::make(__('beneficiary.section.close_file.headings.general_details'))
                ->schema([
                    Group::make()
                        ->maxWidth('3xl')
                        ->label(__('beneficiary.section.close_file.labels.general_details'))
                        ->relationship('closeFile')
                        ->schema(EditCloseFileGeneralDetails::getSchema()),
                ]),
        ];
    }

    protected function getSubmitFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }
}
