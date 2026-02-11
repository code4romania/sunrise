<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Enums\AwardMethod;
use App\Models\Beneficiary;
use App\Models\Benefit;
use App\Models\BenefitService;
use App\Models\BenefitType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Cache;

class InterventionPlanBenefitsWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $plan = $this->record?->interventionPlan;

        return $table
            ->query(
                $plan
                    ? $plan->benefits()->with('benefit.benefitTypes')->getQuery()
                    : BenefitService::query()->whereRaw('1 = 0')
            )
            ->heading(__('intervention_plan.headings.benefit_services'))
            ->columns([
                TextColumn::make('benefit.name')
                    ->label(__('intervention_plan.headings.benefit_name')),
                TextColumn::make('benefit_types')
                    ->label(__('intervention_plan.headings.benefit_type'))
                    ->formatStateUsing(fn ($state, BenefitService $record): string => self::formatBenefitTypes($record)),
                TextColumn::make('award_methods')
                    ->label(__('intervention_plan.headings.award_methods'))
                    ->formatStateUsing(fn ($state): string => self::formatAwardMethods($state)),
                TextColumn::make('description')
                    ->label(__('intervention_plan.headings.benefit_description'))
                    ->html()
                    ->limit(50),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label(__('intervention_plan.actions.add_benefit'))
                    ->modalHeading(__('intervention_plan.headings.add_benefit_modal'))
                    ->model(BenefitService::class)
                    ->mutateDataUsing(function (array $data): array {
                        $data['intervention_plan_id'] = $this->record?->interventionPlan?->id;

                        return $data;
                    })
                    ->schema([
                        Select::make('benefit_id')
                            ->label(__('intervention_plan.labels.benefit_category'))
                            ->placeholder(__('intervention_plan.placeholders.benefit_category'))
                            ->options(Benefit::query()->active()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        CheckboxList::make('benefit_types')
                            ->label(__('intervention_plan.labels.benefit_types'))
                            ->options(fn (Get $get): array => self::getBenefitTypesOptions((int) $get('benefit_id')))
                            ->visible(fn (Get $get): bool => (int) $get('benefit_id') > 0),
                        CheckboxList::make('award_methods')
                            ->label(__('intervention_plan.labels.award_methods'))
                            ->options(AwardMethod::options()),
                        RichEditor::make('description')
                            ->label(__('intervention_plan.labels.benefit_description'))
                            ->placeholder(__('intervention_plan.placeholders.benefit_description'))
                            ->maxLength(1000),
                    ])
                    ->createAnother(false),
            ])
            ->recordActionsColumnLabel(__('intervention_plan.labels.actions'))
            ->recordActions([
                EditAction::make()
                    ->label(__('intervention_plan.actions.edit_benefit'))
                    ->modalHeading(__('intervention_plan.headings.edit_benefit'))
                    ->fillForm(fn (BenefitService $record): array => [
                        'benefit_id' => $record->benefit_id,
                        'benefit_types' => $record->benefit_types ?? [],
                        'award_methods' => $record->award_methods?->map(fn ($e) => $e->value)->all() ?? [],
                        'description' => $record->description,
                    ])
                    ->schema([
                        Select::make('benefit_id')
                            ->label(__('intervention_plan.labels.benefit_category'))
                            ->placeholder(__('intervention_plan.placeholders.benefit_category'))
                            ->options(Benefit::query()->active()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        CheckboxList::make('benefit_types')
                            ->label(__('intervention_plan.labels.benefit_types'))
                            ->options(fn (Get $get): array => self::getBenefitTypesOptions((int) $get('benefit_id')))
                            ->visible(fn (Get $get): bool => (int) $get('benefit_id') > 0),
                        CheckboxList::make('award_methods')
                            ->label(__('intervention_plan.labels.award_methods'))
                            ->options(AwardMethod::options()),
                        RichEditor::make('description')
                            ->label(__('intervention_plan.labels.benefit_description'))
                            ->placeholder(__('intervention_plan.placeholders.benefit_description'))
                            ->maxLength(1000),
                    ])
                    ->action(function (array $data, BenefitService $record): void {
                        $record->update($data);
                    })
                    ->extraModalFooterActions([
                        DeleteAction::make()
                            ->label(__('intervention_plan.actions.delete_benefit'))
                            ->modalHeading(__('intervention_plan.headings.delete_benefit_modal'))
                            ->modalDescription(fn (BenefitService $record): string => $record->benefit?->name ?? '')
                            ->modalSubmitActionLabel(__('intervention_plan.actions.delete_benefit'))
                            ->cancelParentActions(),
                    ]),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_benefit_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_benefit_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    /**
     * @return array<int, string>
     */
    private static function getBenefitTypesOptions(int $benefitId): array
    {
        if ($benefitId <= 0) {
            return [];
        }

        return Cache::driver('array')
            ->rememberForever(
                'benefit_types_'.$benefitId,
                fn () => BenefitType::query()
                    ->where('benefit_id', $benefitId)
                    ->active()
                    ->get()
                    ->pluck('name', 'id')
                    ->all()
            );
    }

    private static function formatBenefitTypes(BenefitService $record): string
    {
        $types = $record->benefit?->benefitTypes;
        if (! $types || $types->isEmpty()) {
            return '—';
        }
        $ids = is_array($record->benefit_types) ? $record->benefit_types : [];
        if ($ids === []) {
            return '—';
        }

        return $types->whereIn('id', $ids)->pluck('name')->join(', ');
    }

    private static function formatAwardMethods(mixed $state): string
    {
        if ($state === null) {
            return '—';
        }
        if ($state instanceof BaseCollection) {
            return $state->map(fn ($e) => is_object($e) && method_exists($e, 'getLabel') ? $e->getLabel() : (string) $e)->join(', ');
        }
        if (is_iterable($state)) {
            return collect($state)->map(fn ($e) => is_object($e) && method_exists($e, 'getLabel') ? $e->getLabel() : (string) $e)->join(', ');
        }

        return '—';
    }

    public static function canView(): bool
    {
        return true;
    }
}
