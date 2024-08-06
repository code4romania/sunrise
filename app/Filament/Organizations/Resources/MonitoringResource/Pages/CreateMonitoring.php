<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Enums\ChildAggressorRelationship;
use App\Enums\MaintenanceSources;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Forms\Components\Select;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CreateMonitoring extends CreateRecord
{
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoring();
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.monitoring.titles.create');
    }

    protected function getRedirectUrl(): string
    {
        return static::getParentResource()::getUrl('monitorings.view', [
            'parent' => $this->parent,
            'record' => $this->record,
        ]);
    }

    protected function configureAction(Action $action): void
    {
        $action->hidden();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make()
                ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                    <x-filament::button
                        type="submit"
                        size="sm"
                    >
                        {{__('filament-panels::resources/pages/create-record.form.actions.create.label')}}
                    </x-filament::button>
                BLADE)))
                ->columnSpanFull()
                ->steps([
                    Wizard\Step::make(__('beneficiary.section.monitoring.headings.details'))
                        ->schema([
                            Grid::make()
                                ->maxWidth('3xl')
                                ->schema([
                                    DatePicker::make('date')
                                        ->label(__('beneficiary.section.monitoring.labels.date')),

                                    TextInput::make('number')
                                        ->label(__('beneficiary.section.monitoring.labels.number'))
                                        ->placeholder(__('beneficiary.section.monitoring.placeholders.number'))
                                        ->maxLength(100),

                                    DatePicker::make('start_date')
                                        ->label(__('beneficiary.section.monitoring.labels.start_date')),

                                    DatePicker::make('end_date')
                                        ->label(__('beneficiary.section.monitoring.labels.end_date')),

                                    Select::make('team')
                                        ->label(__('beneficiary.section.monitoring.labels.team'))
                                        ->placeholder(__('beneficiary.section.monitoring.placeholders.team'))
                                        ->options(
                                            fn () => $this->parent
                                                ->team
                                                ->each(fn ($item) => $item->full_name = $item->user->getFilamentName())
                                                ->pluck('full_name', 'id')
                                        )
                                        ->multiple(),
                                ]),
                        ]),

                    Wizard\Step::make(__('beneficiary.section.monitoring.headings.child_info'))
                        ->schema(function () {
                            $fields = [];
                            foreach ($this->parent->children as $key => $child) {
                                $fields[] = Group::make()
                                    ->maxWidth('3xl')
                                    ->schema([
                                        TextInput::make('children.' . $key . '.name')
                                            ->label(__('beneficiary.section.monitoring.labels.child_name'))
                                            ->columnSpanFull()
                                            ->default($child['name']),

                                        Grid::make()
                                            ->schema([
                                                TextInput::make('children.' . $key . '.status')
                                                    ->label(__('beneficiary.section.monitoring.labels.status'))
                                                    ->default($child['status']),

                                                TextInput::make('children.' . $key . '.age')
                                                    ->label(__('beneficiary.section.monitoring.labels.age'))
                                                    ->default($child['age']),

                                                TextInput::make('children.' . $key . '.birth_date')
                                                    ->label(__('beneficiary.section.monitoring.labels.birthdate'))
                                                    ->default($child['birthdate']),

                                                Select::make('children.' . $key . '.aggressor_relationship')
                                                    ->label(__('beneficiary.section.monitoring.labels.aggressor_relationship'))
                                                    ->placeholder(__('beneficiary.section.monitoring.placeholders.select_an_answer'))
                                                    ->options(ChildAggressorRelationship::options()),

                                                Select::make('children.' . $key . '.maintenance_sources')
                                                    ->label(__('beneficiary.section.monitoring.labels.maintenance_sources'))
                                                    ->placeholder(__('beneficiary.section.monitoring.placeholders.select_an_answer'))
                                                    ->options(MaintenanceSources::options()),

                                                TextInput::make('children.' . $key . '.location')
                                                    ->label(__('beneficiary.section.monitoring.labels.location'))
                                                    ->placeholder(__('beneficiary.section.monitoring.placeholders.location'))
                                                    ->maxLength(100),

                                                Textarea::make('children.' . $key . '.observations')
                                                    ->label(__('beneficiary.section.monitoring.labels.observations'))
                                                    ->placeholder(__('beneficiary.section.monitoring.placeholders.observations'))
                                                    ->maxLength(500)
                                                    ->columnSpanFull(),
                                            ]),
                                    ]);
                            }

                            return $fields;
                        }),

                    Wizard\Step::make(__('beneficiary.section.monitoring.headings.general'))
                        ->schema([
                            Group::make()
                                ->maxWidth('3xl')
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            DatePicker::make('admittance_date')
                                                ->label(__('beneficiary.section.monitoring.labels.admittance_date')),

                                            TextInput::make('admittance_disposition')
                                                ->label(__('beneficiary.section.monitoring.labels.admittance_disposition'))
                                                ->placeholder(__('beneficiary.section.monitoring.placeholders.admittance_disposition'))
                                                ->maxLength(100),
                                        ]),

                                    Textarea::make('services_in_center')
                                        ->label(__('beneficiary.section.monitoring.labels.services_in_center'))
                                        ->placeholder(__('beneficiary.section.monitoring.placeholders.services_in_center'))
                                        ->maxLength(2500),

                                    ...$this->getGeneralMonitoringDataFields(),

                                    Placeholder::make('progress_placeholder')
                                        ->label(__('beneficiary.section.monitoring.headings.progress')),

                                    Textarea::make('progress')
                                        ->label(__('beneficiary.section.monitoring.labels.progress'))
                                        ->placeholder(__('beneficiary.section.monitoring.placeholders.progress'))
                                        ->maxLength(2500),

                                    Placeholder::make('observation_placeholder')
                                        ->label(__('beneficiary.section.monitoring.headings.observation')),

                                    Textarea::make('observation')
                                        ->label(__('beneficiary.section.monitoring.labels.observation'))
                                        ->placeholder(__('beneficiary.section.monitoring.placeholders.observation'))
                                        ->maxLength(2500),

                                ]),
                        ]),

                ]),
        ]);
    }

    private function getGeneralMonitoringDataFields(): array
    {
        $formFields = [];
        $fields = [
            'protection_measures',
            'health_measures',
            'legal_measures',
            'psychological_measures',
            'aggressor_relationship',
            'others',
        ];

        foreach ($fields as $field) {
            $formFields[] = Placeholder::make($field)
                ->label(__(sprintf('beneficiary.section.monitoring.headings.%s', $field)));

            $formFields[] = Textarea::make($field . '.objection')
                ->label(__('beneficiary.section.monitoring.labels.objection'))
                ->placeholder(__('beneficiary.section.monitoring.placeholders.add_details'))
                ->maxLength(1500);

            $formFields[] = Textarea::make($field . '.activity')
                ->label(__('beneficiary.section.monitoring.labels.activity'))
                ->placeholder(__('beneficiary.section.monitoring.placeholders.add_details'))
                ->maxLength(1500);

            $formFields[] = Textarea::make($field . '.conclusion')
                ->label(__('beneficiary.section.monitoring.labels.conclusion'))
                ->placeholder(__('beneficiary.section.monitoring.placeholders.add_details'))
                ->maxLength(1500);
        }

        return $formFields;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the parent relationship key to the parent resource's ID.
        $data[$this->getParentRelationshipKey()] = $this->parent->id;

        return $data;
    }
}
