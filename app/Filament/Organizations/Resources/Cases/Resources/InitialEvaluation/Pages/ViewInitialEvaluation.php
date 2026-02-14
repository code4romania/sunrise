<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\InitialEvaluationResource;
use App\Filament\Organizations\Resources\Cases\Schemas\IdentityInfolist;
use App\Filament\Organizations\Schemas\BeneficiaryResource\InitialEvaluationSchema;
use App\Infolists\Components\Notice;
use App\Models\Beneficiary;
use App\Models\EvaluateDetails;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class ViewInitialEvaluation extends ViewRecord
{
    protected static string $resource = InitialEvaluationResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.initial_evaluation.title');
    }

    protected function getHeaderActions(): array
    {
        $parent = $this->getParentRecord();

        return [
            BackAction::make()
                ->url($parent instanceof Beneficiary
                    ? CaseResource::getUrl('view', ['record' => $parent])
                    : CaseResource::getUrl('index')),
            EditAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();
        $record = $this->getRecord();

        $breadcrumbs = [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
        ];
        if ($parent instanceof Beneficiary) {
            $breadcrumbs[CaseResource::getUrl('view', ['record' => $parent])] = $parent->getBreadcrumb();
        }
        $breadcrumbs[''] = $record instanceof EvaluateDetails
            ? __('beneficiary.page.initial_evaluation.title')
            : '';

        return $breadcrumbs;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('beneficiary.wizard.details.label'))
                            ->maxWidth('3xl')
                            ->schema(InitialEvaluationSchema::getEvaluationDetailsInfolistComponents()),

                        Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                            ->maxWidth('3xl')
                            ->schema($this->getIdentitateBeneficiarSchema()),

                        Tab::make(__('beneficiary.section.identity.tab.children'))
                            ->maxWidth('3xl')
                            ->schema($this->getIdentitateCopiiSchema()),

                        Tab::make(__('beneficiary.wizard.violence.label'))
                            ->maxWidth('3xl')
                            ->schema(InitialEvaluationSchema::getViolenceInfolistComponents()),

                        Tab::make(__('beneficiary.wizard.risk_factors.label'))
                            ->maxWidth('3xl')
                            ->schema(InitialEvaluationSchema::getRiskFactorsInfolistComponents()),

                        Tab::make(__('beneficiary.wizard.requested_services.label'))
                            ->maxWidth('3xl')
                            ->schema(InitialEvaluationSchema::getRequestedServicesInfolistComponents()),

                        Tab::make(__('beneficiary.wizard.beneficiary_situation.label'))
                            ->maxWidth('3xl')
                            ->schema(InitialEvaluationSchema::getBeneficiarySituationInfolistComponents()),
                    ]),
            ]);
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        $record = $this->getRecord();
        $beneficiary = $record instanceof EvaluateDetails ? $record->beneficiary : $record;

        return parent::defaultInfolist($schema)->record($beneficiary ?? $record);
    }

    /**
     * @return array<int, Notice|Section>
     */
    protected function getIdentitateBeneficiarSchema(): array
    {
        $beneficiary = $this->getParentRecord();

        return [
            Notice::make('identity_redirect')
                ->state(__('beneficiary.section.identity.heading_description'))
                ->registerActions([
                    \Filament\Actions\Action::make('go_identity')
                        ->label(__('case.view.identity'))
                        ->record($beneficiary instanceof Beneficiary ? $beneficiary : null)
                        ->url(fn (): string => $beneficiary instanceof Beneficiary
                            ? CaseResource::getUrl('identity', ['record' => $beneficiary])
                            : '#')
                        ->link(),
                ]),
            Section::make(__('beneficiary.section.identity.tab.beneficiary'))
                ->schema(IdentityInfolist::getIdentityFieldsSchemaForEmbedding()),
        ];
    }

    /**
     * @return array<int, Notice|Section|TextEntry|RepeatableEntry>
     */
    protected function getIdentitateCopiiSchema(): array
    {
        $beneficiary = $this->getParentRecord();

        return [
            Notice::make('children_redirect')
                ->state(__('beneficiary.section.identity.heading_description'))
                ->registerActions([
                    \Filament\Actions\Action::make('go_identity')
                        ->label(__('case.view.identity'))
                        ->record($beneficiary instanceof Beneficiary ? $beneficiary : null)
                        ->url(fn (): string => $beneficiary instanceof Beneficiary
                            ? CaseResource::getUrl('identity', ['record' => $beneficiary])
                            : '#')
                        ->link(),
                ]),
            Section::make(__('beneficiary.section.identity.tab.children'))
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

    private function getRepeatableItemIndex(TextEntry $entry): int
    {
        $container = $entry->getContainer();
        $path = $container->getStatePath();
        $key = Str::afterLast($path, '.');

        return is_numeric($key) ? (int) $key : 0;
    }
}
