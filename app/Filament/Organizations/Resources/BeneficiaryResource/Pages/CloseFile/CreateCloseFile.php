<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateCloseFile extends EditRecord
{
    use HasWizard;

    protected static string $resource = BeneficiaryResource::class;

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
                ]),

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
