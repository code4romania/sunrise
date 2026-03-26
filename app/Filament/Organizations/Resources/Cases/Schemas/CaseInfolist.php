<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Enums\AggressorRelationship;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\CloseFile\CloseFileResource;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\InitialEvaluationResource;
use App\Filament\Schemas\Components\SectionWithRecordActions;
use App\Infolists\Components\SectionHeader;
use App\Models\Aggressor;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class CaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->extraAttributes([
                'class' => 'h-full',
            ])
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([

                        SectionWithRecordActions::make(__('case.view.identity'))
                            ->headerActions([
                                Action::make('view_identity')
                                    ->label(__('case.view.see_details'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('identity', ['record' => $record]))
                                    ->link(),
                            ])
                            ->schema([
                                TextEntry::make('status')
                                    ->label(__('case.table.status'))
                                    ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                    ->color(fn ($state) => is_object($state) && method_exists($state, 'getColor') ? $state->getColor() : 'gray')
                                    ->badge()
                                    ->placeholder('—'),
                                TextEntry::make('age')
                                    ->label(__('field.age'))
                                    ->formatStateUsing(function (mixed $state): string {
                                        if ($state === null || $state === '' || $state === '-') {
                                            return '—';
                                        }
                                        $age = is_numeric($state) ? (int) $state : null;

                                        return $age !== null ? "{$age} ani" : '—';
                                    }),
                                TextEntry::make('birthdate')
                                    ->label(__('field.birthdate'))
                                    ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd M Y'))
                                    ->placeholder('—'),
                                TextEntry::make('gender')
                                    ->label(__('field.gender'))
                                    ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                    ->placeholder('—'),
                                TextEntry::make('cnp')
                                    ->label(__('field.cnp'))
                                    ->placeholder('—'),
                                TextEntry::make('civil_status')
                                    ->label(__('field.civil_status'))
                                    ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                    ->placeholder('—'),
                                TextEntry::make('children_care_count')
                                    ->label(__('field.children_care_count'))
                                    ->placeholder('—'),
                                TextEntry::make('children_under_18_care_count')
                                    ->label(__('field.children_under_18_care_count'))
                                    ->placeholder('—'),
                                TextEntry::make('children_accompanying_count')
                                    ->label(__('field.children_accompanying_count'))
                                    ->placeholder('—'),
                                TextEntry::make('effective_residence_formatted')
                                    ->label(__('field.effective_residence_address'))
                                    ->state(fn (Beneficiary $record): string => self::formatAddress($record))
                                    ->placeholder('—'),
                                TextEntry::make('phones_formatted')
                                    ->label(__('field.primary_phone'))
                                    ->state(fn (Beneficiary $record): string => self::formatPhones($record))
                                    ->placeholder('—'),
                                TextEntry::make('contact_notes')
                                    ->label(__('field.contact_notes'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),

                        SectionWithRecordActions::make(__('case.view.case_info'))
                            ->headerActions([
                                Action::make('view_personal_information')
                                    ->label(__('case.view.see_details'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('view_personal_information', ['record' => $record]))
                                    ->link(),
                            ])
                            ->schema([
                                TextEntry::make('flowPresentation.presentation_mode')
                                    ->label(__('field.presentation_mode'))
                                    ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                    ->placeholder('—'),
                                TextEntry::make('flowPresentation.referringInstitution.name')
                                    ->label(__('field.referring_institution'))
                                    ->placeholder('—'),
                                TextEntry::make('details.family_doctor_name')
                                    ->label(__('field.family_doctor_name'))
                                    ->placeholder('—'),
                                TextEntry::make('details.family_doctor_contact')
                                    ->label(__('field.family_doctor_contact'))
                                    ->placeholder('—'),
                                RepeatableEntry::make('aggressors')
                                    ->label(__('case.view.aggressor'))
                                    ->hiddenLabel()
                                    ->columns(2)
                                    ->schema([
                                        SectionHeader::make('header')
                                            ->state(function (SectionHeader $component): string {
                                                $path = $component->getStatePath();
                                                $parts = explode('.', $path);
                                                $index = (int) ($parts[1] ?? 0);
                                                $parentState = $component->getContainer()?->getParentComponent()?->getState();
                                                $total = $parentState instanceof Collection ? $parentState->count() : (is_countable($parentState) ? count($parentState) : 0);

                                                return $total > 1
                                                    ? __('case.view.aggressor').' ('.($index + 1).' din '.$total.' '.__('case.aggressors_documented').')'
                                                    : __('case.view.aggressor');
                                            })
                                            ->visible(fn (SectionHeader $component): bool => $component->getContainer()?->getParentComponent()?->getState() instanceof Collection
                                                && $component->getContainer()->getParentComponent()->getState()->count() > 0),
                                        TextEntry::make('relationship')
                                            ->label(__('field.aggressor_relationship'))
                                            ->formatStateUsing(fn ($state, Aggressor $record): string => $record->relationship !== null
                                                ? ($record->relationship === AggressorRelationship::OTHER && $record->relationship_other
                                                    ? $record->relationship->getLabel().' ('.$record->relationship_other.')'
                                                    : $record->relationship->getLabel())
                                                : '—')
                                            ->placeholder('—'),
                                        TextEntry::make('violence_types')
                                            ->label(__('field.aggressor_violence_types'))
                                            ->state(function (Aggressor $record): string {
                                                $types = $record->violence_types;

                                                if ($types === null || $types->isEmpty()) {
                                                    return '—';
                                                }

                                                return $types->map(fn ($v) => is_object($v) && method_exists($v, 'getLabel') ? $v->getLabel() : (string) $v)->implode(', ');
                                            })
                                            ->placeholder('—'),
                                        TextEntry::make('has_protection_order')
                                            ->label(__('field.has_protection_order'))
                                            ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                            ->placeholder('—'),
                                        TextEntry::make('electronically_monitored')
                                            ->label(__('field.electronically_monitored'))
                                            ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                            ->placeholder('—'),
                                        TextEntry::make('has_police_reports')
                                            ->label(__('field.has_police_reports'))
                                            ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                            ->placeholder('—'),
                                        TextEntry::make('police_report_count')
                                            ->label(__('field.police_report_count'))
                                            ->placeholder('—'),
                                        TextEntry::make('has_medical_reports')
                                            ->label(__('field.has_medical_reports'))
                                            ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                            ->placeholder('—'),
                                        TextEntry::make('medical_report_count')
                                            ->label(__('field.medical_report_count'))
                                            ->placeholder('—'),
                                        TextEntry::make('hospitalization_days')
                                            ->label(__('field.hospitalization_days'))
                                            ->placeholder('—'),
                                        TextEntry::make('hospitalization_observations')
                                            ->label(__('field.hospitalization_observations'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn (Beneficiary $record): bool => $record->aggressors->isNotEmpty()),
                            ]),

                        SectionWithRecordActions::make(__('case.view.initial_evaluation'))
                            ->headerActions([
                                Action::make('view_initial_evaluation')
                                    ->label(__('case.view.see_details'))
                                    ->url(fn (Beneficiary $record): string => InitialEvaluationResource::getUrl('view', [
                                        'beneficiary' => $record,
                                        'record' => $record->evaluateDetails,
                                    ]))
                                    ->visible(fn (Beneficiary $record): bool => $record->evaluateDetails !== null)
                                    ->link(),
                                Action::make('create_initial_evaluation')
                                    ->label(__('case.view.start_evaluation'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('create_initial_evaluation', ['record' => $record]))
                                    ->visible(fn (Beneficiary $record): bool => $record->evaluateDetails === null)
                                    ->link(),
                            ])
                            ->schema([
                                EmptyState::make(__('case.view.initial_evaluation'))
                                    ->description(__('case.view.empty_initial_eval'))
                                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                                    ->visible(fn (Beneficiary $record): bool => $record->evaluateDetails === null)
                                    ->footer([
                                        Action::make('create_initial_evaluation_empty')
                                            ->label(__('case.view.start_evaluation'))
                                            ->url(fn (Beneficiary $record): string => CaseResource::getUrl('create_initial_evaluation', ['record' => $record]))
                                            ->button(),
                                    ]),
                                Grid::make(2)
                                    ->visible(fn (Beneficiary $record): bool => $record->evaluateDetails !== null)
                                    ->schema([
                                        TextEntry::make('evaluateDetails.registered_date')
                                            ->label(__('beneficiary.section.initial_evaluation.labels.registered_date'))
                                            ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd.m.Y'))
                                            ->placeholder('—'),
                                        TextEntry::make('_risk_level_overview')
                                            ->hiddenLabel()
                                            ->state(fn (Beneficiary $record) => $record->riskFactors?->risk_level ?? \App\Enums\Level::NONE)
                                            ->badge()
                                            ->color(fn (\App\Enums\Level $state): string => match ($state) {
                                                \App\Enums\Level::HIGH => 'danger',
                                                \App\Enums\Level::MEDIUM => 'warning',
                                                \App\Enums\Level::LOW => 'warning',
                                                \App\Enums\Level::NONE => 'success',
                                            })
                                            ->icon(fn (\App\Enums\Level $state): ?string => $state->getIcon())
                                            ->formatStateUsing(fn (\App\Enums\Level $state): string => $state->label()),
                                        TextEntry::make('violence.violence_types')
                                            ->label(__('case.view.initial_eval.violence_type'))
                                            ->formatStateUsing(fn ($state) => filled($state)
                                                ? collect($state)->map(fn ($v) => $v->label())->implode('; ')
                                                : null)
                                            ->placeholder('—'),
                                        TextEntry::make('_violence_means_overview')
                                            ->label(__('case.view.initial_eval.violence_means'))
                                            ->state(fn (Beneficiary $record): ?string => self::formatViolenceMeansOverview($record))
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        SectionWithRecordActions::make(__('case.view.detailed_evaluation'))
                            ->headerActions([
                                Action::make('view_detailed_evaluation')
                                    ->label(__('case.view.see_details'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('view_detailed_evaluation', ['record' => $record]))
                                    ->visible(fn (Beneficiary $record): bool => $record->multidisciplinaryEvaluation !== null || $record->detailedEvaluationResult !== null || $record->detailedEvaluationSpecialists()->exists())
                                    ->color('primary')
                                    ->link(),
                                Action::make('start_detailed_evaluation')
                                    ->label(__('case.view.start_evaluation'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('create_detailed_evaluation', ['record' => $record]))
                                    ->visible(fn (Beneficiary $record): bool => $record->multidisciplinaryEvaluation === null && $record->detailedEvaluationResult === null && ! $record->detailedEvaluationSpecialists()->exists())
                                    ->link(),
                            ])
                            ->schema([
                                EmptyState::make(__('case.view.detailed_evaluation'))
                                    ->description(__('case.view.empty_detailed_eval'))
                                    ->icon(Heroicon::OutlinedChartBarSquare)
                                    ->visible(fn (Beneficiary $record): bool => $record->multidisciplinaryEvaluation === null && $record->detailedEvaluationResult === null && ! $record->detailedEvaluationSpecialists()->exists())
                                    ->footer([
                                        Action::make('start_detailed_evaluation_empty')
                                            ->label(__('case.view.start_evaluation'))
                                            ->url(fn (Beneficiary $record): string => CaseResource::getUrl('create_detailed_evaluation', ['record' => $record]))
                                            ->button(),
                                    ]),
                                TextEntry::make('_detailed_evaluation_recommendation_services')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.recommendation_services'))
                                    ->state(fn (Beneficiary $record) => $record->detailedEvaluationResult?->recommendation_services ?? collect())
                                    ->formatStateUsing(function ($state): HtmlString {
                                        if ($state === null || (is_countable($state) && count($state) === 0)) {
                                            return new HtmlString('<span class="text-gray-500">—</span>');
                                        }
                                        $pills = collect($state)->map(fn ($service) => '<span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800">'.e($service).'</span>')->implode('');

                                        return new HtmlString('<div class="flex flex-wrap gap-2">'.$pills.'</div>');
                                    })
                                    ->html()
                                    ->visible(fn (Beneficiary $record): bool => $record->multidisciplinaryEvaluation !== null || $record->detailedEvaluationResult !== null || $record->detailedEvaluationSpecialists()->exists()),
                            ]),

                        SectionWithRecordActions::make(__('case.view.intervention_plan'))
                            ->headerActions([
                                Action::make('create_plan')
                                    ->label(__('case.view.create_plan'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('create_intervention_plan', ['record' => $record]))
                                    ->visible(fn (Beneficiary $record): bool => $record->interventionPlan === null),
                                Action::make('view_plan')
                                    ->label(__('case.view.see_plan_details'))
                                    ->visible(fn (Beneficiary $record): bool => $record->interventionPlan !== null)
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('view_intervention_plan', ['record' => $record])),
                            ])
                            ->schema([
                                EmptyState::make(__('case.view.intervention_plan'))
                                    ->description(__('case.view.empty_intervention_plan'))
                                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                                    ->visible(fn (Beneficiary $record): bool => $record->interventionPlan === null)
                                    ->footer([
                                        Action::make('create_plan_empty')
                                            ->label(__('case.view.create_plan'))
                                            ->url(fn (Beneficiary $record): string => CaseResource::getUrl('create_intervention_plan', ['record' => $record]))
                                            ->button(),
                                    ]),
                                TextEntry::make('interventionPlan.plan_date')
                                    ->label(__('intervention_plan.labels.plan_date'))
                                    ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd.m.Y'))
                                    ->placeholder('—')
                                    ->visible(fn (Beneficiary $record): bool => $record->interventionPlan !== null),
                            ])
                            ->columnSpan(2),

                        SectionWithRecordActions::make(__('case.view.case_monitoring'))
                            ->headerActions([
                                Action::make('edit_monitoring')
                                    ->label(__('case.view.manage_monitoring'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('edit_case_monitoring', ['record' => $record]))
                                    ->link(),
                            ])
                            ->schema([
                                EmptyState::make(__('case.view.case_monitoring'))
                                    ->description(__('case.view.empty_monitoring'))
                                    ->icon(Heroicon::OutlinedDocumentChartBar)
                                    ->visible(fn (Beneficiary $record): bool => $record->monitoring()->count() === 0)
                                    ->footer([
                                        Action::make('complete_monitoring_empty')
                                            ->label(__('case.view.complete_monitoring_sheet'))
                                            ->url(fn (Beneficiary $record): string => CaseResource::getUrl('edit_case_monitoring', ['record' => $record]))
                                            ->button(),
                                    ]),
                                TextEntry::make('lastMonitoring.date')
                                    ->label(__('case.view.last_monitoring'))
                                    ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd.m.Y'))
                                    ->placeholder('—')
                                    ->visible(fn (Beneficiary $record): bool => $record->monitoring()->count() > 0),
                                TextEntry::make('monitoring_count')
                                    ->label(__('case.view.total_monitorings'))
                                    ->state(fn (Beneficiary $record): string => (string) $record->monitoring()->count())
                                    ->placeholder('0')
                                    ->visible(fn (Beneficiary $record): bool => $record->monitoring()->count() > 0),
                            ]),

                        SectionWithRecordActions::make(__('case.view.case_closure'))
                            ->headerActions([
                                Action::make('create_close_file')
                                    ->label(__('case.view.complete_closure_sheet'))
                                    ->url(fn (Beneficiary $record): string => CloseFileResource::getUrl('create', ['beneficiary' => $record]))
                                    ->visible(fn (Beneficiary $record): bool => $record->closeFile === null)
                                    ->link(),
                                Action::make('view_close_file')
                                    ->label(__('beneficiary.section.close_file.headings.file_details_simple'))
                                    ->url(fn (Beneficiary $record): string => CloseFileResource::getUrl('view', ['beneficiary' => $record, 'record' => $record->closeFile]))
                                    ->visible(fn (Beneficiary $record): bool => $record->closeFile !== null)
                                    ->link(),
                            ])
                            ->schema([
                                EmptyState::make(__('case.view.case_closure'))
                                    ->description(__('case.view.empty_closure'))
                                    ->icon(Heroicon::OutlinedDocumentCheck)
                                    ->visible(fn (Beneficiary $record): bool => $record->closeFile === null)
                                    ->footer([
                                        Action::make('complete_closure_empty')
                                            ->label(__('case.view.complete_closure_sheet'))
                                            ->url(fn (Beneficiary $record): string => CloseFileResource::getUrl('create', ['beneficiary' => $record]))
                                            ->button()
                                            ->visible(fn (Beneficiary $record): bool => $record->status?->value === 'closed'),
                                    ]),
                                TextEntry::make('closeFile.date')
                                    ->label(__('case.view.closed_at'))
                                    ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd.m.Y'))
                                    ->placeholder('—')
                                    ->visible(fn (Beneficiary $record): bool => $record->closeFile !== null),
                                TextEntry::make('closeFile.close_method')
                                    ->label(__('case.view.closure_method'))
                                    ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                    ->placeholder('—')
                                    ->visible(fn (Beneficiary $record): bool => $record->closeFile !== null),
                            ]),

                        SectionWithRecordActions::make(__('case.view.case_team'))
                            ->headerActions([
                                Action::make('edit_case_team')
                                    ->label(__('case.view.manage_case_team'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('edit_case_team', ['record' => $record]))
                                    ->link(),
                            ])
                            ->schema([
                                RepeatableEntry::make('specialistsTeam')
                                    ->state(fn (Beneficiary $record) => $record->specialistsTeam()->with(['user', 'roleForDisplay'])->get())
                                    ->schema([
                                        TextEntry::make('user.full_name')
                                            ->label(__('beneficiary.section.specialists.labels.name'))
                                            ->formatStateUsing(function (mixed $state, TextEntry $entry): string {
                                                $specialist = $entry->getRecord();

                                                return $specialist instanceof \App\Models\Specialist && $specialist->user !== null
                                                    ? $specialist->user->full_name
                                                    : '—';
                                            }),
                                        TextEntry::make('role_display')
                                            ->label(__('beneficiary.section.specialists.labels.roles'))
                                            ->state(function (TextEntry $entry): string {
                                                $specialist = $entry->getRecord();

                                                if (! $specialist instanceof \App\Models\Specialist) {
                                                    return '—';
                                                }
                                                if ($specialist->relationLoaded('roleForDisplay') && $specialist->roleForDisplay !== null) {
                                                    return $specialist->roleForDisplay->name;
                                                }
                                                if ($specialist->role_id !== null) {
                                                    $specialist->loadMissing('roleForDisplay');

                                                    return $specialist->roleForDisplay?->name ?? '—';
                                                }

                                                return '—';
                                            }),
                                    ])
                                    ->columns(2)
                                    ->contained(false),
                            ]),

                        SectionWithRecordActions::make(__('case.view.documents'))
                            ->headerActions([
                                Action::make('edit_documents')
                                    ->label(__('case.view.manage_documents'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('edit_case_documents', ['record' => $record]))
                                    ->link(),
                            ])
                            ->schema([
                                EmptyState::make(__('case.view.documents'))
                                    ->description(__('case.view.empty_documents'))
                                    ->icon(Heroicon::OutlinedDocument)
                                    ->visible(fn (Beneficiary $record): bool => $record->documents()->count() === 0)
                                    ->footer([
                                        Action::make('upload_document_empty')
                                            ->label(__('case.view.upload_document'))
                                            ->url(fn (Beneficiary $record): string => CaseResource::getUrl('edit_case_documents', ['record' => $record]))
                                            ->button(),
                                    ]),
                                RepeatableEntry::make('documents')
                                    ->schema([
                                        TextEntry::make('type')
                                            ->label(__('document.labels.type'))
                                            ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—'),
                                        TextEntry::make('name')
                                            ->label(__('document.labels.name')),
                                    ])
                                    ->columns(2)
                                    ->contained(false)
                                    ->visible(fn (Beneficiary $record): bool => $record->documents()->count() > 0),
                            ]),

                        Section::make(__('case.view.related_files'))
                            ->schema([
                                RepeatableEntry::make('relatedCases')
                                    ->label('')
                                    ->state(fn (Beneficiary $record): \Illuminate\Support\Collection => $record->getRelatedCases())
                                    ->schema([
                                        TextEntry::make('id')
                                            ->label(__('case.table.file_number')),
                                        TextEntry::make('full_name')
                                            ->label(__('case.table.beneficiary')),
                                        TextEntry::make('created_at')
                                            ->label(__('case.table.opened_at'))
                                            ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd.m.Y')),
                                        TextEntry::make('manager_display')
                                            ->label(__('case.table.case_manager'))
                                            ->state(fn (Beneficiary $related): string => $related->managerTeam->first()?->user?->full_name ?? '—'),
                                        TextEntry::make('status')
                                            ->label(__('case.table.status'))
                                            ->formatStateUsing(fn ($state) => is_object($state) && method_exists($state, 'getLabel') ? $state->getLabel() : '—')
                                            ->color(fn ($state) => is_object($state) && method_exists($state, 'getColor') ? $state->getColor() : 'gray')
                                            ->badge(),
                                    ])
                                    ->columns(5)
                                    ->contained(false)
                                    ->visible(fn (Beneficiary $record): bool => $record->getRelatedCases()->isNotEmpty()),
                            ])
                            ->columnSpan(2),
                    ]),
            ]);
    }

    private static function formatBirthdateState(mixed $state, string $format): string
    {
        if ($state === null || $state === '' || $state === '-') {
            return '—';
        }

        try {
            return Carbon::parse($state)->translatedFormat($format);
        } catch (\Throwable) {
            return '—';
        }
    }

    private static function formatAddress(Beneficiary $record): string
    {
        $addr = $record->effective_residence;
        if (! $addr) {
            return '';
        }
        $parts = array_filter([
            $addr->address,
            $addr->city?->name,
            $addr->county ? __('field.county').' '.$addr->county->name : null,
        ]);

        return implode(', ', $parts);
    }

    private static function formatPhones(Beneficiary $record): string
    {
        $phones = array_filter([$record->primary_phone, $record->backup_phone]);

        return implode('; ', $phones);
    }

    private static function formatViolenceMeansOverview(Beneficiary $record): ?string
    {
        $violence = $record->violence;
        if ($violence === null) {
            return null;
        }

        $parts = [];
        if (filled($violence->violence_means)) {
            $parts[] = collect($violence->violence_means)->map(fn ($v) => $v->label())->implode('; ');
        }
        if (filled($violence->violence_means_specify)) {
            $parts[] = $violence->violence_means_specify;
        }

        return $parts !== [] ? implode('; ', $parts) : null;
    }
}
