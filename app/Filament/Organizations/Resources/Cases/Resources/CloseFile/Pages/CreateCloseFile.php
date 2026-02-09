<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\CloseFile\Pages;

use App\Actions\BackAction;
use App\Enums\AdmittanceReason;
use App\Enums\CloseMethod;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\CloseFile\CloseFileResource;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\Specialist;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;

class CreateCloseFile extends CreateRecord
{
    use HasWizard;

    protected static string $resource = CloseFileResource::class;

    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        $parent = $this->getParentRecord();
        if ($parent === null || ! $parent instanceof Beneficiary) {
            abort(404);
        }

        $this->authorizeAccess();
        $this->fillForm();
        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        $parent = $this->getParentRecord();
        abort_unless($parent instanceof Beneficiary && CaseResource::canEdit($parent), 403);
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $beneficiary = $this->getParentRecord();
        if (! $beneficiary instanceof Beneficiary) {
            return;
        }

        $caseManager = $beneficiary->specialistsTeam()
            ->with('role:id,case_manager')
            ->get()
            ->filter(fn (Specialist $s) => $s->role?->case_manager ?? false)
            ->first();

        $data = [
            'date' => now()->format('Y-m-d'),
            'admittance_date' => $beneficiary->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'exit_date' => now()->format('Y-m-d'),
            'specialist_id' => $caseManager?->id,
        ];

        $this->form->fill($data);
        $this->callHook('afterFill');
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.close_file.titles.create');
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $parent]) => $parent instanceof Beneficiary ? $parent->getBreadcrumb() : '',
            '' => __('beneficiary.section.close_file.titles.create'),
        ];
    }

    protected function getHeaderActions(): array
    {
        $parent = $this->getParentRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $parent])),
        ];
    }

    /**
     * @return array<int, Step>
     */
    public function getSteps(): array
    {
        return [
            Step::make(__('beneficiary.section.close_file.headings.file_details'))
                ->schema($this->getFileDetailsStepSchema()),
            Step::make(__('beneficiary.section.close_file.headings.general_details'))
                ->schema($this->getGeneralDetailsStepSchema()),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getFileDetailsStepSchema(): array
    {
        $beneficiary = $this->getParentRecord();

        return [
            Group::make()
                ->maxWidth('3xl')
                ->schema([
                    Grid::make()
                        ->schema([
                            DatePicker::make('date')
                                ->label(__('beneficiary.section.close_file.labels.date'))
                                ->required(),
                            DatePicker::make('admittance_date')
                                ->label(__('beneficiary.section.close_file.labels.admittance_date'))
                                ->required(),
                            DatePicker::make('exit_date')
                                ->label(__('beneficiary.section.close_file.labels.exit_date'))
                                ->required(),
                        ]),
                    Select::make('specialist_id')
                        ->label(__('beneficiary.section.close_file.labels.case_manager'))
                        ->options(
                            fn (): array => $beneficiary instanceof Beneficiary
                                ? $beneficiary->specialistsTeam()
                                    ->with(['user:id,first_name,last_name', 'role:id,name'])
                                    ->get()
                                    ->pluck('name_role', 'id')
                                    ->all()
                                : []
                        )
                        ->required()
                        ->searchable(),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getGeneralDetailsStepSchema(): array
    {
        return [
            Group::make()
                ->maxWidth('3xl')
                ->schema([
                    CheckboxList::make('admittance_reason')
                        ->label(__('beneficiary.section.close_file.labels.admittance_reason'))
                        ->options(AdmittanceReason::options()),

                    TextInput::make('admittance_details')
                        ->label(__('beneficiary.section.close_file.labels.admittance_details'))
                        ->placeholder(__('beneficiary.section.close_file.placeholders.admittance_details'))
                        ->maxLength(500)
                        ->columnSpanFull(),

                    Radio::make('close_method')
                        ->label(__('beneficiary.section.close_file.labels.close_method'))
                        ->options(CloseMethod::options())
                        ->live(),

                    TextInput::make('institution_name')
                        ->label(__('beneficiary.section.close_file.labels.institution_name'))
                        ->placeholder(__('beneficiary.section.close_file.placeholders.institution_name'))
                        ->visible(fn (Get $get): bool => CloseMethod::isValue($get('close_method'), CloseMethod::TRANSFER_TO))
                        ->required(fn (Get $get): bool => CloseMethod::isValue($get('close_method'), CloseMethod::TRANSFER_TO))
                        ->maxLength(255),

                    TextInput::make('beneficiary_request')
                        ->label(__('beneficiary.section.close_file.labels.beneficiary_request'))
                        ->placeholder(__('beneficiary.section.close_file.placeholders.add_details'))
                        ->visible(fn (Get $get): bool => CloseMethod::isValue($get('close_method'), CloseMethod::BENEFICIARY_REQUEST))
                        ->maxLength(500)
                        ->columnSpanFull(),

                    TextInput::make('other_details')
                        ->label(__('beneficiary.section.close_file.labels.other_details'))
                        ->placeholder(__('beneficiary.section.close_file.placeholders.add_details'))
                        ->visible(fn (Get $get): bool => CloseMethod::isValue($get('close_method'), CloseMethod::OTHER))
                        ->maxLength(500)
                        ->columnSpanFull(),

                    RichEditor::make('close_situation')
                        ->label(__('beneficiary.section.close_file.labels.close_situation'))
                        ->placeholder(__('beneficiary.section.close_file.placeholders.close_situation'))
                        ->maxLength(2500)
                        ->columnSpanFull(),

                    Checkbox::make('confirm_closure_criteria')
                        ->label(__('beneficiary.section.close_file.labels.confirm_closure_criteria'))
                        ->live(),

                    Checkbox::make('confirm_documentation')
                        ->label(__('beneficiary.section.close_file.labels.confirm_documentation'))
                        ->visible(fn (Get $get): bool => (bool) $get('confirm_closure_criteria')),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['admittance_reason']) && is_array($data['admittance_reason'])) {
            $data['admittance_reason'] = array_values($data['admittance_reason']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        $parent = $this->getParentRecord();

        return CloseFileResource::getUrl('view', [
            'beneficiary' => $parent,
            'record' => $this->getRecord(),
        ]);
    }
}
