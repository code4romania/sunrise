<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Enums\CaseStatus;
use App\Enums\Role;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CreateDetailedEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ListSpecialists;
use App\Filament\Organizations\Resources\DocumentResource\Pages\ListDocuments;
use App\Filament\Organizations\Resources\DocumentResource\Pages\ViewDocument;
use App\Filters\DateFilter;
use App\Models\Beneficiary;
use App\Models\User;
use App\Tables\Filters\SelectFilter;
use DB;
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
        return $table->modifyQueryUsing(fn(Builder $query)=> $query->with('managerTeam'))
//            ->modifyQueryUsing(
//                fn (Builder $query) => $query->with('team.user')
//                    ->leftJoin('case_teams', 'beneficiaries.id', '=', 'case_teams.beneficiary_id')
//                    ->leftJoin('users', 'case_teams.user_id', '=', 'users.id')
//                    ->distinct('beneficiaries.id')
//                    ->select('beneficiaries.*')
//                    ->addSelect(DB::raw("
//            IF(JSON_CONTAINS(case_teams.roles, '\"manager\"'), CONCAT_WS(' ', users.first_name, users.last_name), NULL) as manager_name
//        "))
//            )
            ->columns([
                TextColumn::make('id')
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

                TextColumn::make('last_evaluated_at')
                    ->label(__('field.last_evaluated_at'))
                    ->date()
                    ->toggleable(),
                //                    ->sortable(),

                //TODO Change WIth FULL name
                TextColumn::make('managerTeam.user.first_name')
                    ->label(Role::MANGER->getLabel())
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('field.status'))
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('field.status'))
                    ->options(CaseStatus::options())
                    ->modifyQueryUsing(fn (Builder $query, $state) => $state['value'] ? $query->where('beneficiaries.status', $state) : $query),

                SelectFilter::make('case_manager')
                    ->label(Role::MANGER->getLabel())
                    ->options(fn () => User::getTenantOrganizationUsers())
                    ->modifyQueryUsing(fn (Builder $query, $state) => $state['value'] ?
                            $query->where('case_teams.user_id', $state)
                                ->whereJsonContains('case_teams.roles', Role::MANGER)
                        : $query),

                DateFilter::make('created_at')
                    ->label(__('field.open_at'))
                    ->attribute('beneficiaries.created_at'),

                //                DateFilter::make('last_evaluated_at')
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

            'create_close_file' => CloseFile\CreateCloseFile::route('/{record}/createCloseFile'),
            'view_close_file' => CloseFile\ViewCloseFile::route('/{record}/closeFile'),
            'edit_close_file_details' => CloseFile\EditCloseFileDetails::route('{record}/closeFile/editDetails'),
            'edit_close_file_general_details' => CloseFile\EditCloseFileGeneralDetails::route('{record}/closeFile/editGeneralDetails'),
        ];
    }
}
