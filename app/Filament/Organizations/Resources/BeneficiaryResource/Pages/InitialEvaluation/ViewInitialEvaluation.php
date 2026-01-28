<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs\Tab;
use App\Actions\BackAction;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiaryIdentity;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\Notice;
use App\Infolists\Components\SectionHeader;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewInitialEvaluation extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.initial_evaluation.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_initial_evaluation');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->persistTabInQueryString()
                ->columnSpanFull()
                ->schema([
                    Tab::make(__('beneficiary.wizard.details.label'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.wizard.details.label'))
                                ->headerActions([
                                    EditAction::make()
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_details',
                                            ['record' => $record]
                                        )),
                                ])
                                ->schema(EditEvaluationDetails::getInfoListSchema())]),

                    Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                        ->maxWidth('3xl')
                        ->schema(ViewBeneficiaryIdentity::identitySchemaForOtherPage($this->record)),

                    Tab::make(__('beneficiary.section.identity.tab.children'))
                        ->maxWidth('3xl')
                        ->schema(ViewBeneficiaryIdentity::childrenSchemaForOtherPage($this->record)),

                    Tab::make(__('beneficiary.wizard.violence.label'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.wizard.violence.label'))
                                ->headerActions([
                                    EditAction::make()
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_violence',
                                            ['record' => $record]
                                        )),
                                ])
                                ->schema(EditViolence::getInfoListSchema())]),
                    Tab::make(__('beneficiary.wizard.risk_factors.label'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Notice::make('riskFactors.risk_level'),

                                    SectionHeader::make('riskFactors')
                                        ->state(__('beneficiary.wizard.risk_factors.label'))
                                        ->action(
                                            EditAction::make()
                                                ->url(BeneficiaryResource::getUrl(
                                                    'edit_initial_evaluation_risk_factors',
                                                    ['record' => $this->getRecord()]
                                                )),
                                        ),

                                    ...EditRiskFactors::getInfoListSchema(),
                                ]),
                        ]),
                    Tab::make(__('beneficiary.wizard.requested_services.label'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.wizard.requested_services.label'))
                                ->headerActions([
                                    EditAction::make()
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_requested_services',
                                            ['record' => $record]
                                        )),
                                ])
                                ->schema($this->getRequestedServicesInfoListSchema())]),
                    Tab::make(__('beneficiary.wizard.beneficiary_situation.label'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.wizard.beneficiary_situation.label'))
                                ->headerActions([
                                    EditAction::make()
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_beneficiary_situation',
                                            ['record' => $record]
                                        )),
                                ])
                                ->schema(EditBeneficiarySituation::getInfoListSchema())]),
                ]),
        ]);
    }

    public function getRequestedServicesInfoListSchema(): array
    {
        return [
            Group::make()
                ->relationship('requestedServices')
                ->schema([
                    TextEntry::make('requested_services')
                        ->label(__('beneficiary.section.initial_evaluation.heading.types_of_requested_services'))
                        ->listWithLineBreaks(),
                    TextEntry::make('other_services_description')
                        ->hiddenLabel()
                        ->placeholder(__('beneficiary.placeholder.other_services')),
                ]),

        ];
    }
}
