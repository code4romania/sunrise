<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Enums\CaseStatus;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Schemas\BeneficiaryIdentityFormSchema;
use App\Filament\Organizations\Resources\Cases\Schemas\ChildrenIdentityFormSchema;
use App\Filament\Organizations\Resources\Cases\Schemas\PersonalInfoFormSchema;
use App\Models\Beneficiary;
use App\Services\Case\CnpLookupService;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;

class CreateCase extends CreateRecord
{
    use HasWizard;

    protected static string $resource = CaseResource::class;

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
                        ->maxWidth('3xl')
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
                        ->maxWidth('3xl')
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
                                ->disabled(fn (Get $get): bool => (bool) $get('without_cnp')),

                            Checkbox::make('without_cnp')
                                ->label(__('field.without_cnp'))
                                ->afterStateUpdated(fn (bool $state, Set $set): mixed => $set('cnp', null))
                                ->live(),
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
                ->schema(PersonalInfoFormSchema::getSchema()),
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
            $this->redirect(CaseResource::getUrl('view', [
                'tenant' => $tenant,
                'record' => $result->beneficiaryInTenant,
            ]));

            return;
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
}
