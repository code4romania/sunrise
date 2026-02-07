<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\BeneficiaryIdentityFormSchema;
use App\Models\Beneficiary;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditCaseIdentity extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_identity.title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record->getBreadcrumb(),
            CaseResource::getUrl('identity', ['record' => $record]) => __('beneficiary.page.identity.title'),
            '' => __('beneficiary.page.edit_identity.title'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('identity', ['record' => $this->getRecord()])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return CaseResource::getUrl('identity', [
            'record' => $this->getRecord(),
            'tab' => '-'.str(\Illuminate\Support\Str::slug(__('beneficiary.section.identity.tab.beneficiary')))->append('-tab')->toString(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $record = $this->getRecord();

        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema(BeneficiaryIdentityFormSchema::getSchema($record instanceof Beneficiary ? $record : null)),
            ]);
    }
}
