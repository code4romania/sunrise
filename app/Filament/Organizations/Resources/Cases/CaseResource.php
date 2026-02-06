<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases;

use App\Filament\Organizations\Resources\Cases\Pages\CreateCase;
use App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation\CreateCaseDetailedEvaluation;
use App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation\ViewCaseDetailedEvaluation;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseAggressor;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseChildren;
use App\Filament\Organizations\Resources\Cases\Pages\EditCaseIdentity;
use App\Filament\Organizations\Resources\Cases\Pages\EditCasePersonalInformation;
use App\Filament\Organizations\Resources\Cases\Pages\InitialEvaluation\CreateCaseInitialEvaluation;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\CreateCaseInterventionPlan;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\ViewCaseInterventionPlan;
use App\Filament\Organizations\Resources\Cases\Pages\ListCases;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCase;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCaseIdentity;
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
                'managerTeam.role',
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
                'specialistsTeam.role',
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

    public static function getRelations(): array
    {
        return [
            'evaluateDetails' => EvaluateDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCases::route('/'),
            'create' => CreateCase::route('/create'),
            'view' => ViewCase::route('/{record}'),
            'identity' => ViewCaseIdentity::route('/{record}/identity'),
            'edit_identity' => EditCaseIdentity::route('/{record}/identity/edit'),
            'edit_children' => EditCaseChildren::route('/{record}/children/edit'),
            'view_personal_information' => ViewCasePersonalInformation::route('/{record}/personal'),
            'edit_personal_information' => EditCasePersonalInformation::route('/{record}/personal/edit'),
            'edit_aggressor' => EditCaseAggressor::route('/{record}/personal/aggressor/edit'),
            'create_initial_evaluation' => CreateCaseInitialEvaluation::route('/{record}/initial-evaluation/create'),
            'edit_initial_evaluation' => CreateCaseInitialEvaluation::route('/{record}/initial-evaluation/edit'),
            'view_detailed_evaluation' => ViewCaseDetailedEvaluation::route('/{record}/detailed-evaluation'),
            'create_detailed_evaluation' => CreateCaseDetailedEvaluation::route('/{record}/detailed-evaluation/create'),
            'edit_detailed_evaluation' => CreateCaseDetailedEvaluation::route('/{record}/detailed-evaluation/edit'),
            'create_intervention_plan' => CreateCaseInterventionPlan::route('/{record}/intervention-plan/create'),
            'view_intervention_plan' => ViewCaseInterventionPlan::route('/{record}/intervention-plan'),
        ];
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
