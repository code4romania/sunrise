<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\RelationManagers;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\InitialEvaluationResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EvaluateDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'evaluateDetails';

    protected static ?string $relatedResource = InitialEvaluationResource::class;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('beneficiary.page.initial_evaluation.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_number')
            ->columns([
                TextColumn::make('registered_date')
                    ->label(__('beneficiary.section.initial_evaluation.labels.registered_date'))
                    ->date(),
                TextColumn::make('file_number')
                    ->label(__('beneficiary.section.initial_evaluation.labels.file_number')),
                TextColumn::make('specialist.full_name')
                    ->label(__('beneficiary.section.initial_evaluation.labels.specialist')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn (): string => CaseResource::getUrl('create_initial_evaluation', [
                        'record' => $this->getOwnerRecord(),
                    ]))
                    ->visible(fn (): bool => $this->getOwnerRecord()->evaluateDetails === null),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
