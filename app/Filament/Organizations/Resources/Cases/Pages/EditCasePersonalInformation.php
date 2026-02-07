<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToPersonalInformation;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\PersonalInfoFormSchema;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditCasePersonalInformation extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToPersonalInformation;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_personal_information.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
        ]);
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record->getBreadcrumb(),
            CaseResource::getUrl('view_personal_information', ['record' => $record]) => __('beneficiary.page.personal_information.title'),
            '' => __('beneficiary.page.edit_personal_information.title'),
        ];
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.beneficiary'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema(PersonalInfoFormSchema::getSchema()),
        ]);
    }
}
