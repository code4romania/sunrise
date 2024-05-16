<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewInitialEvaluation extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->schema([
                    Tabs\Tab::make(__('beneficiary.wizard.details.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.details.label'))
                                ->headerActions([
                                    Action::make('edit')
                                        ->label(__('general.action.edit'))
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_details',
                                            ['record' => $record]
                                        ))
                                        ->link(),
                                ])
                                ->schema(EditEvaluationDetails::getInfoListSchema())]),
                    Tabs\Tab::make(__('beneficiary.wizard.violence.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.violence.label'))
                                ->headerActions([
                                    Action::make('edit')
                                        ->label(__('general.action.edit'))
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_violence',
                                            ['record' => $record]
                                        ))
                                        ->link(),
                                ])
                                ->schema(EditViolence::getInfoListSchema())]),
                    Tabs\Tab::make(__('beneficiary.wizard.risk_factors.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.risk_factors.label'))
                                ->headerActions([
                                    Action::make('edit')
                                        ->label(__('general.action.edit'))
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_risk_factors',
                                            ['record' => $record]
                                        ))
                                        ->link(),
                                ])
                                ->schema(EditRiskFactors::getInfoListSchema())]),
                    Tabs\Tab::make(__('beneficiary.wizard.requested_services.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.requested_services.label'))
                                ->headerActions([
                                    Action::make('edit')
                                        ->label(__('general.action.edit'))
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_requested_services',
                                            ['record' => $record]
                                        ))
                                        ->link(),
                                ])
                                ->schema(EditRequestedServices::getInfoListSchema())]),
                    Tabs\Tab::make(__('beneficiary.wizard.beneficiary_situation.label'))
                        ->schema([
                            Section::make(__('beneficiary.wizard.beneficiary_situation.label'))
                                ->headerActions([
                                    Action::make('edit')
                                        ->label(__('general.action.edit'))
                                        ->url(fn ($record) => BeneficiaryResource::getUrl(
                                            'edit_initial_evaluation_beneficiary_situation',
                                            ['record' => $record]
                                        ))
                                        ->link(),
                                ])
                                ->schema(EditBeneficiarySituation::getInfoListSchema())]),
                ]),
        ]);
    }
}
