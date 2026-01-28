<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use Filament\Schemas\Schema;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Enums\AddressType;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Notice;
use App\Models\Beneficiary;
use App\Models\Organization;
use App\Models\Scopes\BelongsToCurrentTenant;
use App\Rules\ValidCNP;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\HtmlString;

class CreateBeneficiary extends CreateRecord
{
    use HasWizard;
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public ?Beneficiary $parentBeneficiary = null;

    public function mount(): void
    {
        $this->setParentBeneficiary();
        parent::mount();
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.create.title');
    }

    public function getBreadcrumb(): string
    {
        return $this->getTitle();
    }

    protected function afterFill(): void
    {
        if (! $this->parentBeneficiary) {
            return;
        }

        $data = $this->parentBeneficiary->toArray();
        $data['initial_id'] = $this->parentBeneficiary->initial_id ?: $this->parentBeneficiary->id;
        $data['consent'] = true;
        $this->form->fill($data);
    }

    protected function setParentBeneficiary(): void
    {
        $parentBeneficiaryID = (int) request('parent');
        if (! $parentBeneficiaryID) {
            $refererUrl = request()->server('HTTP_REFERER');
            $parentBeneficiaryID = $refererUrl ? (int) str_replace([self::getResource()::getUrl('create'), '/'], '', $refererUrl) : null;
        }

        $this->parentBeneficiary = $parentBeneficiaryID ? Beneficiary::find($parentBeneficiaryID) : null;
    }

    public function form(Schema $schema): Schema
    {
        return parent::form($schema)
            ->components([
                Wizard::make($this->getSteps())
                    ->extraAlpineAttributes([
                        '@copy_beneficiary_data.window' => "step='beneficiary'",
                    ])
                    ->startOnStep($this->getStartStep())
                    ->cancelAction($this->getCancelFormAction())
                    ->submitAction(
                        $this->getSubmitFormAction()
                            ->label(__('general.action.finish'))
                    )
                    ->skippable($this->hasSkippableSteps()),
            ])
            ->columns(null);
    }

    public function getStartStep(): int
    {
        return $this->parentBeneficiary ? 3 : 1;
    }

