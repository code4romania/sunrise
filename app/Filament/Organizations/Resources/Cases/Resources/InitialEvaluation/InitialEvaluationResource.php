<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\Pages\CreateInitialEvaluation;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\Pages\EditInitialEvaluation;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\Pages\ViewInitialEvaluation;
use App\Models\EvaluateDetails;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InitialEvaluationResource extends Resource
{
    protected static ?string $model = EvaluateDetails::class;

    protected static ?string $slug = 'initial-evaluation';

    protected static bool $shouldRegisterNavigation = false;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return CaseResource::asParent()
            ->relationship('evaluateDetails')
            ->inverseRelationship('beneficiary');
    }

    public static function getModelLabel(): string
    {
        return __('beneficiary.page.initial_evaluation.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('beneficiary.page.initial_evaluation.title');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record instanceof EvaluateDetails
            ? __('beneficiary.page.initial_evaluation.title').' #'.$record->getKey()
            : null;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->with(['beneficiary', 'specialist']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateInitialEvaluation::route('/create'),
            'view' => ViewInitialEvaluation::route('/{record}'),
            'edit' => EditInitialEvaluation::route('/{record}/edit'),
        ];
    }
}
