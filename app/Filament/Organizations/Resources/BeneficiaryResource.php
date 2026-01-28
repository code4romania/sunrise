<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages\ListBeneficiaryHistories;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages\ViewBeneficiaryHistories;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile\CreateCloseFile;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile\EditCloseFileDetails;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile\EditCloseFileGeneralDetails;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile\ViewCloseFile;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CreateBeneficiary;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation\CreateDetailedEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation\EditBeneficiaryPartner;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation\EditDetailedEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation\EditDetailedEvaluationResult;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation\EditMultidisciplinaryEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation\ViewDetailedEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\EditAggressor;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\EditAntecedents;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\EditBeneficiaryIdentity;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\EditBeneficiaryPersonalInformation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\EditChildrenIdentity;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\EditFlowPresentation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation\CreateInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation\EditBeneficiarySituation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation\EditEvaluationDetails;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation\EditRequestedServices;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation\EditRiskFactors;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation\EditViolence;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation\ViewInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ListBeneficiaries;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ListSpecialists;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiary;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiaryIdentity;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiaryPersonalInformation;
use App\Filament\Organizations\Resources\BeneficiaryResource\RelationManagers\DocumentsRelationManager;
use App\Filament\Organizations\Resources\InterventionPlanResource\Pages\CreateInterventionPlan;
use App\Filament\Organizations\Resources\InterventionPlanResource\Pages\ViewInterventionPlan;
use App\Filament\Organizations\Schemas\BeneficiaryResourceSchema;
use App\Models\Beneficiary;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';

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
        return BeneficiaryResourceSchema::table($table);
    }

    public static function getRelations(): array
    {
        return [
            'documents' => DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBeneficiaries::route('/'),
            'create' => CreateBeneficiary::route('/create/{parent?}'),
            'view' => ViewBeneficiary::route('/{record}'),

            'view_identity' => ViewBeneficiaryIdentity::route('/{record}/identity'),
            'edit_identity' => EditBeneficiaryIdentity::route('/{record}/identity/edit'),
            'edit_children' => EditChildrenIdentity::route('{record}/children/edit'),
            'view_personal_information' => ViewBeneficiaryPersonalInformation::route('/{record}/personal'),
            'edit_personal_information' => EditBeneficiaryPersonalInformation::route('/{record}/personal/edit'),
            'edit_aggressor' => EditAggressor::route('/{record}/aggressor/edit'),
            'edit_antecedents' => EditAntecedents::route('{record}/antecedents/edit'),
            'edit_flow_presentation' => EditFlowPresentation::route('{record}/flowPresentation/edit'),

            'view_initial_evaluation' => ViewInitialEvaluation::route('/{record}/initialEvaluation'),
            'create_initial_evaluation' => CreateInitialEvaluation::route('/{record}/initialEvaluation/create'),
            'edit_initial_evaluation_details' => EditEvaluationDetails::route('/{record}/initialEvaluation/details/edit'),
            'edit_initial_evaluation_violence' => EditViolence::route('/{record}/initialEvaluation/violence/edit'),
            'edit_initial_evaluation_risk_factors' => EditRiskFactors::route('/{record}/initialEvaluation/riskFactors/edit'),
            'edit_initial_evaluation_requested_services' => EditRequestedServices::route('/{record}/initialEvaluation/requestedServices/edit'),
            'edit_initial_evaluation_beneficiary_situation' => EditBeneficiarySituation::route('/{record}/initialEvaluation/beneficiarySituation/edit'),

            'view_detailed_evaluation' => ViewDetailedEvaluation::route('/{record}/detailedEvaluation'),
            'create_detailed_evaluation' => CreateDetailedEvaluation::route('/{record}/detailedEvaluation/create'),
            'edit_detailed_evaluation' => EditDetailedEvaluation::route('/{record}/detailedEvaluation/edit'),
            'edit_beneficiary_partner' => EditBeneficiaryPartner::route('/{record}/beneficiaryPartner/edit'),
            'edit_multidisciplinary_evaluation' => EditMultidisciplinaryEvaluation::route('/{record}/multidisciplinaryEvaluation/edit'),
            'edit_detailed_evaluation_result' => EditDetailedEvaluationResult::route('/{record}/detailedEvaluationResult/edit'),

            'view_specialists' => ListSpecialists::route('/{record}/specialists'),

            'beneficiary-histories.index' => ListBeneficiaryHistories::route('{parent}/history'),
            'beneficiary-histories.view' => ViewBeneficiaryHistories::route('{parent}/history/{record}'),

            'create_close_file' => CreateCloseFile::route('/{record}/createCloseFile'),
            'view_close_file' => ViewCloseFile::route('/{record}/closeFile'),
            'edit_close_file_details' => EditCloseFileDetails::route('{record}/closeFile/editDetails'),
            'edit_close_file_general_details' => EditCloseFileGeneralDetails::route('{record}/closeFile/editGeneralDetails'),

            'create_intervention_plan' => CreateInterventionPlan::route('/{parent}/createInterventionPlan'),
            'view_intervention_plan' => ViewInterventionPlan::route('/{parent}/interventionPlan/{record}'),
        ];
    }
}
