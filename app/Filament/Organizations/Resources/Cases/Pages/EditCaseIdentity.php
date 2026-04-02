<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Enums\AddressType;
use App\Filament\Organizations\Concerns\InteractsWithBeneficiaryDetailsPanel;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\BeneficiaryIdentityFormSchema;
use App\Models\Beneficiary;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditCaseIdentity extends EditRecord
{
    use InteractsWithBeneficiaryDetailsPanel;
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        if (! $record instanceof Beneficiary) {
            return $data;
        }

        $record->loadMissing(['legal_residence', 'effective_residence']);

        if ($record->legal_residence) {
            $data['legal_residence'] = array_merge(
                $data['legal_residence'] ?? [],
                $record->legal_residence->only([
                    'county_id', 'city_id', 'address', 'environment',
                ])
            );
        }

        if ($record->effective_residence) {
            $data['effective_residence'] = array_merge(
                $data['effective_residence'] ?? [],
                $record->effective_residence->only([
                    'county_id', 'city_id', 'address', 'environment',
                ])
            );
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['legal_residence'], $data['effective_residence']);

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        if (! $record instanceof Beneficiary) {
            return;
        }

        $state = $this->form->getState();
        $legalData = $state['legal_residence'] ?? [];
        $effectiveData = $state['effective_residence'] ?? [];
        $sameAsLegal = (bool) ($state['same_as_legal_residence'] ?? false);

        if ($this->hasAddressData($legalData)) {
            $attrs = array_merge(
                $this->buildAddressAttributes($legalData, AddressType::LEGAL_RESIDENCE),
                ['address_type' => AddressType::LEGAL_RESIDENCE]
            );
            $record->legal_residence()->updateOrCreate([], $attrs);
        } else {
            $record->legal_residence?->delete();
        }

        $effectivePayload = $sameAsLegal ? $legalData : $effectiveData;
        if ($this->hasAddressData($effectivePayload)) {
            $attrs = array_merge(
                $this->buildAddressAttributes($effectivePayload, AddressType::EFFECTIVE_RESIDENCE),
                ['address_type' => AddressType::EFFECTIVE_RESIDENCE]
            );
            $record->effective_residence()->updateOrCreate([], $attrs);
        } else {
            $record->effective_residence?->delete();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function hasAddressData(array $data): bool
    {
        $countyId = $data['county_id'] ?? null;
        $cityId = $data['city_id'] ?? null;
        $address = $data['address'] ?? null;

        return $countyId !== null || $cityId !== null || filled($address);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildAddressAttributes(array $data, AddressType $type): array
    {
        $attrs = [
            'country_id' => $data['country_id'] ?? null,
            'county_id' => $data['county_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'address' => $data['address'] ?? null,
            'environment' => $data['environment'] ?? null,
        ];

        return array_filter($attrs, fn ($v) => $v !== null && $v !== '');
    }
}
