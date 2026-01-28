<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Specialist;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;

class CreateCloseFile extends EditRecord
{
    use HasWizard;
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

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

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    public function beforeFill(): void
    {
        $this->prefillFormData = [
            'date' => now()->format('d.m.Y'),
            'admittance_date' => $this->getRecord()->created_at->format('d.m.Y'),
            'exit_date' => now()->format('d.m.Y'),
            'specialist_id' => $this->getRecord()
                ->specialistsTeam
                ->loadMissing('role:id,case_manager')
                ->filter(fn (Specialist $item) => $item->role?->case_manager)
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
            ->label(__('general.action.finish'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }
}
