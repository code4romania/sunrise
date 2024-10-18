<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Enums\CaseStatus;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages\ListBeneficiaryHistories;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages\ViewBeneficiaryHistories;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CreateDetailedEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ListSpecialists;
use App\Filament\Organizations\Resources\DocumentResource\Pages\ListDocuments;
use App\Filament\Organizations\Resources\DocumentResource\Pages\ViewDocument;
use App\Filament\Organizations\Resources\MonitoringResource\Pages as MonitoringResourcePages;
use App\Models\Beneficiary;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $slug = 'cases';

    protected static ?int $navigationSort = 10;

    public static function getRecordTitle(?Model $record): string|null|Htmlable
    {
        return $record->full_name;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.beneficiaries._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.beneficiaries.cases');
    }

    public static function getModelLabel(): string
    {
        return __('beneficiary.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('beneficiary.label.plural');
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->full_name;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('field.case_id'))
                    ->shrink()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label(__('field.beneficiary'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('field.open_at'))
                    ->date()
                    ->shrink()
                    ->sortable(),

                TextColumn::make('last_evaluated_at')
                    ->label(__('field.last_evaluated_at'))
                    ->date()
                    ->shrink()
                    ->sortable(),

                TextColumn::make('last_serviced_at')
                    ->label(__('field.last_serviced_at'))
                    ->date()
                    ->shrink()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('field.status'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        CaseStatus::ACTIVE => 'success',
                        CaseStatus::REACTIVATED => 'success',
                        CaseStatus::MONITORED => 'warning',
                        CaseStatus::CLOSED => 'gray',
                        default => dd($state)
                    })
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->shrink(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create'),
            'view' => Pages\ViewBeneficiary::route('/{record}'),

            'view_identity' => Pages\ViewBeneficiaryIdentity::route('/{record}/identity'),
            'edit_identity' => Pages\EditBeneficiaryIdentity::route('/{record}/identity/edit'),
            'edit_children' => Pages\EditChildrenIdentity::route('{record}/children/edit'),
            'view_personal_information' => Pages\ViewBeneficiaryPersonalInformation::route('/{record}/personal'),
            'edit_personal_information' => Pages\EditBeneficiaryPersonalInformation::route('/{record}/personal/edit'),
            'edit_aggressor' => Pages\EditAggressor::route('/{record}/aggressor/edit'),
            'edit_antecedents' => Pages\EditAntecedents::route('{record}/antecedents/edit'),
            'edit_flow_presentation' => Pages\EditFlowPresentation::route('{record}/flowPresentation/edit'),

            'view_initial_evaluation' => Pages\ViewInitialEvaluation::route('/{record}/initialEvaluation'),
            'create_initial_evaluation' => Pages\CreateInitialEvaluation::route('/{record}/initialEvaluation/create'),
            'edit_initial_evaluation_details' => Pages\EditEvaluationDetails::route('/{record}/initialEvaluation/details/edit'),
            'edit_initial_evaluation_violence' => Pages\EditViolence::route('/{record}/initialEvaluation/violence/edit'),
            'edit_initial_evaluation_risk_factors' => Pages\EditRiskFactors::route('/{record}/initialEvaluation/riskFactors/edit'),
            'edit_initial_evaluation_requested_services' => Pages\EditRequestedServices::route('/{record}/initialEvaluation/requestedServices/edit'),
            'edit_initial_evaluation_beneficiary_situation' => Pages\EditBeneficiarySituation::route('/{record}/initialEvaluation/beneficiarySituation/edit'),

            'view_detailed_evaluation' => Pages\ViewDetailedEvaluation::route('/{record}/detailedEvaluation'),
            'create_detailed_evaluation' => CreateDetailedEvaluation::route('/{record}/detailedEvaluation/create'),
            'edit_detailed_evaluation' => Pages\EditDetailedEvaluation::route('/{record}/detailedEvaluation/edit'),
            'edit_beneficiary_partner' => Pages\EditBeneficiaryPartner::route('/{record}/beneficiaryPartner/edit'),
            'edit_multidisciplinary_evaluation' => Pages\EditMultidisciplinaryEvaluation::route('/{record}/multidisciplinaryEvaluation/edit'),
            'edit_detailed_evaluation_result' => Pages\EditDetailedEvaluationResult::route('/{record}/detailedEvaluationResult/edit'),

            'view_specialists' => ListSpecialists::route('/{record}/specialists'),

            'documents.index' => ListDocuments::route('/{parent}/documents'),
            'documents.view' => ViewDocument::route('/{parent}/documents/{record}'),


            'monitorings.create' => MonitoringResourcePages\CreateMonitoring::route('/{parent}/monitoring/create/{copyLastFile?}'),
            'monitorings.index' => MonitoringResourcePages\ListMonitoring::route('/{parent}/monitoring'),
            'monitorings.view' => MonitoringResourcePages\ViewMonitoring::route('/{parent}/monitoring/{record}'),
            'monitoring.edit_details' => MonitoringResourcePages\EditDetails::route('/{parent}/monitoring/{record}/editDetails'),
            'monitoring.edit_children' => MonitoringResourcePages\EditChildren::route('/{parent}/monitoring/{record}/editChildren'),
            'monitoring.edit_general' => MonitoringResourcePages\EditGeneral::route('/{parent}/monitoring/{record}/editGeneral'),
            'beneficiary-histories.index' => ListBeneficiaryHistories::route('{parent}/history'),
            'beneficiary-histories.view' => ViewBeneficiaryHistories::route('{parent}/history/{record}'),

            'create_close_file' => CloseFile\CreateCloseFile::route('/{record}/createCloseFile'),
            'view_close_file' => CloseFile\ViewCloseFile::route('/{record}/closeFile'),
            'edit_close_file_details' => CloseFile\EditCloseFileDetails::route('{record}/closeFile/editDetails'),
            'edit_close_file_general_details' => CloseFile\EditCloseFileGeneralDetails::route('{record}/closeFile/editGeneralDetails'),

        ];
    }
}
