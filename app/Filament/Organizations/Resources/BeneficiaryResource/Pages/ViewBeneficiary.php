<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Enums\RecommendationService;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\EnumEntry;
use App\Livewire\Beneficiary\ListTeam;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Livewire;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
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
            //
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
                $this->evaluations(),
                $this->team(),
                $this->documents(),
            ]);
    }

    protected function identitySectionSection(): Section
    {
        return Section::make(__('beneficiary.page.identity.title'))
            ->columnSpan(1)
            ->columns()
            ->headerActions([
                BeneficiaryResource\Actions\ViewDetailsAction::make('view')
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
                BeneficiaryResource\Actions\ViewDetailsAction::make('view')
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

    private function evaluations(): Group
    {
        return Group::make()
            ->schema([
                Section::make(__('beneficiary.page.create_initial_evaluation.title'))
                    ->columnSpan(1)
                    ->headerActions(
                        [
                            BeneficiaryResource\Actions\ViewDetailsAction::make('view')
                                ->url(fn ($record) => BeneficiaryResource::getUrl('view_initial_evaluation', ['record' => $record]))
                                ->visible(fn ($record) => $record->violence?->violence_types),
                        ]
                    )
                    ->schema([
                        Group::make()
                            ->columns()
                            ->visible(fn ($record) => $record->violence?->violence_types)
                            ->schema([
                                TextEntry::make('evaluateDetails.registered_date')
                                    ->label(__('beneficiary.section.initial_evaluation.labels.registered_date'))
                                    ->hidden(fn ($state) => $state == '-')
                                    ->date(),
                                TextEntry::make('violence.violence_types')
                                    ->label(__('beneficiary.section.initial_evaluation.labels.violence_type'))
                                    ->hidden(fn ($state) => $state == '-')
                                    ->badge()
                                    ->color(Color::Gray)
                                    ->formatStateUsing(fn ($state) => $state != '-' ? $state->label() : ''),
                                EnumEntry::make('riskFactors.risk_level')
                                    ->hiddenLabel()
                                    ->badge()
                                    ->icon(false),
                            ]),
                        Group::make()
                            ->visible(fn ($record) => ! $record->violence?->violence_types)
                            ->schema([
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->default(__('beneficiary.helper_text.initial_evaluation'))
                                    ->alignCenter()
                                    ->weight(FontWeight::Bold)
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->default(__('beneficiary.helper_text.initial_evaluation_2'))
                                    ->alignCenter()
                                    ->size(TextEntry\TextEntrySize::Medium),
                                Actions::make([
                                    BeneficiaryResource\Actions\EditExtraLarge::make('edit')
                                        ->label(__('beneficiary.action.start_evaluation'))
                                        ->url(fn ($record) => BeneficiaryResource::getUrl('create_initial_evaluation', ['record' => $record])),
                                ])
                                    ->alignCenter(),
                            ]),
                    ]),

                Section::make(__('beneficiary.page.create_detailed_evaluation.title'))
                    ->columnSpan(1)
                    ->headerActions(
                        [
                            BeneficiaryResource\Actions\ViewDetailsAction::make('view')
                                ->url(fn ($record) => BeneficiaryResource::getUrl('view_detailed_evaluation', ['record' => $record]))
                                ->visible(fn ($record) => $record->detailedEvaluationResult),
                        ]
                    )
                    ->schema([
                        Group::make()
                            ->relationship('detailedEvaluationResult')
                            ->visible(fn ($record) => $record->detailedEvaluationResult)
                            ->columns()
                            ->schema(function ($state) {
                                $fields = [];
                                foreach (RecommendationService::options() as $key => $option) {
                                    if (empty($state->$key)) {
                                        continue;
                                    }

                                    $fields[] = TextEntry::make($key)
                                        ->hiddenLabel()
                                        ->badge()
                                        ->color(Color::Gray)
                                        ->formatStateUsing(fn () => substr($option, 0, 50));
                                }

                                return $fields;
                            }),
                        Group::make()
                            ->visible(fn ($record) => ! $record->detailedEvaluationResult)
                            ->schema([
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->default(__('beneficiary.helper_text.detailed_evaluation'))
                                    ->alignCenter()
                                    ->weight(FontWeight::Bold)
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->default(__('beneficiary.helper_text.detailed_evaluation_2'))
                                    ->alignCenter()
                                    ->size(TextEntry\TextEntrySize::Medium),
                                Actions::make([
                                    BeneficiaryResource\Actions\EditExtraLarge::make('edit')
                                        ->label(__('beneficiary.action.start_evaluation'))
                                        ->url(fn ($record) => BeneficiaryResource::getUrl('create_detailed_evaluation', ['record' => $record])),
                                ])
                                    ->alignCenter(),
                            ]),
                    ]),
            ]);
    }

    private function team(): Section
    {
        return Section::make(__('beneficiary.section.specialists.title'))
            ->columnSpan(1)
            ->headerActions([
                BeneficiaryResource\Actions\ViewDetailsAction::make('view')
                    ->url(fn ($record) => BeneficiaryResource::getUrl('view_specialists', ['record' => $record])),
            ])
            ->schema([
                Livewire::make(ListTeam::class),
            ]);
    }

    private function documents(): Section
    {
        return Section::make(__('beneficiary.section.documents.title.page'))
            ->columnSpan(1)
            ->headerActions([
                Action::make('edit')
                    ->label(__('general.action.view_details'))
                    ->url(fn ($record) => BeneficiaryResource::getUrl('documents.index', ['parent' => $record]))
                    ->link()
                    ->visible(fn ($record) => $record->documents->count()),
            ])
            ->schema([
                Livewire::make(\App\Livewire\Beneficiary\ListDocuments::class)
                    ->visible(fn ($record) => $record->documents->count()),
                Group::make()
                    ->visible(fn ($record) => ! $record->documents->count())
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->default(__('beneficiary.helper_text.documents'))
                            ->alignCenter()
                            ->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->default(__('beneficiary.helper_text.documents_2'))
                            ->alignCenter()
                            ->size(TextEntry\TextEntrySize::Medium),
                        Actions::make([
                            Action::make('edit')
                                ->label(__('beneficiary.section.documents.actions.add'))
                                ->url(fn ($record) => BeneficiaryResource::getUrl('documents.index', ['parent' => $record]))
                                ->badge()
                                ->size(ActionSize::ExtraLarge),
                        ])
                            ->alignCenter(),
                    ]),
            ]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            BeneficiaryResource\Widgets\CloseFileWidget::class,
        ];
    }
}
