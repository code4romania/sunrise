<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases;

use App\Filament\Organizations\Resources\Cases\Pages\CreateCase;
use App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation\CreateCaseDetailedEvaluation;
use App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation\ViewCaseDetailedEvaluation;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseAggressor;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseAntecedents;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseChildren;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseDocuments;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseFlowPresentation;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseIdentity;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseMonitoring;
use App\Filament\Organizations\Resources\Cases\Pages\EditCasePersonalInformation;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseTeam;
use App\Filament\Organizations\Resources\Cases\Pages\InitialEvaluation\CreateCaseInitialEvaluation;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\CreateCaseInterventionPlan;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\CreateCaseMonthlyPlan;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\EditCaseMonthlyPlanDetails;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\ViewCaseInterventionPlan;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\ViewCaseInterventionService;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\ViewCaseMonthlyPlan;
use App\Filament\Organizations\Resources\Cases\Pages\ListCases;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCase;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCaseDocument;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCaseIdentity;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCaseModificationHistory;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCasePersonalInformation;
use App\Filament\Organizations\Resources\Cases\RelationManagers\EvaluateDetailsRelationManager;
use App\Filament\Organizations\Resources\Cases\Schemas\CaseInfolist;
use App\Filament\Organizations\Resources\Cases\Tables\CaseTable;
use App\Models\Beneficiary;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CaseResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static ?string $slug = 'cases';

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 1;

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->with([
                'managerTeam.user',
                'managerTeam.roleForDisplay',
                'lastMonitoring',
                'interventionPlan',
                'closeFile',
                'documents',
                'legal_residence.county',
                'legal_residence.city',
                'effective_residence.county',
                'effective_residence.city',
                'flowPresentation.referringInstitution',
                'flowPresentation.firstCalledInstitution',
                'flowPresentation.otherCalledInstitution',
                'flowPresentation',
                'details',
                'aggressors',
                'antecedents',
                'specialistsTeam.user',
                'specialistsTeam.roleForDisplay',
                'evaluateDetails',
                'children',
                'detailedEvaluationSpecialists',
                'meetings',
                'partner.legal_residence.county',
                'partner.legal_residence.city',
                'partner.effective_residence.county',
                'partner.effective_residence.city',
                'multidisciplinaryEvaluation',
                'violenceHistory',
                'detailedEvaluationResult',
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.beneficiaries._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.beneficiaries.cases');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->full_name;
    }

    public static function getModelLabel(): string
    {
        return __('beneficiary.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('beneficiary.label.plural');
    }

    public static function infolist(Schema $schema): Schema
    {
        return CaseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CaseTable::configure($table);
    }

    //    public static function getRelations(): array
    //    {
    //        return [
    //            'evaluateDetails' => EvaluateDetailsRelationManager::class,
    //        ];
    //    }

    public static function getPages(): array
    {
        return [
            'index' => ListCases::route('/'),
            'create' => CreateCase::route('/create'),
            'view' => ViewCase::route('/{record}'),
            'modification_history' => ViewCaseModificationHistory::route('/{record}/modification-history'),
            'identity' => ViewCaseIdentity::route('/{record}/identity'),
            'edit_identity' => EditCaseIdentity::route('/{record}/identity/edit'),
            'edit_children' => EditCaseChildren::route('/{record}/children/edit'),
            'view_personal_information' => ViewCasePersonalInformation::route('/{record}/personal'),
            'edit_personal_information' => EditCasePersonalInformation::route('/{record}/personal/edit'),
            'edit_aggressor' => EditCaseAggressor::route('/{record}/personal/aggressor/edit'),
            'edit_flow_presentation' => EditCaseFlowPresentation::route('/{record}/personal/flow-presentation/edit'),
            'edit_case_team' => EditCaseTeam::route('/{record}/case-team/edit'),
            'edit_case_documents' => EditCaseDocuments::route('/{record}/documents'),
            'view_case_document' => ViewCaseDocument::route('/{record}/documents/{document}'),
            'edit_case_monitoring' => EditCaseMonitoring::route('/{record}/monitoring'),
            'edit_antecedents' => EditCaseAntecedents::route('/{record}/personal/antecedents/edit'),
            'create_initial_evaluation' => CreateCaseInitialEvaluation::route('/{record}/initial-evaluation/create'),
            'edit_initial_evaluation' => CreateCaseInitialEvaluation::route('/{record}/initial-evaluation/edit'),
            'view_detailed_evaluation' => ViewCaseDetailedEvaluation::route('/{record}/detailed-evaluation'),
            'create_detailed_evaluation' => CreateCaseDetailedEvaluation::route('/{record}/detailed-evaluation/create'),
            'edit_detailed_evaluation' => CreateCaseDetailedEvaluation::route('/{record}/detailed-evaluation/edit'),
            'create_intervention_plan' => CreateCaseInterventionPlan::route('/{record}/intervention-plan/create'),
            'view_intervention_plan' => ViewCaseInterventionPlan::route('/{record}/intervention-plan'),
            'view_intervention_service' => ViewCaseInterventionService::route('/{record}/intervention-plan/services/{interventionService}'),
            'create_monthly_plan' => CreateCaseMonthlyPlan::route('/{case}/intervention-plan/monthly-plans/create'),
            'view_monthly_plan' => ViewCaseMonthlyPlan::route('/{record}/intervention-plan/monthly-plans/{monthlyPlan}'),
            'edit_monthly_plan_details' => EditCaseMonthlyPlanDetails::route('/{record}/intervention-plan/monthly-plans/{monthlyPlan}/edit-details'),
        ];
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
