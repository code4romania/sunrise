<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Enums\CaseStatus;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\AggressorFormSchema;
use App\Filament\Organizations\Resources\Cases\Schemas\BeneficiaryIdentityFormSchema;
use App\Filament\Organizations\Resources\Cases\Schemas\CaseTeamFormSchema;
use App\Filament\Organizations\Resources\Cases\Schemas\ChildrenIdentityFormSchema;
use App\Filament\Organizations\Resources\Cases\Schemas\FlowPresentationFormSchema;
use App\Filament\Organizations\Resources\Cases\Schemas\PersonalInfoFormSchema;
use App\Forms\Components\Repeater;
use App\Models\Aggressor;
use App\Models\Beneficiary;
use App\Models\FlowPresentation;
use App\Services\Case\CnpLookupService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class CreateCase extends CreateRecord
{
    use HasWizard;

    protected static string $resource = CaseResource::class;

    /** @var array<int|string, mixed> Captured case_team selection from form before unset in mutateFormDataBeforeCreate */
    protected array $pendingCaseTeamSelection = [];

    /** Beneficiary found in same center when validating CNP step (shows "CNP identificat" message + link). */
    public ?Beneficiary $cnpBeneficiaryInTenant = null;

    public function getTitle(): string|Htmlable
    {
        return __('case.create.title');
    }

    public function getWizardComponent(): \Filament\Schemas\Components\Component
    {
        return Wizard::make($this->getSteps())
            ->key('form.wizard')
            ->startOnStep($this->getStartStep())
            ->cancelAction($this->getCancelFormAction())
            ->submitAction($this->getSubmitFormAction())
            ->alpineSubmitHandler("\$wire.{$this->getSubmitFormLivewireMethodName()}()")
            ->skippable($this->hasSkippableSteps())
            ->contained(false);
    }

    /**
     * @return array<int, Step>
     */
    protected function getSteps(): array
    {
        return [
            Step::make('consent')
                ->label(__('case.create.wizard.consent'))
                ->schema([
                    Grid::make()
                        ->schema([
                            Checkbox::make('consent')
                                ->label(__('field.create_beneficiary_consent'))
                                ->required()
                                ->accepted()
                                ->columnSpanFull(),
                        ]),
                ]),

            Step::make('cnp')
                ->label(__('case.create.wizard.cnp'))
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('cnp')
                                ->label(__('field.beneficiary_cnp'))
                                ->placeholder(__('placeholder.cnp'))
                                ->maxLength(13)
                                ->required(fn (Get $get): bool => ! $get('without_cnp'))
                                ->mask('9999999999999')
                                ->rules([
                                    fn (Get $get): array => $get('without_cnp') ? [] : [new \App\Rules\ValidCNP],
                                ])
                                ->lazy()
                                ->afterStateUpdated(fn (): mixed => $this->resetCnpBeneficiaryInTenant())
                                ->disabled(fn (Get $get): bool => (bool) $get('without_cnp')),

                            Checkbox::make('without_cnp')
                                ->label(__('field.without_cnp'))
                                ->afterStateUpdated(fn (bool $state, Set $set): mixed => $set('cnp', null))
                                ->live(),

                            TextEntry::make('cnp_beneficiary_exists')
                                ->hiddenLabel()
                                ->visible(fn (): bool => $this->cnpBeneficiaryInTenant !== null)
                                ->state(fn (): HtmlString => $this->getCnpBeneficiaryExistsMessage())
                                ->columnSpanFull(),
                        ]),
                ])
                ->afterValidation(function ($livewire): void {
                    if ($livewire instanceof self) {
                        $livewire->handleCnpStepAfterValidation();
                    }
                }),

            Step::make('identity_beneficiary')
                ->label(__('case.create.wizard.identity_beneficiary'))
                ->schema(BeneficiaryIdentityFormSchema::getSchema(null, false)),

            Step::make('identity_children')
                ->label(__('case.create.wizard.identity_children'))
                ->schema(ChildrenIdentityFormSchema::getSchema()),

            Step::make('case_info')
                ->label(__('case.create.wizard.case_info'))
                ->schema([
                    ...PersonalInfoFormSchema::getSchema(),
                    Section::make(__('case.create.wizard.aggressor'))
                        ->schema([
                            Repeater::make('aggressors')
                                ->schema(AggressorFormSchema::getRepeaterItemSchema())
                                ->maxWidth('3xl')
                                ->hiddenLabel()
                                ->columns(2)
                                ->minItems(1)
                                ->addAction(
                                    fn (Action $action): Action => $action
                                        ->label(__('beneficiary.section.personal_information.actions.add_aggressor'))
                                        ->link()
                                        ->color('primary')
                                )
                                ->defaultItems(1),
                        ]),
                    Section::make(__('case.create.wizard.flow_presentation'))
                        ->schema(FlowPresentationFormSchema::getSchemaForCreateWizard()),
                ]),

            Step::make('case_team')
                ->label(__('case.create.wizard.case_team'))
                ->schema([
                    Section::make()
                        ->description(fn (): string => __('beneficiary.section.specialists.labels.select_roles', [
                            'user_name' => auth()->user()?->full_name ?? '',
                        ]))
                        ->schema(CaseTeamFormSchema::getSchemaForCreateWizard())
                        ->maxWidth('3xl'),
                ]),
        ];
    }

    public function handleCnpStepAfterValidation(): void
    {
        $data = $this->form->getRawState();
        $withoutCnp = (bool) ($data['without_cnp'] ?? false);
        $raw = isset($data['cnp']) && $data['cnp'] !== '' ? (string) $data['cnp'] : null;
        $cnp = $raw !== null ? preg_replace('/\D/', '', $raw) : null;

        if ($withoutCnp || $cnp === null || $cnp === '') {
            return;
        }

        $tenant = Filament::getTenant();
        $user = auth()->user();
        $result = app(CnpLookupService::class)->lookup($cnp, $tenant, $user);

        if ($result->shouldRedirectToView() && $result->beneficiaryInTenant !== null) {
            $this->cnpBeneficiaryInTenant = $result->beneficiaryInTenant;
            throw new Halt;
        }

        if ($result->showNoAccessMessage()) {
            $this->resetErrorBag();
            $this->form->addError('cnp', __('case.create.cnp_no_access'));
            throw new Halt;
        }

        if ($result->canCopyFromOtherCenter()) {
            $source = $result->beneficiaryToCopyFrom();
            if ($source !== null) {
                $this->fillFormFromBeneficiary($source);
            }
        }
    }

    public function resetCnpBeneficiaryInTenant(): void
    {
        $this->cnpBeneficiaryInTenant = null;
    }

    protected function getCnpBeneficiaryExistsMessage(): HtmlString
    {
        $beneficiary = $this->cnpBeneficiaryInTenant;
        if ($beneficiary === null) {
            return new HtmlString('');
        }

        $tenant = Filament::getTenant();
        $viewUrl = CaseResource::getUrl('view', [
            'tenant' => $tenant,
            'record' => $beneficiary,
        ]);
        $text = __('beneficiary.placeholder.beneficiary_exists');
        $linkText = __('beneficiary.page.view_case_details.title');

        $html = \sprintf(
            '<div class="flex items-center gap-3 rounded-xl p-4 bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400 ring-1 ring-primary-200 dark:ring-primary-500/20"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-500 text-white text-sm font-medium">i</span><span class="text-sm">%s</span> <a href="%s" class="text-sm font-medium text-primary-600 dark:text-primary-400 underline hover:no-underline">%s</a></div>',
            e($text),
            e($viewUrl),
            e($linkText)
        );

        return new HtmlString($html);
    }

    /**
     * Format birthdate for form display, returning null for empty or placeholder values.
     */
    protected function formatBirthdateForForm(mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('d.m.Y');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function fillFormFromBeneficiary(Beneficiary $source): void
    {
        $current = $this->form->getRawState();
        $fill = [
            'last_name' => $source->last_name,
            'first_name' => $source->first_name,
            'prior_name' => $source->prior_name,
            'civil_status' => $source->civil_status?->value,
            'gender' => $source->gender?->value,
            'birthdate' => $this->formatBirthdateForForm($source->birthdate),
            'birthplace' => $source->birthplace,
            'ethnicity' => $source->ethnicity?->value,
            'id_type' => $source->id_type?->value,
            'id_serial' => $source->id_serial,
            'id_number' => $source->id_number,
            'cnp' => $source->cnp,
        ];
        $this->form->fill(array_merge($current, $fill));
    }

    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();

        return CaseResource::getUrl('view', ['record' => $record]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['consent'], $data['without_cnp']);
        $data['status'] = $data['status'] ?? CaseStatus::ACTIVE;

        $fillable = (new Beneficiary)->getFillable();

        return array_intersect_key($data, array_flip($fillable));
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
        $beneficiary = $this->getRecord();

        $aggressors = $state['aggressors'] ?? [];
        foreach ($aggressors as $item) {
            $beneficiary->aggressors()->create($this->mapAggressorItemForSave($item));
        }

        $flowData = $state['flow_presentation'] ?? null;
        if (\is_array($flowData) && ! empty(array_filter($flowData))) {
            $otherIds = $flowData['other_called_institutions'] ?? [];
            unset($flowData['other_called_institutions']);
            $flow = $beneficiary->flowPresentation()->create(
                array_intersect_key($flowData, array_flip((new FlowPresentation)->getFillable()))
            );
            if (! empty($otherIds)) {
                $flow->otherCalledInstitution()->sync($otherIds);
            }
        }

        $selected = $this->pendingCaseTeamSelection;
        $roleIds = self::normalizeCaseTeamSelection($selected);
        if ($roleIds === null || $roleIds === []) {
            return;
        }
        $currentUserId = auth()->id();
        if (! $currentUserId) {
            return;
        }
        foreach ($roleIds as $roleId) {
            $beneficiary->specialistsTeam()->create([
                'role_id' => $roleId,
                'user_id' => $currentUserId,
                'specialistable_type' => $beneficiary->getMorphClass(),
            ]);
        }
    }

    /**
     * Normalize case_team form state to list of role IDs, or null if "no other role" is selected.
     *
     * @param  array<int|string, mixed>  $selected  CheckboxList state (list of keys or associative key => true)
     * @return array<int>|null Role IDs to create specialists for, or null when no specialists should be created
     */
    private static function normalizeCaseTeamSelection(array $selected): ?array
    {
        $hasNoOtherRole = false;
        $roleIds = [];

        foreach ($selected as $key => $value) {
            $optionKey = ($value === true && (\is_int($key) || is_numeric($key)))
                ? $key
                : (\is_int($key) ? $value : $key);
            if ($optionKey === CaseTeamFormSchema::NO_OTHER_ROLE_VALUE) {
                $hasNoOtherRole = true;

                continue;
            }
            if (is_numeric($optionKey)) {
                $roleIds[] = (int) $optionKey;
            }
        }

        if ($hasNoOtherRole) {
            return null;
        }

        return array_values(array_unique($roleIds));
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function mapAggressorItemForSave(array $item): array
    {
        $fillable = (new Aggressor)->getFillable();
        $mapped = array_intersect_key($item, array_flip($fillable));
        foreach (['violence_types', 'legal_history', 'drugs'] as $key) {
            if (isset($mapped[$key]) && \is_array($mapped[$key])) {
                $mapped[$key] = array_values($mapped[$key]);
            }
        }

        return $mapped;
    }
}
