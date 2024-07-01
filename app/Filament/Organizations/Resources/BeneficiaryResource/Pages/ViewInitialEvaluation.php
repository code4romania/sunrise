<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewInitialEvaluation extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbsForInitialEvaluation();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->schema([
                    Tabs\Tab::make(__('beneficiary.wizard.details.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.details.label'))
                                ->headerActions([
                                    BeneficiaryResource\Actions\Edit::make('edit')
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_details',
                                            ['record' => $record]
                                        )),
                                ])
                                ->schema(EditEvaluationDetails::getInfoListSchema())]),

                    Tabs\Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                        ->schema(ViewBeneficiaryIdentity::getBeneficiaryIdentityFormSchema()),

                    Tabs\Tab::make(__('beneficiary.section.identity.tab.children'))
                        ->schema(ViewBeneficiaryIdentity::getChildrenIdentityFormSchema()),

                    Tabs\Tab::make(__('beneficiary.wizard.violence.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.violence.label'))
                                ->headerActions([
                                    BeneficiaryResource\Actions\Edit::make('edit')
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_violence',
                                            ['record' => $record]
                                        )),
                                ])
                                ->schema(EditViolence::getInfoListSchema())]),
                    Tabs\Tab::make(__('beneficiary.wizard.risk_factors.label'))
                        ->schema([
                            Section::make(fn ($record) => $record->riskFactors->risk_level->label() ??
                                __('beneficiary.wizard.risk_factors.label'))
                                ->schema([
                                    Section::make(__('beneficiary.wizard.risk_factors.label'))
                                        ->headerActions([
                                            BeneficiaryResource\Actions\Edit::make('edit')
                                                ->url(fn ($record) => BeneficiaryResource::getUrl(
                                                    'edit_initial_evaluation_risk_factors',
                                                    ['record' => $record]
                                                )),
                                        ])
                                        ->schema(EditRiskFactors::getInfoListSchema())]),
                        ]),
                    Tabs\Tab::make(__('beneficiary.wizard.requested_services.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.requested_services.label'))
                                ->headerActions([
                                    BeneficiaryResource\Actions\Edit::make('edit')
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_requested_services',
                                            ['record' => $record]
                                        )),
                                ])
                                ->schema(EditRequestedServices::getInfoListSchema())]),
                    Tabs\Tab::make(__('beneficiary.wizard.beneficiary_situation.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.beneficiary_situation.label'))
                                ->headerActions([
                                    BeneficiaryResource\Actions\Edit::make('edit')
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_beneficiary_situation',
                                            ['record' => $record]
                                        )),
                                ])
                                ->schema(EditBeneficiarySituation::getInfoListSchema())]),
                ]),
        ]);
    }
}
