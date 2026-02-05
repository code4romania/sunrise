<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases;

use App\Filament\Organizations\Resources\Cases\Pages\CreateCase;
use App\Filament\Organizations\Resources\Cases\Pages\EditCasePersonalInformation;
use App\Filament\Organizations\Resources\Cases\Pages\InitialEvaluation\CreateCaseInitialEvaluation;
use App\Filament\Organizations\Resources\Cases\Pages\ListCases;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCase;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCaseIdentity;
use App\Filament\Organizations\Resources\Cases\Pages\ViewCasePersonalInformation;
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

    public static function getPages(): array
    {
        return [
            'index' => ListCases::route('/'),
            'create' => CreateCase::route('/create'),
            'view' => ViewCase::route('/{record}'),
            'identity' => ViewCaseIdentity::route('/{record}/identity'),
            'view_personal_information' => ViewCasePersonalInformation::route('/{record}/personal'),
            'edit_personal_information' => EditCasePersonalInformation::route('/{record}/personal/edit'),
            'create_initial_evaluation' => CreateCaseInitialEvaluation::route('/{record}/initial-evaluation/create'),
        ];
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
