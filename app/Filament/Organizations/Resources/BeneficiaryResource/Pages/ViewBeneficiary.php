<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Enums\CaseStatus;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Actions\EditExtraLarge;
use App\Filament\Organizations\Resources\BeneficiaryResource\Actions\ViewDetailsAction;
use App\Filament\Organizations\Resources\BeneficiaryResource\Widgets\CaseTeamListWidget;
use App\Filament\Organizations\Resources\BeneficiaryResource\Widgets\CloseFileWidget;
use App\Filament\Organizations\Resources\BeneficiaryResource\Widgets\DocumentsListWidget;
use App\Filament\Organizations\Resources\BeneficiaryResource\Widgets\IntervetnionPlanWidget;
use App\Filament\Organizations\Resources\BeneficiaryResource\Widgets\RelatedCases;
use App\Filament\Organizations\Resources\MonitoringResource\Widgets\MonitoringWidget;
use App\Infolists\Components\EnumEntry;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Grid;
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
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

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
            Action::make('view_history')
                ->label(__('beneficiary.section.history.actions.view'))
                ->icon('heroicon-o-clock')
                ->outlined()
                ->link()
                ->url(self::getResource()::getUrl('beneficiary-histories.index', ['parent' => $this->getRecord()])),

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
                            BeneficiaryResource\Actions\ChangeStatus::make('archived'),
                        ]),

                    ActionGroup::make([])
                        ->dropdown(false)
                        ->actions([
                            Action::make('reactivate')
                                ->label(__('beneficiary.action.reactivate'))
                                ->disabled(
                                    fn (Beneficiary $record): bool => $record->status !== CaseStatus::CLOSED
                                    && $record->status !== CaseStatus::ARCHIVED
                                )
                                ->modalHeading(__('beneficiary.section.identity.headings.reactivate_modal'))
                                ->form([
                                    Placeholder::make('reactivate_text_1')
                                        ->hiddenLabel()
                                        ->content(__('beneficiary.placeholder.reactivate_text_1')),

                                    Placeholder::make('reactivate_text_2')
                                        ->hiddenLabel()
                                        ->content(__('beneficiary.placeholder.reactivate_text_2')),

                                    Placeholder::make('reactivate_text_3')
                                        ->hiddenLabel()
                                        ->content(__('beneficiary.placeholder.reactivate_text_3')),

                                    Checkbox::make('confirm')
                                        ->label(__('beneficiary.section.identity.labels.beneficiary_agreement'))
                                        ->required(),
                                ])
                                ->modalSubmitActionLabel(__('beneficiary.action.reactivate_modal'))
                                ->action(fn (Action $action, Beneficiary $record) => redirect(self::getResource()::getUrl('create', ['parent' => $record->id]))),

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
        $statusBadge = Blade::render('<x-filament::badge :color="$color">{{$name}}</x-filament::badge>', [
            'name' => $this->record->status->getLabel(),
            'color' => $this->record->status->getColor(),
        ]);

        return new HtmlString(__('beneficiary.page.view.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
            'badge' => $statusBadge,
        ]));
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
                Grid::make()
                    ->relationship('flowPresentation')
                    ->schema([
                        EnumEntry::make('presentation_mode')
                            ->label(__('field.presentation_mode')),

                        TextEntry::make('referringInstitution.name')
                            ->label(__('field.referring_institution')),
                    ]),

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

                Grid::make()
                    ->relationship('antecedents')
                    ->schema([
                        EnumEntry::make('has_police_reports')
                            ->label(__('field.has_police_reports'))
                            ->suffix(fn (Beneficiary $record, $state) => Ternary::isYes($state)
                                ? " ({$record->antecedents->police_report_count})" : null),

                        EnumEntry::make('has_medical_reports')
                            ->label(__('field.has_medical_reports'))
                            ->suffix(fn (Beneficiary $record, $state) => Ternary::isYes($state)
                                ? " ({$record->antecedents->medical_report_count})" : null),

                        EnumEntry::make('has_protection_order')
                            ->label(__('field.has_protection_order')),

                        TextEntry::make('protection_order_notes')
                            ->label(__('field.protection_order_notes')),
                    ]),
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
                                ->color(Color::Gray),
                            TextEntry::make('riskFactors.risk_level')
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
                                Actions\Action::make('create_initial_evaluation')
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
                            TextEntry::make('recommendation_services')
                                ->label(__('beneficiary.section.detailed_evaluation.heading.recommendation_services'))
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
                                Actions\Action::make('create_detailed_evaluation')
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
            IntervetnionPlanWidget::class,
            MonitoringWidget::class,
            CloseFileWidget::class,
            CaseTeamListWidget::class,
            DocumentsListWidget::class,
            RelatedCases::class,
        ];
    }
}
