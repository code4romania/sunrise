<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use App\Enums\CasePermission;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Specialist;
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
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('create_close_file');
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
            'specialist_id' => $this->getRecord()
                ->specialistsTeam
                ->load('role')
                ->filter(
                    fn (Specialist $item) => $item->role->case_permissions->contains(CasePermission::CAN_BE_CASE_MANAGER)
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
                    $set('closeFile', $this->prefillFormData);
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

    public function getCloseFilePreFillData(): array
    {
        return [
            'date' => now()->format('Y-m-d'),
            'admittance_date' => $this->getRecord()->created_at->format('Y-m-d'),
            'exit_date' => now()->format('Y-m-d'),
            'user_id' => $this->getRecord()
                ->managerTeam
                ->first()
                ?->user_id,
        ];
    }
}
