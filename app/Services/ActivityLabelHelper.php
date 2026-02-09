<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ActivityDescription;
use App\Models\Activity;

class ActivityLabelHelper
{
    public static function getEventLabel(Activity $record): string
    {
        $description = __('beneficiary.section.history.labels.beneficiary');
        if (ActivityDescription::tryFrom($record->event)) {
            return $description;
        }

        return $description.', '.__('beneficiary.section.history.labels.'.$record->event);
    }

    public static function getSubsectionLabel(Activity $record): string
    {
        if ($record->event !== $record->description->value) {
            return '';
        }

        if ($record->description !== ActivityDescription::UPDATED) {
            return '';
        }

        $changedFields = array_merge(
            array_keys($record->properties->get('old', [])),
            array_keys($record->properties->get('attributes', []))
        );

        $identityFields = [
            'last_name',
            'first_name',
            'prior_name',
            'civil_status',
            'cnp',
            'gender',
            'birthdate',
            'birthplace',
            'citizenship',
            'ethnicity',
            'id_serial',
            'id_number',
            'legal_residence_county_id',
            'legal_residence_city_id',
            'legal_residence_environment',
            'legal_residence_address',
            'effective_residence_county_id',
            'effective_residence_city_id',
            'effective_residence_environment',
            'effective_residence_address',
            'primary_phone',
            'backup_phone',
            'email',
            'contact_notes',
            'studies',
            'occupation',
            'workplace',
            'children',
        ];

        if (array_intersect($identityFields, $changedFields)) {
            return __('beneficiary.page.identity.title');
        }

        return __('beneficiary.page.personal_information.title');
    }
}
