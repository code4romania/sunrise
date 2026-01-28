<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use Filament\Schemas\Schema;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToCloseFile;
use App\Enums\AdmittanceReason;
use App\Enums\CloseMethod;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditCloseFileGeneralDetails extends EditRecord
{
    use RedirectToCloseFile;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.close_file.titles.edit_general_details');
    }

    public function getBreadcrumbs(): array
    {
        return array_merge(
            BeneficiaryBreadcrumb::make($this->getRecord())
                ->getBreadcrumbs('view_close_file'),
            [__('beneficiary.section.close_file.headings.general_details')]
        );
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.close_file.headings.general_details'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->maxWidth('3xl')
                ->relationship('closeFile')
                ->schema($this->getFormSchema()),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            CheckboxList::make('admittance_reason')
                ->label(__('beneficiary.section.close_file.labels.admittance_reason'))
                ->options(AdmittanceReason::options()),

            TextInput::make('admittance_details')
                ->label(__('beneficiary.section.close_file.labels.admittance_details'))
                ->placeholder(__('beneficiary.section.close_file.placeholders.admittance_details'))
                ->maxLength(100),

            Radio::make('close_method')
                ->label(__('beneficiary.section.close_file.labels.close_method'))
                ->options(CloseMethod::options())
                ->live(),

            TextInput::make('institution_name')
                ->label(__('beneficiary.section.close_file.labels.institution_name'))
                ->placeholder(__('beneficiary.section.close_file.placeholders.institution_name'))
                ->visible(fn (Get $get) => CloseMethod::isValue($get('close_method'), CloseMethod::TRANSFER_TO))
                ->maxLength(100),

            TextInput::make('beneficiary_request')
                ->label(__('beneficiary.section.close_file.labels.beneficiary_request'))
                ->placeholder(__('beneficiary.section.close_file.placeholders.add_details'))
                ->visible(fn (Get $get) => CloseMethod::isValue($get('close_method'), CloseMethod::BENEFICIARY_REQUEST))
                ->maxLength(100),

            TextInput::make('other_details')
                ->label(__('beneficiary.section.close_file.labels.other_details'))
                ->placeholder(__('beneficiary.section.close_file.placeholders.add_details'))
                ->visible(fn (Get $get) => CloseMethod::isValue($get('close_method'), CloseMethod::OTHER))
                ->maxLength(100),

            RichEditor::make('close_situation')
                ->label(__('beneficiary.section.close_file.labels.close_situation'))
                ->placeholder(__('beneficiary.section.close_file.placeholders.close_situation'))->columnSpanFull()
                ->maxLength(2500),

        ];
    }
}
