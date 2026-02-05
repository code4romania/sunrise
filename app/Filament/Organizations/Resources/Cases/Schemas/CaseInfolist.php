<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CaseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([

                        Section::make(__('case.view.identity'))
                            ->description(__('case.view.see_details'))
                            ->headerActions([
                                Action::make('view_identity')
                                    ->label(__('case.view.see_details'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('identity', ['record' => $record]))
                                    ->link(),
                            ])
                            ->schema([
                                TextEntry::make('age')
                                    ->label(__('field.age'))
                                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state} ani" : '—'),
                                TextEntry::make('birthdate')
                                    ->label(__('field.birthdate'))
                                    ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd M Y'))
                                    ->placeholder('—'),
                                TextEntry::make('gender')
                                    ->label(__('field.gender'))
                                    ->placeholder('—'),
                                TextEntry::make('cnp')
                                    ->label(__('field.cnp'))
                                    ->placeholder('—'),
                                TextEntry::make('civil_status')
                                    ->label(__('field.civil_status'))
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

                        Section::make(__('case.view.case_info'))
                            ->description(__('case.view.see_details'))
                            ->headerActions([
                                Action::make('view_personal_information')
                                    ->label(__('case.view.see_details'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('view_personal_information', ['record' => $record]))
                                    ->link(),
                            ])
                            ->schema([
                                TextEntry::make('flowPresentation.presentation_mode')
                                    ->label(__('field.presentation_mode'))
                                    ->placeholder('—'),
                            ]),

                        Section::make(__('case.view.initial_evaluation'))
                            ->description(__('case.view.empty_initial_eval'))
                            ->headerActions([
                                Action::make('create_initial_evaluation')
                                    ->label(__('case.view.start_evaluation'))
                                    ->url(fn (Beneficiary $record): string => CaseResource::getUrl('create_initial_evaluation', ['record' => $record]))
                                    ->visible(fn (Beneficiary $record): bool => $record->evaluateDetails === null)
                                    ->link(),
                            ])
                            ->schema([
                                TextEntry::make('evaluateDetails.registered_date')
                                    ->label(__('beneficiary.labels.registered_date'))
                                    ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd.m.Y'))
                                    ->placeholder('—')
                                    ->visible(fn (Beneficiary $record): bool => $record->evaluateDetails !== null),
                            ]),

                        Section::make(__('case.view.detailed_evaluation'))
                            ->description(__('case.view.empty_detailed_eval'))
                            ->schema([]),

                        Section::make(__('case.view.intervention_plan'))
                            ->description(__('case.view.empty_intervention_plan'))
                            ->schema([
                                TextEntry::make('interventionPlan.id')
                                    ->label(__('case.view.see_plan_details'))
                                    ->placeholder('—')
                                    ->visible(fn (Beneficiary $record): bool => $record->interventionPlan !== null),
                            ])
                            ->columnSpan(2),

                        Section::make(__('case.view.case_monitoring'))
                            ->schema([
                                TextEntry::make('lastMonitoring.date')
                                    ->label(__('case.view.last_monitoring'))
                                    ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd.m.Y'))
                                    ->placeholder('—'),
                                TextEntry::make('monitoring_count')
                                    ->label(__('case.view.total_monitorings'))
                                    ->state(fn (Beneficiary $record): string => (string) $record->monitoring()->count())
                                    ->placeholder('0'),
                            ]),

                        Section::make(__('case.view.case_closure'))
                            ->description(__('case.view.empty_closure'))
                            ->schema([
                                TextEntry::make('closeFile.date')
                                    ->label(__('case.view.closed_at'))
                                    ->formatStateUsing(fn (mixed $state): string => self::formatBirthdateState($state, 'd.m.Y'))
                                    ->placeholder('—')
                                    ->visible(fn (Beneficiary $record): bool => $record->closeFile !== null),
                                TextEntry::make('closeFile.close_method')
                                    ->label(__('case.view.closure_method'))
                                    ->placeholder('—')
                                    ->visible(fn (Beneficiary $record): bool => $record->closeFile !== null),
                            ]),

                        Section::make(__('case.view.case_team'))
                            ->description(__('case.view.see_details'))
                            ->schema([
                                RepeatableEntry::make('specialistsTeam')
                                    ->schema([
                                        TextEntry::make('role.name')
                                            ->label(__('case.view.role')),
                                        TextEntry::make('user.full_name')
                                            ->label(__('case.view.specialist')),
                                    ])
                                    ->columns(2)
                                    ->contained(false),
                            ]),

                        Section::make(__('case.view.documents'))
                            ->schema([
                                RepeatableEntry::make('documents')
                                    ->schema([
                                        TextEntry::make('type')
                                            ->label(__('document.labels.type')),
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
}
