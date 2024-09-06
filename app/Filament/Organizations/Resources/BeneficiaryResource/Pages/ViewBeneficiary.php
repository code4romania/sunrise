<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Enums\CaseStatus;
use App\Enums\RecommendationService;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Actions\EditExtraLarge;
use App\Filament\Organizations\Resources\BeneficiaryResource\Actions\ViewDetailsAction;
use App\Infolists\Components\EnumEntry;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiary extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBaseBreadcrumbs();
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([])
                ->label(__('beneficiary.action.case_actions'))
                ->button()
                ->iconPosition(IconPosition::After)
                ->icon('heroicon-s-chevron-down')
                ->actions([
                    ActionGroup::make([])
                        ->dropdown(false)
                        ->actions([
                            BeneficiaryResource\Actions\ChangeStatus::make('active'),
                            BeneficiaryResource\Actions\ChangeStatus::make('monitored'),
                            BeneficiaryResource\Actions\ChangeStatus::make('closed'),
                            //                            BeneficiaryResource\Actions\ChangeStatus::make('archived'),
                        ]),

                    ActionGroup::make([])
                        ->dropdown(false)
                        ->actions([
                            Action::make('reactivate')
                                ->label(__('beneficiary.action.reactivate'))
                                ->disabled(
                                    fn (Beneficiary $record): bool => $record->status !== CaseStatus::CLOSED
//                                    && $record->status !== CaseStatus::ARCHIVED
                                )
                                ->url(fn ($record) => self::getResource()::getUrl('reactivate', ['parent' => $record->id])),

                            Action::make('delete')
                                ->label(__('beneficiary.action.delete'))
                                ->color('danger')
                                ->disabled(),
                        ]),
                ]),
        ];
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.view.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns()
            ->schema([
                $this->identitySectionSection(),
                $this->personalInformationSection(),
                $this->initialEvaluation(),
                $this->detailedEvaluation(),
            ]);
    }

    protected function identitySectionSection(): Section
    {
        return Section::make(__('beneficiary.page.identity.title'))
            ->columnSpan(1)
            ->columns()
            ->headerActions([
                ViewDetailsAction::make('view')
                    ->url(fn ($record) => BeneficiaryResource::getUrl('view_identity', ['record' => $record])),
            ])
            ->extraAttributes([
                'class' => 'h-full',
            ])
            ->schema([
                TextEntry::make('age')
                    ->label(__('field.age'))
                    ->formatStateUsing(fn ($state) => $state === '-' ? $state : trans_choice('general.age', $state)),

                TextEntry::make('birthdate')
                    ->label(__('field.birthdate'))
                    ->formatStateUsing(fn ($state) => $state === '-' ? $state : $state->toDateString()),

                EnumEntry::make('gender')
                    ->label(__('field.gender')),

                TextEntry::make('cnp')
                    ->label(__('field.cnp')),

                EnumEntry::make('civil_status')
                    ->label(__('field.civil_status')),

                TextEntry::make('children_care_count')
                    ->label(__('field.children_care_count')),

                TextEntry::make('legal_residence_address')
                    ->label(__('field.legal_residence_address'))
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull(),

                TextEntry::make('primary_phone')
                    ->label(__('field.primary_phone'))
                    ->icon('heroicon-o-phone')
                    ->url(fn ($state) => "tel:{$state}"),

                TextEntry::make('backup_phone')
                    ->label(__('field.backup_phone'))
                    ->icon('heroicon-o-phone')
                    ->url(fn ($state) => "tel:{$state}"),

                TextEntry::make('email')
                    ->label(__('beneficiary.section.identity.labels.email'))
                    ->icon('heroicon-o-envelope'),

                TextEntry::make('notes')
                    ->label(__('field.notes'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->columnSpanFull(),
            ]);
    }

    protected function personalInformationSection(): Section
    {
        return Section::make(__('beneficiary.page.personal_information.title'))
            ->columnSpan(1)
            ->columns()
            ->headerActions([
                ViewDetailsAction::make('view')
                    ->url(fn ($record) => BeneficiaryResource::getUrl('view_personal_information', ['record' => $record])),
            ])
            ->extraAttributes([
                'class' => 'h-full',
            ])
            ->schema([
                EnumEntry::make('presentation_mode')
                    ->label(__('field.presentation_mode')),

                TextEntry::make('referringInstitution.name')
                    ->label(__('field.referring_institution')),

                TextEntry::make('family_doctor_name')
                    ->label(__('field.family_doctor_name')),

                TextEntry::make('family_doctor_contact')
                    ->label(__('field.family_doctor_contact'))
                    ->icon('heroicon-o-phone')
                    ->url(fn ($state) => "tel:{$state}"),

                RepeatableEntry::make('aggressor')
                    ->label(__('beneficiary.section.personal_information.section.aggressor'))
                    ->columns()
                    ->columnSpanFull()
                    ->schema([
                        EnumEntry::make('relationship')
                            ->label(__('field.aggressor_relationship')),
                        EnumEntry::make('has_violence_history')
                            ->label(__('field.aggressor_has_violence_history')),
                    ]),
                EnumEntry::make('has_police_reports')
                    ->label(__('field.has_police_reports'))
                    ->suffix(fn ($record) => Ternary::isYes($record->has_police_reports)
                        ? " ({$record->police_report_count})" : null),

                EnumEntry::make('has_medical_reports')
                    ->label(__('field.has_medical_reports'))
                    ->suffix(fn ($record) => Ternary::isYes($record->has_medical_reports)
                        ? " ({$record->medical_report_count})" : null),

                EnumEntry::make('has_protection_order')
                    ->label(__('field.has_protection_order')),
            ]);
    }

    private function initialEvaluation(): Section
    {
        return
            Section::make(__('beneficiary.page.create_initial_evaluation.title'))
                ->columnSpan(1)
                ->headerActions(
                    [
                        ViewDetailsAction::make('view')
                            ->url(fn (Beneficiary $record) => BeneficiaryResource::getUrl('view_initial_evaluation', ['record' => $record]))
                            ->visible(fn (Beneficiary $record) => $record->violence?->violence_types),
                    ]
                )
                ->schema([
                    Group::make()
                        ->columns()
                        ->visible(fn (Beneficiary $record) => $record->violence?->violence_types)
                        ->schema([
                            TextEntry::make('evaluateDetails.registered_date')
                                ->label(__('beneficiary.section.initial_evaluation.labels.registered_date'))
                                ->date(),
                            TextEntry::make('violence.violence_types')
                                ->label(__('beneficiary.section.initial_evaluation.labels.violence_type'))
                                ->badge()
                                ->color(Color::Gray),
                            EnumEntry::make('riskFactors.risk_level')
                                ->hiddenLabel()
                                ->badge()
                                ->icon(false),
                        ]),
                    Group::make()
                        ->visible(fn (Beneficiary $record) => ! $record->violence?->violence_types)
                        ->schema([
                            TextEntry::make('empty_state_heading')
                                ->hiddenLabel()
                                ->default(__('beneficiary.helper_text.initial_evaluation'))
                                ->alignCenter()
                                ->weight(FontWeight::SemiBold)
                                ->size(TextEntrySize::Medium),
                            TextEntry::make('empty_state_description')
                                ->hiddenLabel()
                                ->default(__('beneficiary.helper_text.initial_evaluation_2'))
                                ->alignCenter()
                                ->color(Color::Gray)
                                ->size(TextEntrySize::Small),
                            Actions::make([
                                EditExtraLarge::make('create_initial_evaluation')
                                    ->label(__('beneficiary.action.start_evaluation'))
                                    ->url(fn (Beneficiary $record) => BeneficiaryResource::getUrl('create_initial_evaluation', ['record' => $record]))
                                    ->outlined(),
                            ])
                                ->alignCenter(),
                        ]),
                ]);
    }

    private function detailedEvaluation(): Section
    {
        return
            Section::make(__('beneficiary.page.create_detailed_evaluation.title'))
                ->columnSpan(1)
                ->headerActions(
                    [
                        ViewDetailsAction::make('view')
                            ->url(fn (Beneficiary $record) => BeneficiaryResource::getUrl('view_detailed_evaluation', ['record' => $record]))
                            ->visible(fn (Beneficiary $record) => $record->detailedEvaluationResult),
                    ]
                )
                ->schema([
                    Group::make()
                        ->relationship('detailedEvaluationResult')
                        ->visible(fn (Beneficiary $record) => $record->detailedEvaluationResult)
                        ->schema([
                            TextEntry::make('detailedEvaluationResult')
                                ->state(
                                    fn (Beneficiary $record) => collect(RecommendationService::options())
                                        ->filter(fn ($label, $key) => $record->detailedEvaluationResult?->$key)
                                        ->all()
                                )
                                ->color(Color::Gray)
                                ->badge(),
                        ]),
                    Group::make()
                        ->visible(fn (Beneficiary $record) => ! $record->detailedEvaluationResult)
                        ->schema([
                            TextEntry::make('empty_state_heading')
                                ->hiddenLabel()
                                ->default(__('beneficiary.helper_text.detailed_evaluation'))
                                ->alignCenter()
                                ->weight(FontWeight::SemiBold)
                                ->size(TextEntrySize::Medium),
                            TextEntry::make('empty_state_description')
                                ->hiddenLabel()
                                ->default(__('beneficiary.helper_text.detailed_evaluation_2'))
                                ->alignCenter()
                                ->color(Color::Gray)
                                ->size(TextEntrySize::Small),
                            Actions::make([
                                EditExtraLarge::make('create_detailed_evaluation')
                                    ->label(__('beneficiary.action.start_evaluation'))
                                    ->url(fn (Beneficiary $record) => BeneficiaryResource::getUrl('create_detailed_evaluation', ['record' => $record]))
                                    ->outlined(),
                            ])
                                ->alignCenter(),
                        ]),
                ]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            BeneficiaryResource\Widgets\CaseTeamListWidget::class,
            BeneficiaryResource\Widgets\DocumentsListWidget::class,
        ];
    }
}
