<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Models\Role;
use Filament\Forms\Components\CheckboxList;

class CaseTeamFormSchema
{
    public const NO_OTHER_ROLE_VALUE = 'no_other_role';

    /**
     * Schema for case team step in create wizard: only roles (assignment = current user).
     *
     * @return array<int, mixed>
     */
    public static function getSchemaForCreateWizard(): array
    {
        $roleOptions = Role::query()
            ->active()
            ->orderBy('sort')
            ->pluck('name', 'id')
            ->all();

        $options = $roleOptions + [self::NO_OTHER_ROLE_VALUE => __('beneficiary.section.specialists.labels.without_role')];

        return [
            CheckboxList::make('case_team')
                ->options($options)
                ->required()
                ->columns(1),
        ];
    }
}
