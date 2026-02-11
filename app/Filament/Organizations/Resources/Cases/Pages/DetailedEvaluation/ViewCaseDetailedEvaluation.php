<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation;

use App\Actions\BackAction;
use App\Enums\AddressType;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\IdentityInfolist;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\DateEntry;
use App\Infolists\Components\EnumEntry;
use App\Infolists\Components\Location;
use App\Infolists\Components\Notice;
use App\Models\Beneficiary;
use App\Models\BeneficiaryPartner;
use App\Models\DetailedEvaluationResult;
use App\Models\MultidisciplinaryEvaluation;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class ViewCaseDetailedEvaluation extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.view_detailed_evaluation.title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        $breadcrumbs = [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
        ];
        if ($record instanceof Beneficiary) {
            $breadcrumbs[CaseResource::getUrl('view', ['record' => $record])] = $record->getBreadcrumb();
        }
        $breadcrumbs[''] = __('beneficiary.breadcrumb.wizard_detailed_evaluation');

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $this->getRecord()])),
            \Filament\Actions\EditAction::make()
                ->url(CaseResource::getUrl('edit_detailed_evaluation', ['record' => $this->getRecord()])),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('beneficiary.wizard.details.label'))
                            ->maxWidth('3xl')
                            ->schema($this->getDetaliiEvaluareSchema()),

                        Tab::make(__('beneficiary.wizard.beneficiary.label'))
                            ->maxWidth('3xl')
                            ->schema($this->getIdentitateBeneficiarSchema()),

                        Tab::make(__('beneficiary.wizard.children.label'))
                            ->maxWidth('3xl')
                            ->schema($this->getIdentitateCopiiSchema()),

                        Tab::make(__('beneficiary.wizard.partner.label'))
                            ->maxWidth('3xl')
                            ->schema($this->getPartenerSchema()),

                        Tab::make(__('beneficiary.wizard.multidisciplinary_evaluation.label'))
                            ->maxWidth('3xl')
                            ->schema($this->getEvaluareMultidisciplinaraSchema()),

                        Tab::make(__('beneficiary.wizard.results.label'))
                            ->maxWidth('3xl')
                            ->schema($this->getRezultateSchema()),
                    ]),
            ]);
    }

    /**
     * @return array<int, Section|RepeatableEntry>
     */
    protected function getDetaliiEvaluareSchema(): array
    {
        return [
            Section::make(__('beneficiary.wizard.details.label'))
                ->headerActions([
                    EditAction::make('edit')
                        ->url(fn (Beneficiary $record): string => CaseResource::getUrl('edit_detailed_evaluation', ['record' => $record])),
                ])
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.labels.specialists'))
                        ->compact()
                        ->schema([
                            RepeatableEntry::make('detailedEvaluationSpecialists')
                                ->hiddenLabel()
                                ->table([
                                    TableColumn::make(__('nomenclature.labels.nr')),
                                    TableColumn::make(__('beneficiary.section.detailed_evaluation.labels.full_name')),
                                    TableColumn::make(__('beneficiary.section.detailed_evaluation.labels.institution')),
                                    TableColumn::make(__('beneficiary.section.detailed_evaluation.labels.relationship')),
                                    TableColumn::make(__('beneficiary.section.detailed_evaluation.labels.contact_date')),
                                ])
                                ->schema([
                                    TextEntry::make('nr_crt')
                                        ->state(fn (TextEntry $entry): int => $this->getRepeatableItemIndex($entry) + 1),
                                    TextEntry::make('full_name'),
                                    TextEntry::make('institution'),
                                    TextEntry::make('relationship'),
                                    DateEntry::make('date'),
                                ]),
                        ]),
                    Section::make(__('beneficiary.section.detailed_evaluation.labels.meetings'))
                        ->compact()
                        ->schema([
                            RepeatableEntry::make('meetings')
                                ->hiddenLabel()
                                ->table([
                                    TableColumn::make(__('nomenclature.labels.nr')),
                                    TableColumn::make(__('beneficiary.section.detailed_evaluation.labels.specialist')),
                                    TableColumn::make(__('beneficiary.section.detailed_evaluation.labels.date')),
                                    TableColumn::make(__('beneficiary.section.detailed_evaluation.labels.location')),
                                    TableColumn::make(__('beneficiary.section.detailed_evaluation.labels.observations')),
                                ])
                                ->schema([
                                    TextEntry::make('nr_crt')
                                        ->state(fn (TextEntry $entry): int => $this->getRepeatableItemIndex($entry) + 1),
                                    TextEntry::make('specialist'),
                                    DateEntry::make('date'),
                                    TextEntry::make('location'),
                                    TextEntry::make('observations'),
                                ]),
                        ]),
                ]),
        ];
    }

    /**
     * @return array<int, Notice|Section|Grid>
     */
    protected function getIdentitateBeneficiarSchema(): array
    {
        return [
            Notice::make('identity_redirect')
                ->state(__('beneficiary.section.identity.heading_description'))
                ->registerActions([
                    \Filament\Actions\Action::make('go_identity')
                        ->label(__('case.view.identity'))
                        ->url(fn (Beneficiary $record): string => CaseResource::getUrl('identity', ['record' => $record]))
                        ->link(),
                ]),
            Section::make(__('beneficiary.wizard.beneficiary.label'))
                ->schema(IdentityInfolist::getIdentityFieldsSchemaForEmbedding()),
        ];
    }

    /**
     * @return array<int, Notice|Section|TextEntry|RepeatableEntry>
     */
    protected function getIdentitateCopiiSchema(): array
    {
        return [
            Notice::make('children_redirect')
                ->state(__('beneficiary.section.identity.heading_description'))
                ->registerActions([
                    \Filament\Actions\Action::make('go_identity')
                        ->label(__('case.view.identity'))
                        ->url(fn (Beneficiary $record): string => CaseResource::getUrl('identity', ['record' => $record]))
                        ->link(),
                ]),
            Section::make(__('beneficiary.wizard.children.label'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('children_total_count')
                                ->label(__('field.children_total_count'))
                                ->placeholder('—')
                                ->numeric(),
                            TextEntry::make('children_accompanying_count')
                                ->label(__('field.children_accompanying_count'))
                                ->placeholder('—')
                                ->numeric(),
                        ]),
                    Section::make(__('enum.notifier.child'))
                        ->compact()
                        ->hidden(fn (Beneficiary $record): bool => (bool) $record->doesnt_have_children)
                        ->schema([
                            RepeatableEntry::make('children')
                                ->hiddenLabel()
                                ->table([
                                    TableColumn::make(__('nomenclature.labels.nr')),
                                    TableColumn::make(__('field.child_name')),
                                    TableColumn::make(__('field.age')),
                                    TableColumn::make(__('field.current_address')),
                                    TableColumn::make(__('field.child_status')),
                                ])
                                ->schema([
                                    TextEntry::make('nr_crt')
                                        ->state(fn (TextEntry $entry): int => $this->getRepeatableItemIndex($entry) + 1),
                                    TextEntry::make('name'),
                                    TextEntry::make('age'),
                                    TextEntry::make('current_address'),
                                    TextEntry::make('status'),
                                ]),
                        ]),
                    TextEntry::make('children_notes')
                        ->label(__('field.children_notes'))
                        ->placeholder('—')
                        ->columnSpanFull()
                        ->hidden(fn (Beneficiary $record): bool => (bool) $record->doesnt_have_children),
                ]),
        ];
    }

    /**
     * @return array<int, Group>
     */
    protected function getPartenerSchema(): array
    {
        return [
            Group::make()
                ->relationship('partner')
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                        ->headerActions([
                            EditAction::make('edit')
                                ->url(fn (BeneficiaryPartner $record): string => CaseResource::getUrl('edit_detailed_evaluation', ['record' => $record->beneficiary])),
                        ])
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('last_name')
                                        ->label(__('field.last_name'))
                                        ->placeholder('—'),
                                    TextEntry::make('first_name')
                                        ->label(__('field.first_name'))
                                        ->placeholder('—'),
                                    TextEntry::make('age')
                                        ->label(__('field.age'))
                                        ->placeholder('—'),
                                    EnumEntry::make('occupation')
                                        ->label(__('field.occupation'))
                                        ->placeholder('—'),
                                    Location::make(AddressType::LEGAL_RESIDENCE->value)
                                        ->relationship(AddressType::LEGAL_RESIDENCE->value)
                                        ->city()
                                        ->address()
                                        ->environment(false),
                                    Location::make(AddressType::EFFECTIVE_RESIDENCE->value)
                                        ->relationship(AddressType::EFFECTIVE_RESIDENCE->value)
                                        ->city()
                                        ->address(),
                                    TextEntry::make('observations')
                                        ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                                        ->placeholder('—')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ];
    }

    /**
     * @return array<int, Section|Group|RepeatableEntry>
     */
    protected function getEvaluareMultidisciplinaraSchema(): array
    {
        return [
            Group::make()
                ->relationship('multidisciplinaryEvaluation')
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.reasons_for_start_evaluation'))
                        ->compact()
                        ->headerActions([
                            EditAction::make('edit')
                                ->url(fn (MultidisciplinaryEvaluation $record): string => CaseResource::getUrl('edit_detailed_evaluation', ['record' => $record->beneficiary])),
                        ])
                        ->schema([
                            EnumEntry::make('applicant')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.applicant'))
                                ->placeholder('—'),
                            TextEntry::make('reporting_by')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.reporting_by'))
                                ->placeholder('—'),
                        ]),
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.beneficiary_needs'))
                        ->compact()
                        ->schema([
                            TextEntry::make('medical_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_need'))
                                ->placeholder('—'),
                            TextEntry::make('professional_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.professional_need'))
                                ->placeholder('—'),
                            TextEntry::make('emotional_and_psychological_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.emotional_and_psychological_need'))
                                ->placeholder('—'),
                            TextEntry::make('social_economic_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.social_economic_need'))
                                ->placeholder('—'),
                            TextEntry::make('legal_needs')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_needs'))
                                ->placeholder('—'),
                        ]),
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.family'))
                        ->compact()
                        ->schema([
                            TextEntry::make('extended_family')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.extended_family'))
                                ->placeholder('—'),
                            TextEntry::make('family_social_integration')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.family_social_integration'))
                                ->placeholder('—'),
                            TextEntry::make('income')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.income'))
                                ->placeholder('—'),
                            TextEntry::make('community_resources')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.community_resources'))
                                ->placeholder('—'),
                            TextEntry::make('house')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.house'))
                                ->placeholder('—'),
                        ]),
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.risk'))
                        ->compact()
                        ->schema([
                            TextEntry::make('risk')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.risk'))
                                ->placeholder('—')
                                ->columnSpanFull(),
                        ]),
                ]),
            Section::make(__('beneficiary.section.detailed_evaluation.heading.historic_violence'))
                ->compact()
                ->schema([
                    RepeatableEntry::make('violenceHistory')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('date_interval')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.date_interval'))
                                ->placeholder('—'),
                            TextEntry::make('significant_events')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.significant_events'))
                                ->placeholder('—')
                                ->html(),
                        ]),
                ]),
        ];
    }

    /**
     * @return array<int, Group>
     */
    protected function getRezultateSchema(): array
    {
        return [
            Group::make()
                ->relationship('detailedEvaluationResult')
                ->schema([
                    Section::make(__('beneficiary.wizard.results.label'))
                        ->headerActions([
                            EditAction::make('edit')
                                ->url(fn (DetailedEvaluationResult $record): string => CaseResource::getUrl('edit_detailed_evaluation', ['record' => $record->beneficiary])),
                        ])
                        ->schema([
                            TextEntry::make('recommendation_services')
                                ->label(__('beneficiary.section.detailed_evaluation.heading.recommendation_services'))
                                ->placeholder('—')
                                ->listWithLineBreaks(),
                            TextEntry::make('other_services_description')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.other_services'))
                                ->placeholder('—'),
                            TextEntry::make('recommendations_for_intervention_plan')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                                ->placeholder('—')
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    private function getRepeatableItemIndex(TextEntry $entry): int
    {
        $container = $entry->getContainer();
        $path = $container->getStatePath();
        $key = Str::afterLast($path, '.');

        return is_numeric($key) ? (int) $key : 0;
    }
}
