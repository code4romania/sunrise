<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services\Schemas;

use App\Enums\CounselingSheet;
use App\Infolists\Components\Notice;
use App\Models\BeneficiaryIntervention;
use App\Models\OrganizationService;
use App\Schemas\CounselingSheetFormSchemas;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    TextEntry::make('serviceWithoutStatusCondition.name')
                        ->label(__('service.labels.name')),
                    TextEntry::make('status')
                        ->label(__('service.labels.status'))
                        ->badge(),
                ]),

            Section::make()
                ->visible(fn (OrganizationService $record) => $record->serviceWithoutStatusCondition?->counseling_sheet !== null)
                ->schema([
                    Notice::make('counseling_sheet')
                        ->state(__('service.helper_texts.counseling_sheet'))
                        ->icon('heroicon-o-document-text')
                        ->action(
                            Action::make('view_counseling_sheet')
                                ->label(__('service.actions.view_counseling_sheet'))
                                ->modalHeading(fn (OrganizationService $record) => $record->serviceWithoutStatusCondition?->counseling_sheet?->getLabel())
                                ->schema(fn (OrganizationService $record) => match ($record->serviceWithoutStatusCondition?->counseling_sheet) {
                                    CounselingSheet::LEGAL_ASSISTANCE => CounselingSheetFormSchemas::getLegalAssistanceForm(),
                                    CounselingSheet::PSYCHOLOGICAL_ASSISTANCE => CounselingSheetFormSchemas::getSchemaForPsychologicalAssistance(),
                                    CounselingSheet::SOCIAL_ASSISTANCE => CounselingSheetFormSchemas::getSchemaForSocialAssistance(null),
                                    default => [],
                                })
                                ->disabledForm()
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel(__('filament-actions::view.single.modal.actions.close.label'))
                                ->link(),
                        ),
                ]),

            Section::make(__('service.headings.interventions'))
                ->schema([
                    RepeatableEntry::make('interventions')
                        ->columnSpanFull()
                        ->columns(2)
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('serviceInterventionWithoutStatusCondition.name')
                                ->label(__('service.labels.interventions')),

                            TextEntry::make('cases_count')
                                ->label(__('service.labels.cases'))
                                ->state(function ($record) {
                                    if (! $record->relationLoaded('beneficiaryInterventions')) {
                                        $record->load('beneficiaryInterventions.interventionPlan');
                                    }

                                    return $record->beneficiaryInterventions
                                        ?->map(fn (BeneficiaryIntervention $bi) => $bi->interventionPlan?->beneficiary_id)
                                        ->unique()
                                        ->filter()
                                        ->count() ?? 0;
                                }),

                            TextEntry::make('status')
                                ->label(__('service.labels.status'))
                                ->badge(),
                        ]),
                ]),
        ]);
    }
}
