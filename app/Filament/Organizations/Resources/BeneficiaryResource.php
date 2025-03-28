<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Enums\CaseStatus;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages\ListBeneficiaryHistories;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages\ViewBeneficiaryHistories;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ListSpecialists;
use App\Filament\Organizations\Resources\DocumentResource\Pages\ListDocuments;
use App\Filament\Organizations\Resources\DocumentResource\Pages\ViewDocument;
use App\Filament\Organizations\Resources\InterventionPlanResource\Pages\CreateInterventionPlan;
use App\Filament\Organizations\Resources\InterventionPlanResource\Pages\ViewInterventionPlan;
use App\Filament\Organizations\Resources\MonitoringResource\Pages as MonitoringResourcePages;
use App\Filters\DateFilter;
use App\Models\Beneficiary;
use App\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

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
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'managerTeam',
                        'lastMonitoring',
                        // used for permissions
                        'specialistsMembers',
                    ])
                    ->whereUserHasAccess();
            })
            ->columns([
                TextColumn::make('organization_beneficiary_id')
                    ->label(__('field.case_id'))
                    ->sortable()
                    ->searchable(true, fn (Builder $query, $search) => $query->where('beneficiaries.id', 'LIKE', '%' . $search . '%')),

                TextColumn::make('full_name')
                    ->label(__('field.beneficiary'))
                    ->description(fn ($record) => $record->initial_id ? __('beneficiary.labels.reactivated') : '')
                    ->sortable()
                    ->searchable(true, fn (Builder $query, $search) => $query->where('beneficiaries.full_name', 'LIKE', '%' . $search . '%')),

                TextColumn::make('created_at')
                    ->label(__('field.open_at'))
                    ->date()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('lastMonitoring.date')
                    ->label(__('field.last_evaluated_at'))
                    ->date()
                    ->toggleable(),

                TextColumn::make('managerTeam.user.full_name')
                    ->label(__('beneficiary.labels.case_manager'))
                    ->toggleable()
                    ->formatStateUsing(
                        fn ($state) => collect(explode(',', $state))
                            ->map(fn ($item) => trim($item))
                            ->unique()
                            ->join(', ')
                    ),

                TextColumn::make('status')
                    ->label(__('field.status'))
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('general.action.view_details')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('field.status'))
                    ->options(CaseStatus::options())
                    ->modifyQueryUsing(fn (Builder $query, $state) => $state['value'] ? $query->where('beneficiaries.status', $state) : $query),

                SelectFilter::make('case_manager')
                    ->label(__('beneficiary.labels.case_manager'))
                    ->searchable()
                    ->preload()
                    ->relationship('managerTeam.user', 'full_name'),

                DateFilter::make('created_at')
                    ->label(__('field.open_at'))
                    ->attribute('beneficiaries.created_at'),

                DateFilter::make('monitorings.date')
                    ->label(__('field.last_evaluated_at'))
                    ->modifyQueryUsing(
                        fn (Builder $query, array $state) => $query
                            ->when(data_get($state, 'date_from'), function (Builder $query, string $date) {
                                $query->whereHas('lastMonitoring', fn (Builder $query) => $query->whereDate('date', '>=', $date));
                            })
                            ->when(data_get($state, 'date_until'), function (Builder $query, string $date) {
                                $query->whereHas('lastMonitoring', fn (Builder $query) => $query->whereDate('date', '<=', $date));
                            })
                    ),
            ])
            ->paginationPageOptions([10, 20, 40, 60, 80, 100])
            ->defaultPaginationPageOption(20)
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordRouteKeyName(): ?string
    {
        return 'ulid';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeneficiaries::route('/'),
            'create' => Pages\CreateBeneficiary::route('/create/{parent?}'),
            'view' => Pages\ViewBeneficiary::route('/{record:ulid}'),

            'view_identity' => Pages\ViewBeneficiaryIdentity::route('/{record:ulid}/identity'),
            'edit_identity' => Pages\EditBeneficiaryIdentity::route('/{record:ulid}/identity/edit'),
            'edit_children' => Pages\EditChildrenIdentity::route('{record:ulid}/children/edit'),
            'view_personal_information' => Pages\ViewBeneficiaryPersonalInformation::route('/{record:ulid}/personal'),
            'edit_personal_information' => Pages\EditBeneficiaryPersonalInformation::route('/{record:ulid}/personal/edit'),
            'edit_aggressor' => Pages\EditAggressor::route('/{record:ulid}/aggressor/edit'),
            'edit_antecedents' => Pages\EditAntecedents::route('{record:ulid}/antecedents/edit'),
            'edit_flow_presentation' => Pages\EditFlowPresentation::route('{record:ulid}/flowPresentation/edit'),

            'view_initial_evaluation' => InitialEvaluation\ViewInitialEvaluation::route('/{record:ulid}/initialEvaluation'),
            'create_initial_evaluation' => InitialEvaluation\CreateInitialEvaluation::route('/{record:ulid}/initialEvaluation/create'),
            'edit_initial_evaluation_details' => InitialEvaluation\EditEvaluationDetails::route('/{record:ulid}/initialEvaluation/details/edit'),
            'edit_initial_evaluation_violence' => InitialEvaluation\EditViolence::route('/{record:ulid}/initialEvaluation/violence/edit'),
            'edit_initial_evaluation_risk_factors' => InitialEvaluation\EditRiskFactors::route('/{record:ulid}/initialEvaluation/riskFactors/edit'),
            'edit_initial_evaluation_requested_services' => InitialEvaluation\EditRequestedServices::route('/{record:ulid}/initialEvaluation/requestedServices/edit'),
            'edit_initial_evaluation_beneficiary_situation' => InitialEvaluation\EditBeneficiarySituation::route('/{record:ulid}/initialEvaluation/beneficiarySituation/edit'),

            'view_detailed_evaluation' => DetailedEvaluation\ViewDetailedEvaluation::route('/{record:ulid}/detailedEvaluation'),
            'create_detailed_evaluation' => DetailedEvaluation\CreateDetailedEvaluation::route('/{record:ulid}/detailedEvaluation/create'),
            'edit_detailed_evaluation' => DetailedEvaluation\EditDetailedEvaluation::route('/{record:ulid}/detailedEvaluation/edit'),
            'edit_beneficiary_partner' => DetailedEvaluation\EditBeneficiaryPartner::route('/{record:ulid}/beneficiaryPartner/edit'),
            'edit_multidisciplinary_evaluation' => DetailedEvaluation\EditMultidisciplinaryEvaluation::route('/{record:ulid}/multidisciplinaryEvaluation/edit'),
            'edit_detailed_evaluation_result' => DetailedEvaluation\EditDetailedEvaluationResult::route('/{record:ulid}/detailedEvaluationResult/edit'),

            'view_specialists' => ListSpecialists::route('/{record:ulid}/specialists'),

            'documents.index' => ListDocuments::route('/{parent:ulid}/documents'),
            'documents.view' => ViewDocument::route('/{parent:ulid}/documents/{record}'),

            'monitorings.create' => MonitoringResourcePages\CreateMonitoring::route('/{parent:ulid}/monitoring/create/{copyLastFile?}'),
            'monitorings.index' => MonitoringResourcePages\ListMonitoring::route('/{parent:ulid}/monitoring'),
            'monitorings.view' => MonitoringResourcePages\ViewMonitoring::route('/{parent:ulid}/monitoring/{record}'),
            'monitoring.edit_details' => MonitoringResourcePages\EditDetails::route('/{parent:ulid}/monitoring/{record}/editDetails'),
            'monitoring.edit_children' => MonitoringResourcePages\EditChildren::route('/{parent:ulid}/monitoring/{record}/editChildren'),
            'monitoring.edit_general' => MonitoringResourcePages\EditGeneral::route('/{parent:ulid}/monitoring/{record}/editGeneral'),

            'beneficiary-histories.index' => ListBeneficiaryHistories::route('{parent:ulid}/history'),
            'beneficiary-histories.view' => ViewBeneficiaryHistories::route('{parent:ulid}/history/{record}'),

            'create_close_file' => CloseFile\CreateCloseFile::route('/{record:ulid}/createCloseFile'),
            'view_close_file' => CloseFile\ViewCloseFile::route('/{record:ulid}/closeFile'),
            'edit_close_file_details' => CloseFile\EditCloseFileDetails::route('{record:ulid}/closeFile/editDetails'),
            'edit_close_file_general_details' => CloseFile\EditCloseFileGeneralDetails::route('{record:ulid}/closeFile/editGeneralDetails'),

            'create_intervention_plan' => CreateInterventionPlan::route('/{parent:ulid}/createInterventionPlan'),
            'view_intervention_plan' => ViewInterventionPlan::route('/{parent:ulid}/interventionPlan/{record}'),
        ];
    }
}