    protected function getSteps(): array
    {
        return [
            Step::make('consent')
                ->label(__('beneficiary.wizard.consent.label'))
                ->schema([
                    Grid::make()
                        ->maxWidth('3xl')
                        ->schema([
                            Checkbox::make('consent')
                                ->label(__('field.create_beneficiary_consent'))
                                ->required()
                                ->accepted()
                                ->columnSpanFull(),

                            Placeholder::make('consent_placeholder')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->content(__('beneficiary.placeholder.consent')),
                        ]),
                ]),

            Step::make(__('field.cnp'))
                ->schema([
                    Group::make()
                        ->maxWidth('3xl')
                        ->schema([
                            TextInput::make('cnp')
                                ->label(__('field.beneficiary_cnp'))
                                ->placeholder(__('placeholder.cnp'))
                                ->maxLength(13)
                                ->required(fn (Get $get) => ! $get('without_cnp'))
                                ->mask('9999999999999')
                                ->rule(new ValidCNP)
                                ->lazy()
                                ->disabled(fn (Get $get) => $get('without_cnp')),

                            Checkbox::make('without_cnp')
                                ->label(__('field.without_cnp'))
                                ->afterStateUpdated(fn (bool $state, Set $set) => $set('cnp', null))
                                ->live(),

                            Notice::make('beneficiary_exist')
                                ->key('beneficiary_exist')
                                ->color('primary')
                                ->visible(
                                    function (Get $get, Set $set) {
                                        $cnp = $get('cnp');
                                        if (empty($cnp) || $cnp < 10) {
                                            return false;
                                        }
                                        $existedBeneficiary = Beneficiary::query()
                                            ->where('cnp', $get('cnp'))
                                            ->when(
                                                auth()->user()->canSearchBeneficiary(),
                                                fn (EloquentBuilder $query) => $query->whereIn(
                                                    'organization_id',
                                                    auth()->user()
                                                        ->whereHas(
                                                            'organizations',
                                                            fn (EloquentBuilder $query) => $query
                                                                ->where('institution_id', Filament::getTenant()->institution_id)
                                                        )
                                                )->pluck('id')
                                                    ->toArray()
                                            )
                                            ->withoutGlobalScope(BelongsToCurrentTenant::class)
                                            ->first();
                                        $set('organization_where_beneficiary_exist', $existedBeneficiary->organization ?? null);
                                    }
                                )
                                ->content(function (Get $get) {
                                    $organization = $get('organization_where_beneficiary_exist');
                                    debug($organization);

                                    $beneficiary = Beneficiary::query()
                                        ->where('cnp', $get('cnp'))
                                        ->first();

                                    if ($beneficiary) {
                                        return new HtmlString(__('beneficiary.placeholder.beneficiary_exists'));
                                    }

                                    $organizations = auth()->user()->organizations
                                        ->filter(fn (Organization $organization) => $organization->institution_id == Filament::getTenant()->institution_id);
                                    $beneficiary = Beneficiary::query()
                                        ->where('cnp', $get('cnp'))
                                        ->whereIn('organization_id', $organizations->pluck('id')->toArray())
                                        ->withoutGlobalScopes([BelongsToCurrentTenant::class])
                                        ->with('organization')
                                        ->first();

                                    if (! $beneficiary) {
                                        return '';
                                    }

                                    return new HtmlString(__('beneficiary.placeholder.beneficiary_exists_in_another_tenant', [
                                        'center' => $beneficiary->organization->name,
                                    ]));
                                })
                                ->registerActions([
                                    \Filament\Actions\Action::make('view_beneficiary')
                                        ->label(__('beneficiary.action.view_case_details'))
                                        ->link()
                                        ->url(
                                            fn (Get $get) => BeneficiaryResource::getUrl('view', [
                                                'record' => Beneficiary::query()
                                                    ->where('cnp', $get('cnp'))
                                                    ->first(),
                                            ])
                                        )
                                        ->visible(
                                            fn (Get $get) => Beneficiary::query()
                                                ->where('cnp', $get('cnp'))
                                                ->first()
                                        ),

                                    \Filament\Actions\Action::make('view_beneficiary_from_another_tenant')
                                        ->label(__('beneficiary.action.copy_beneficiary_data'))
                                        ->link()
                                        ->modalHeading(__('beneficiary.headings.modal_create_beneficiary_from_anther_tenant'))
                                        ->modalDescription(
                                            fn (Get $get) => __('beneficiary.labels.modal_create_beneficiary_from_anther_tenant', [
                                                'cnp' => $get('cnp'),
                                                'center' => Beneficiary::query()
                                                    ->where('cnp', $get('cnp'))
                                                    ->whereIn(
                                                        'organization_id',
                                                        auth()->user()
                                                            ->organizations
                                                            ->filter(
                                                                fn (Organization $organization) => $organization->institution_id == Filament::getTenant()->institution_id
                                                                    && $organization !== Filament::getTenant()
                                                            )
                                                            ->pluck('id')
                                                            ->toArray()
                                                    )
                                                    ->withoutGlobalScopes([BelongsToCurrentTenant::class])
                                                    ->with(['organization'])
                                                    ->first()
                                                    ->organization
                                                    ->name,
                                            ])
                                        )
                                        ->modalSubmitActionLabel(__('beneficiary.action.continue_copy_beneficiary_data'))
                                        ->modalWidth('md')
                                        ->action(function (Get $get, Set $set): void {
                                            $beneficiary = Beneficiary::query()
                                                ->where('cnp', $get('cnp'))
                                                ->whereIn(
                                                    'organization_id',
                                                    auth()->user()
                                                        ->organizations
                                                        ->filter(
                                                            fn (Organization $organization) => $organization->institution_id == Filament::getTenant()->institution_id
                                                                && $organization !== Filament::getTenant()
                                                        )
                                                        ->pluck('id')
                                                        ->toArray()
                                                )
                                                ->withoutGlobalScopes([BelongsToCurrentTenant::class])
                                                ->with([
                                                    'effective_residence',
                                                    'legal_residence',
                                                    'children',
                                                    'aggressors',
                                                    'details',
                                                ])
                                                ->first();

                                            $ignoredFields = [
                                                'id',
                                                'initial_id',
                                            ];

                                            $beneficiaryArray = $beneficiary->toArray();
                                            foreach ($beneficiaryArray['children'] as &$child) {
                                                $child['birthdate'] = $child['birthdate'] ? Carbon::parse($child['birthdate'])->format('d.m.Y') : null;
                                                $child['age'] = $child['birthdate'] ? Carbon::createFromFormat('d.m.Y', $child['birthdate'])->diffInYears(now()) : null;
                                            }

                                            foreach ($beneficiaryArray as $beneficiaryKey => $beneficiaryValue) {
                                                if (\in_array($beneficiaryKey, $ignoredFields)) {
                                                    continue;
                                                }

                                                if ($beneficiaryKey === 'birthdate' && $beneficiaryValue) {
                                                    $beneficiaryValue = Carbon::parse($beneficiaryValue)->format('d.m.Y');
                                                }

                                                $set($beneficiaryKey, $beneficiaryValue);
                                            }

                                            $this->dispatch('copy_beneficiary_data');
                                        })
                                        ->visible(
                                            fn (Get $get) => ! Beneficiary::query()
                                                ->where('cnp', $get('cnp'))
                                                ->first() &&
                                                Beneficiary::query()
                                                    ->where('cnp', $get('cnp'))
                                                    ->whereIn(
                                                        'organization_id',
                                                        auth()->user()
                                                            ->organizations
                                                            ->filter(
                                                                fn (Organization $organization) => $organization->institution_id == Filament::getTenant()->institution_id
                                                                    && $organization !== Filament::getTenant()
                                                            )
                                                            ->pluck('id')
                                                            ->toArray()
                                                    )
                                                    ->withoutGlobalScopes([BelongsToCurrentTenant::class])
                                                    ->first()
                                        ),
                                ]),
                        ]),
                ]),

            Step::make('beneficiary')
                ->label(__('beneficiary.wizard.beneficiary.label'))
                ->schema(EditBeneficiaryIdentity::getBeneficiaryIdentityFormSchema($this->parentBeneficiary, false))
                ->afterStateHydrated(function (Set $set) {
                    $legalResidence = AddressType::LEGAL_RESIDENCE->value;
                    $effectiveResidence = AddressType::EFFECTIVE_RESIDENCE->value;

                    if (filled($this->parentBeneficiary?->$legalResidence)) {
                        $set($legalResidence, $this->parentBeneficiary?->$legalResidence->toArray());
                    }
                    if (filled($this->parentBeneficiary?->$effectiveResidence)) {
                        $set($effectiveResidence, $this->parentBeneficiary?->$effectiveResidence->toArray());
                    }
                }),

            Step::make('children')
                ->label(__('beneficiary.wizard.children.label'))
                ->schema(EditChildrenIdentity::getChildrenIdentityFormSchema())
                ->afterStateHydrated(
                    function (Set $set) {
                        if (! $this->parentBeneficiary?->children->count()) {
                            return;
                        }

                        $children = $this->parentBeneficiary?->children->toArray() ?? [];
                        foreach ($children as &$child) {
                            $child['birthdate'] = $child['birthdate'] ? Carbon::parse($child['birthdate'])->format('d.m.Y') : null;
                            $child['age'] = $child['birthdate'] ? Carbon::createFromFormat('d.m.Y', $child['birthdate'])->diffInYears(now()) : null;
                        }
                        $set('children', $children);
                    }
                ),

            Step::make('personal_information')
                ->label(__('beneficiary.wizard.personal_information.label'))
                ->schema([
                    Section::make(__('beneficiary.section.personal_information.section.beneficiary'))
                        ->columns()
                        ->compact()
                        ->schema(EditBeneficiaryPersonalInformation::beneficiarySection()),

                    Section::make(__('beneficiary.section.personal_information.section.aggressor'))
                        ->columns()
                        ->compact()
                        ->schema(EditAggressor::aggressorSection()),

                    Section::make(__('beneficiary.section.personal_information.section.antecedents'))
                        ->columns()
                        ->compact()
                        ->schema(EditAntecedents::antecedentsSection()),

                    Section::make(__('beneficiary.section.personal_information.section.flow'))
                        ->columns()
                        ->compact()
                        ->schema(EditFlowPresentation::flowSection()),
                ]),

            Step::make('specialist')
                ->label(__('beneficiary.wizard.specialist.label'))
                ->schema([
                    Section::make()
                        ->label(__('beneficiary.wizard.specialist.label'))
                        ->maxWidth('3xl')
                        ->compact()
                        ->schema([
                            CheckboxList::make('roles')
                                ->label(__('beneficiary.section.specialists.labels.select_roles', ['user_name' => auth()->user()->full_name]))
                                ->options(function () {
                                    $roles = auth()->user()->rolesInOrganization->pluck('name', 'id');
                                    $roles[-1] = __('beneficiary.section.specialists.labels.without_role');

                                    return $roles;
                                })
                                ->disableOptionWhen(fn (Get $get, string $value) => $value === '-1' ?
                                    array_diff($get('roles'), ['-1']) :
                                    \in_array('-1', $get('roles')))
                                ->live(),
                        ]),
                ]),
        ];
    }

    public function afterCreate(): void
    {
        /** @var Beneficiary $record */
        $record = $this->getRecord();
        if ($record->same_as_legal_residence) {
            $record->loadMissing(['legal_residence', 'effective_residence']);

            Beneficiary::copyLegalResidenceToEffectiveResidence($record);
        }

        $roles = $this->data['roles'] ?? [];

        if (! $roles) {
            return;
        }

        foreach ($roles as $role) {
            if (! $role) {
                continue;
            }
            $record->specialistsTeam()->create([
                'role_id' => $role !== '-1' ? $role : null,
                'user_id' => auth()->id(),
                'specialistable_type' => 'beneficiary',
            ]);
        }
    }
}
