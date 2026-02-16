<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation;

use App\Enums\AddressType;
use App\Models\Beneficiary;
use App\Models\BeneficiaryPartner;

trait SavesPartnerAddresses
{
    /**
     * Partner address data captured before save (form state is cleared of these after save).
     *
     * @var array{legal_residence?: array, effective_residence?: array, same_as_legal_residence?: bool}|null
     */
    protected ?array $pendingPartnerAddressData = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFillPartner(array $data): array
    {
        $record = $this->getRecord();
        if (! $record instanceof Beneficiary) {
            return $data;
        }

        $partner = $record->partner;
        if (! $partner) {
            return $data;
        }

        $partner->loadMissing(['legal_residence', 'effective_residence']);
        $partnerData = $data['partner'] ?? [];

        if ($partner->legal_residence) {
            $partnerData['legal_residence'] = array_merge(
                $partnerData['legal_residence'] ?? [],
                $partner->legal_residence->only(['county_id', 'city_id', 'address'])
            );
        }
        if ($partner->effective_residence) {
            $partnerData['effective_residence'] = array_merge(
                $partnerData['effective_residence'] ?? [],
                $partner->effective_residence->only(['county_id', 'city_id', 'address'])
            );
        }

        $data['partner'] = $partnerData;

        return $data;
    }

    /**
     * Capture partner address data from form state and strip it from $data so it is not saved on the partner model.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function capturePartnerAddressDataBeforeSave(array $data): array
    {
        $rawState = method_exists($this->form, 'getRawState') ? $this->form->getRawState() : (array) $this->data;
        $partnerFromState = $data['partner'] ?? $rawState['partner'] ?? [];
        $partnerFromLivewire = $rawState['partner'] ?? $this->data['partner'] ?? [];
        $legal = $partnerFromState['legal_residence'] ?? $partnerFromLivewire['legal_residence'] ?? [];
        $effective = $partnerFromState['effective_residence'] ?? $partnerFromLivewire['effective_residence'] ?? [];
        $this->pendingPartnerAddressData = [
            'legal_residence' => is_array($legal) ? $legal : [],
            'effective_residence' => is_array($effective) ? $effective : [],
            'same_as_legal_residence' => (bool) ($partnerFromState['same_as_legal_residence'] ?? $partnerFromLivewire['same_as_legal_residence'] ?? false),
        ];

        $partnerData = $data['partner'] ?? [];
        unset($partnerData['legal_residence'], $partnerData['effective_residence']);
        $data['partner'] = $partnerData;

        return $data;
    }

    protected function afterSavePartnerAddresses(): void
    {
        $record = $this->getRecord();
        if (! $record instanceof Beneficiary) {
            return;
        }

        $partnerRecord = $record->partner;
        if ($partnerRecord && $partnerRecord->same_as_legal_residence) {
            $partnerRecord->loadMissing(['legal_residence', 'effective_residence']);
            BeneficiaryPartner::copyLegalResidenceToEffectiveResidence($partnerRecord);
        }

        $this->savePartnerAddressesFromFormState();
    }

    private function savePartnerAddressesFromFormState(): void
    {
        $record = $this->getRecord();
        if (! $record instanceof Beneficiary) {
            return;
        }

        $partner = $record->partner;
        if (! $partner) {
            return;
        }

        $partnerState = $this->pendingPartnerAddressData ?? [];
        $this->pendingPartnerAddressData = null;

        $legalData = $partnerState['legal_residence'] ?? [];
        $effectiveData = $partnerState['effective_residence'] ?? [];
        $sameAsLegal = (bool) ($partnerState['same_as_legal_residence'] ?? false);

        if ($this->hasPartnerAddressData($legalData)) {
            $attrs = array_merge(
                $this->buildPartnerAddressAttributes($legalData, AddressType::LEGAL_RESIDENCE),
                [
                    'address_type' => AddressType::LEGAL_RESIDENCE,
                    'addressable_id' => $partner->getKey(),
                    'addressable_type' => $partner->getMorphClass(),
                ]
            );
            $partner->legal_residence()->updateOrCreate(
                ['addressable_id' => $partner->getKey(), 'addressable_type' => $partner->getMorphClass()],
                $attrs
            );
        } else {
            $partner->legal_residence?->delete();
        }

        $effectivePayload = $sameAsLegal ? $legalData : $effectiveData;
        if ($this->hasPartnerAddressData($effectivePayload)) {
            $attrs = array_merge(
                $this->buildPartnerAddressAttributes($effectivePayload, AddressType::EFFECTIVE_RESIDENCE),
                [
                    'address_type' => AddressType::EFFECTIVE_RESIDENCE,
                    'addressable_id' => $partner->getKey(),
                    'addressable_type' => $partner->getMorphClass(),
                ]
            );
            $partner->effective_residence()->updateOrCreate(
                ['addressable_id' => $partner->getKey(), 'addressable_type' => $partner->getMorphClass()],
                $attrs
            );
        } else {
            $partner->effective_residence?->delete();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function hasPartnerAddressData(array $data): bool
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
    private function buildPartnerAddressAttributes(array $data, AddressType $type): array
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
