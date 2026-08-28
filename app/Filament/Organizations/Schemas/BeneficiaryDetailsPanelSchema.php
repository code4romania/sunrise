<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Schemas;

use App\Filament\Organizations\Support\BeneficiaryDetailsPanelFormatter;
use App\Models\Beneficiary;
use Filament\Infolists\Components\TextEntry;

final class BeneficiaryDetailsPanelSchema
{
    /**
     * @return array<int, TextEntry>
     */
    public static function infolistComponents(): array
    {
        return [
            TextEntry::make('full_name')
                ->label(__('intervention_plan.labels.beneficiary_full_name'))
                ->placeholder('—'),
            TextEntry::make('created_at')
                ->label(__('case.view.case_created_at'))
                ->formatStateUsing(fn (mixed $state): string => BeneficiaryDetailsPanelFormatter::formatDateState($state))
                ->placeholder('—'),
            TextEntry::make('age')
                ->label(__('field.age'))
                ->state(fn (Beneficiary $r): string => $r->age !== null ? (string) $r->age : '—'),
            TextEntry::make('civil_status')
                ->label(__('field.civil_status'))
                ->state(fn (Beneficiary $r): string => BeneficiaryDetailsPanelFormatter::formatEnumLabel($r->civil_status)),
            TextEntry::make('children_total_count')
                ->label(__('field.children_total_count'))
                ->placeholder('—'),
            TextEntry::make('children_under_18_care_count')
                ->label(__('field.children_under_18_care_count'))
                ->placeholder('—'),
            TextEntry::make('legal_residence_city')
                ->label(__('field.legal_residence_city'))
                ->state(fn (Beneficiary $r): string => $r->legal_residence?->city?->name ?? '—'),
            TextEntry::make('effective_residence_city')
                ->label(__('field.effective_residence_city'))
                ->state(fn (Beneficiary $r): string => $r->effective_residence?->city?->name ?? '—'),
            TextEntry::make('details.studies')
                ->label(__('field.studies'))
                ->state(fn (Beneficiary $r): string => BeneficiaryDetailsPanelFormatter::formatEnumLabel($r->details?->studies)),
            TextEntry::make('details.occupation')
                ->label(__('field.occupation'))
                ->state(fn (Beneficiary $r): string => BeneficiaryDetailsPanelFormatter::formatEnumLabel($r->details?->occupation)),
            TextEntry::make('details.net_income')
                ->label(__('field.net_income'))
                ->state(function (Beneficiary $r): string {
                    $income = $r->details?->net_income;

                    return blank($income) ? '—' : "{$income} RON";
                }),
            TextEntry::make('details.homeownership')
                ->label(__('field.homeownership'))
                ->state(fn (Beneficiary $r): string => BeneficiaryDetailsPanelFormatter::formatEnumLabel($r->details?->homeownership)),
            TextEntry::make('aggressor_relationship')
                ->label(__('field.aggressor_relationship'))
                ->state(fn (Beneficiary $r): string => BeneficiaryDetailsPanelFormatter::formatEnumLabel($r->aggressors->first()?->relationship)),
            TextEntry::make('aggressor_legal_history')
                ->label(__('field.aggressor_legal_history'))
                ->state(function (Beneficiary $r): string {
                    $aggressor = $r->aggressors->first();

                    return BeneficiaryDetailsPanelFormatter::formatCollectionLabels($aggressor?->legal_history);
                }),
            TextEntry::make('flowPresentation.presentation_mode')
                ->label(__('field.presentation_mode'))
                ->state(fn (Beneficiary $r): string => BeneficiaryDetailsPanelFormatter::formatEnumLabel($r->flowPresentation?->presentation_mode)),
            TextEntry::make('flowPresentation.act_location')
                ->label(__('field.act_location'))
                ->state(fn (Beneficiary $r): string => BeneficiaryDetailsPanelFormatter::formatCollectionLabels($r->flowPresentation?->act_location)),
        ];
    }
}
