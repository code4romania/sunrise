<?php

declare(strict_types=1);

use App\Filament\Organizations\Resources\Cases\Schemas\CaseTeamFormSchema;

it('does not disable any option when nothing is selected', function (): void {
    expect(CaseTeamFormSchema::shouldDisableOption([], '1'))->toBeFalse();
    expect(CaseTeamFormSchema::shouldDisableOption([], CaseTeamFormSchema::NO_OTHER_ROLE_VALUE))->toBeFalse();
});

it('disables all other options when the special option is selected', function (): void {
    $selected = [CaseTeamFormSchema::NO_OTHER_ROLE_VALUE];

    expect(CaseTeamFormSchema::shouldDisableOption($selected, '1'))->toBeTrue();
    expect(CaseTeamFormSchema::shouldDisableOption($selected, CaseTeamFormSchema::NO_OTHER_ROLE_VALUE))->toBeFalse();
});

it('disables special option when any other option is selected', function (): void {
    $selected = ['1'];

    expect(CaseTeamFormSchema::shouldDisableOption($selected, CaseTeamFormSchema::NO_OTHER_ROLE_VALUE))->toBeTrue();
    expect(CaseTeamFormSchema::shouldDisableOption($selected, '1'))->toBeFalse();
});

it('when both special and other are present, treat special as selected and disable others', function (): void {
    $selected = [CaseTeamFormSchema::NO_OTHER_ROLE_VALUE, '1'];

    expect(CaseTeamFormSchema::shouldDisableOption($selected, '1'))->toBeTrue();
    expect(CaseTeamFormSchema::shouldDisableOption($selected, CaseTeamFormSchema::NO_OTHER_ROLE_VALUE))->toBeFalse();
});

