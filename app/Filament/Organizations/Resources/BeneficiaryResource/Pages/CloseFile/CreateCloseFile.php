<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CreateCloseFile extends EditRecord
{
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
                    Wizard\Step::make(__('beneficiary.section.close_file.headings.file_details'))
                        ->schema([
                            Group::make()
                                ->label(__('beneficiary.section.close_file.headings.file_details_simple'))
                                ->maxWidth('3xl')
                                ->columns()
                                ->relationship('closeFile')
                                ->schema(EditCloseFileDetails::getSchema($this->getRecord())),
                        ]),
                    Wizard\Step::make(__('beneficiary.section.close_file.headings.general_details'))
                        ->schema([
                            Group::make()
                                ->maxWidth('3xl')
                                ->label(__('beneficiary.section.close_file.labels.general_details'))
                                ->relationship('closeFile')
                                ->schema(EditCloseFileGeneralDetails::getSchema()),
                        ]),
                ]),
        ]);
    }
}
