<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Utilities\Get;

class CaseTeamFormSchema
{
    public const NO_OTHER_ROLE_VALUE = 'no_other_role';

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
                ->hiddenLabel()
                ->live()
                ->disableOptionWhen(fn (Get $get, string $value): bool => self::shouldDisableOption($get('case_team') ?? [], $value))
                ->required()
                ->columns(1),
        ];
    }

    /**
     * Determine whether a specific option should be disabled based on current selected values.
     *
     * - If the special "no other role" option is selected, all other options should be disabled
     *   (only the special option stays active).
     * - If any other option is selected, the special option should be disabled.
     * - Otherwise, no options are disabled.
     */
    public static function shouldDisableOption(array|null $selected, string $value): bool
    {
        $selected = $selected ?? [];
        if (!\is_array($selected)) {
            $selected = [];
        }

        $special = self::NO_OTHER_ROLE_VALUE;
        $specialSelected = \in_array($special, $selected, true);

        $otherSelected = (bool) count(array_filter($selected, fn ($v) => $v !== $special));

        $disabled = false;

        if ($specialSelected) {
            $disabled = $value !== $special;
        } elseif ($otherSelected) {
            $disabled = $value === $special;
        }

        return $disabled;
    }
}
