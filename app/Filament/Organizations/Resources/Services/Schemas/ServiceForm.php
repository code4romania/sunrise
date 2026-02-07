<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services\Schemas;

use App\Forms\Components\Select;
use App\Models\OrganizationService;
use App\Models\Service;
use App\Models\ServiceIntervention;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::getFormComponents());
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function getFormComponents(): array
    {
        return [
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    TextEntry::make('before_form')
                        ->hiddenLabel()
                        ->state(__('service.helper_texts.before_form')),

                    Select::make('service_id')
                        ->label(__('service.labels.name'))
                        ->visible(fn (string $operation) => $operation === 'create')
                        ->options(function (): array {
                            $tenant = Filament::getTenant();
                            if (! $tenant) {
                                return [];
                            }
                            $existingIds = OrganizationService::query()
                                ->where('organization_id', $tenant->id)
                                ->pluck('service_id');

                            return Service::query()
                                ->active()
                                ->whereNotIn('id', $existingIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->afterStateUpdated(function (mixed $state, Get $get, Set $set): void {
                            $serviceId = $state ?? $get('service_id');

                            if (! $serviceId) {
                                return;
                            }
                            $existing = $get('interventions');
                            $existing = collect($existing)
                                ->filter(fn (array $item) => $item['service_intervention_id'] !== null)
                                ->all();
                            if (\is_array($existing) && \count($existing) > 0) {
                                return;
                            }
                            $interventions = ServiceIntervention::query()
                                ->where('service_id', $serviceId)
                                ->active()
                                ->orderBy('sort')
                                ->get();
                            $items = [];

                            foreach ($interventions as $i) {
                                $items[(string) Str::uuid()] = [
                                    'active' => false,
                                    'name' => $i->name,
                                    'service_intervention_id' => $i->id,
                                    'status' => false,
                                ];
                            }
                            $set('interventions', $items);
                        })
                        ->live()
                        ->required(fn (string $operation): bool => $operation === 'create'),

                    TextEntry::make('service_name')
                        ->label(__('service.labels.name'))
                        ->state(function (Get $get, $record = null) {
                            $name = $record?->serviceWithoutStatusCondition?->name ?? null;
                            if ($name !== null) {
                                return $name;
                            }
                            $serviceId = $get('service_id');

                            return $serviceId ? (Service::query()->find($serviceId)?->name ?? '-') : '-';
                        })
                        ->visible(fn (string $operation) => $operation === 'edit'),

                    Group::make()
                        ->visible(fn (string $operation, Get $get) => $operation === 'edit' || (bool) $get('service_id'))
                        ->schema([
                            TextEntry::make('interventions_heading')
                                ->label(__('service.headings.interventions'))
                                ->state(__('service.helper_texts.interventions')),

                            Repeater::make('interventions')
                                ->helperText(__('service.helper_texts.under_interventions_table'))
                                ->addAction(fn ($action) => $action->hidden())
                                ->deletable(false)
                                ->reorderable(false)
                                ->schema([
                                    Checkbox::make('active')
                                        ->label(__('service.labels.select'))
                                        ->afterStateUpdated(fn (bool $state, Set $set) => $set('status', $state))
                                        ->live(),

                                    TextEntry::make('name')
                                        ->label(__('service.labels.name')),

                                    TextEntry::make('status_display')
                                        ->label(__('service.labels.status'))
                                        ->state(fn (Get $get): string => $get('status') ? __('nomenclature.labels.active') : __('nomenclature.labels.inactive'))
                                        ->visible(fn (string $operation) => $operation === 'edit')
                                        ->hintAction(
                                            Action::make('inactivate')
                                                ->label(__('service.actions.change_status.inactivate'))
                                                ->icon(Heroicon::XCircle)
                                                ->color('danger')
                                                ->requiresConfirmation()
                                                ->modalHeading(__('service.headings.inactivate_intervention_modal'))
                                                ->modalDescription(__('service.helper_texts.inactivate_intervention_modal'))
                                                ->modalSubmitActionLabel(__('service.actions.change_status.inactivate_intervention'))
                                                ->visible(fn (Get $get): bool => (bool) $get('status'))
                                                ->action(function (Set $set): void {
                                                    $set('status', false);
                                                    $set('active', false);
                                                })
                                        )
                                        ->hintAction(
                                            Action::make('activate')
                                                ->label(__('service.actions.change_status.activate'))
                                                ->icon(Heroicon::CheckCircle)
                                                ->color('success')
                                                ->visible(fn (Get $get): bool => ! (bool) $get('status'))
                                                ->action(function (Set $set): void {
                                                    $set('status', true);
                                                    $set('active', true);
                                                })
                                        ),

                                    Hidden::make('id'),
                                    Hidden::make('service_intervention_id'),
                                ])
                                ->columns(3)
                                ->itemLabel(fn (array $state): string => $state['name'] ?? ServiceIntervention::find($state['service_intervention_id'] ?? null)?->name ?? __('service.labels.intervention_item'))
                                ->collapsible(),
                        ]),
                ]),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $interventions
     * @return array<int, array<string, mixed>>|null
     */
    public static function processInterventionsBeforeSave(?array $interventions): ?array
    {
        if ($interventions === null) {
            return null;
        }

        $result = [];
        foreach ($interventions as $intervention) {
            if (empty($intervention['active'])) {
                continue;
            }
            $result[] = [
                'service_intervention_id' => $intervention['service_intervention_id'] ?? null,
                'status' => true,
            ];
        }

        return $result;
    }
}
