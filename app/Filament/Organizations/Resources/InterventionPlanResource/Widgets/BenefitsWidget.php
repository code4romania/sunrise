<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Widgets;

use App\Enums\AwardMethod;
use App\Forms\Components\Select;
use App\Models\BenefitType;
use App\Models\InterventionPlan;
use App\Tables\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class BenefitsWidget extends BaseWidget
{
    public ?InterventionPlan $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->benefits()
                    ->with('benefit.benefitTypes')
            )
            ->heading(__('intervention_plan.headings.benefit_services'))
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label(__('intervention_plan.actions.add_benefit'))
                    ->modalHeading(__('intervention_plan.headings.add_benefit_modal'))
                    ->schema($this->getBenefitSchema())
                    ->createAnother(false),
            ])
            ->columns([
                TextColumn::make('benefit.name')
                    ->label(__('intervention_plan.headings.benefit_name')),

                TextColumn::make('benefit_types')
                    ->label(__('intervention_plan.headings.benefit_type'))
                    ->formatStateUsing(
                        fn ($state) => collect(explode(',', $state))
                            ->map(fn ($item) => BenefitType::find((int) trim($item))->name)
                            ->join(', ')
                    ),

                TextColumn::make('award_methods')
                    ->label(__('intervention_plan.headings.award_methods')),

                TextColumn::make('description')
                    ->label(__('intervention_plan.headings.benefit_description'))
                    ->html(),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema($this->getBenefitSchema())
                    ->modalHeading(__('intervention_plan.headings.edit_benefit'))
                    ->extraModalFooterActions([
                        \Filament\Actions\DeleteAction::make()
                            ->label(__('intervention_plan.actions.delete_benefit'))
                            ->link()
                            ->icon(null)
                            ->cancelParentActions()
                            ->modalHeading(__('intervention_plan.headings.delete_benefit_modal'))
                            ->modalDescription(fn ($record) => $record->benefit->name)
                            ->modalSubmitActionLabel(__('intervention_plan.actions.delete_benefit')),
                    ])
                    ->modalExtraFooterActionsAlignment(Alignment::Left),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_benefit_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_benefit_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public function getBenefitSchema(): array
    {
        return [
            Select::make('benefit_id')
                ->label(__('intervention_plan.labels.benefit_category'))
                ->placeholder(__('intervention_plan.placeholders.benefit_category'))
                ->relationship('benefit', 'name')
                ->searchable()
                ->preload()
                ->live(),

            CheckboxList::make('benefit_types')
                ->label(__('intervention_plan.labels.benefit_types'))
                ->visible(
                    fn (Get $get) => (int) $get('benefit_id') &&
                        ! self::getBenefitTypes((int) $get('benefit_id'))->isEmpty()
                )
                ->options(function (Get $get) {
                    $benefitID = (int) $get('benefit_id');

                    return $benefitID ?
                        self::getBenefitTypes($benefitID)
                            ->pluck('name', 'id') :
                        [];
                }),

            //            i don't know ...
            //            TextInput::make('another_benefit_type')
            //                ->label(__('intervention_plan.labels.benefit_type'))
            //                ->visible(
            //                    fn (Get $get) => (int) $get('benefit_id') &&
            //                        self::getBenefitTypes((int) $get('benefit_id'))->isEmpty()
            //                ),

            CheckboxList::make('award_methods')
                ->label(__('intervention_plan.labels.award_methods'))
                ->options(AwardMethod::options()),

            RichEditor::make('description')
                ->label(__('intervention_plan.labels.benefit_description'))
                ->placeholder(__('intervention_plan.placeholders.benefit_description'))
                ->maxLength(1000),

            Hidden::make('intervention_plan_id')
                ->default(fn () => $this->record->id),
        ];
    }

    protected static function getBenefitTypes(int $benefitID): Collection
    {
        return Cache::driver('array')
            ->rememberForever(
                'benefit_types_' . $benefitID,
                fn () => BenefitType::query()
                    ->where('benefit_id', $benefitID)
                    ->active()
                    ->get()
            );
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.benefit_services');
    }
}
