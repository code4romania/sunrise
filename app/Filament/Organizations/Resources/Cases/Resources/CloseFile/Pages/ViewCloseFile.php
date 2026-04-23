<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\CloseFile\Pages;

use App\Actions\BackAction;
use App\Enums\AdmittanceReason;
use App\Enums\CloseMethod;
use App\Filament\Organizations\Concerns\InteractsWithBeneficiaryDetailsPanel;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\CloseFile\CloseFileResource;
use App\Infolists\Components\DateEntry;
use App\Models\Beneficiary;
use App\Models\Specialist;
use App\Services\CaseExports\CaseExportManager;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewCloseFile extends ViewRecord
{
    use InteractsWithBeneficiaryDetailsPanel;

    protected static string $resource = CloseFileResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.close_file.titles.create');
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $parent]) => $parent instanceof Beneficiary ? $parent->getBreadcrumb() : '',
            '' => __('beneficiary.section.close_file.headings.file_details_simple').' #'.$record->getKey(),
        ];
    }

    protected function getHeaderActions(): array
    {
        $parent = $this->getParentRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $parent])),
            DeleteAction::make()
                ->label(__('beneficiary.section.close_file.actions.delete'))
                ->modalHeading(__('beneficiary.section.close_file.headings.modal_delete'))
                ->modalDescription(__('beneficiary.section.close_file.labels.modal_delete_description'))
                ->successRedirectUrl(CaseResource::getUrl('view', ['record' => $parent]))
                ->outlined(),
            Action::make('download_sheet')
                ->label(__('case.view.identity_page.download_sheet'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->outlined()
                ->action(fn (): StreamedResponse => app(CaseExportManager::class)->downloadCloseFilePdf($this->getRecord())),
        ];
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->inlineLabel(true)
            ->record($this->getRecord());
    }

    public function infolist(Schema $schema): Schema
    {
        $record = $this->getRecord();

        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([
                    Tab::make(__('beneficiary.section.close_file.headings.file_details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.close_file.headings.file_details_simple'))
                                ->headerActions([
                                    $this->getEditFileDetailsAction(),
                                ])
                                ->schema([
                                    DateEntry::make('date')
                                        ->label(__('beneficiary.section.close_file.labels.date')),
                                    DateEntry::make('admittance_date')
                                        ->label(__('beneficiary.section.close_file.labels.admittance_date')),
                                    DateEntry::make('exit_date')
                                        ->label(__('beneficiary.section.close_file.labels.exit_date')),
                                    TextEntry::make('caseManager.name_role')
                                        ->label(__('beneficiary.section.close_file.labels.case_manager')),
                                ]),
                        ]),

                    Tab::make(__('beneficiary.section.close_file.headings.general_details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.close_file.headings.general_details'))
                                ->headerActions([
                                    $this->getEditGeneralDetailsAction(),
                                ])
                                ->schema([
                                    TextEntry::make('admittance_reason')
                                        ->label(__('beneficiary.section.close_file.labels.admittance_reason'))
                                        ->formatStateUsing(fn ($state) => $this->formatAdmittanceReason($state)),
                                    TextEntry::make('admittance_details')
                                        ->label(__('beneficiary.section.close_file.labels.admittance_details')),
                                    TextEntry::make('close_method')
                                        ->label(__('beneficiary.section.close_file.labels.close_method'))
                                        ->formatStateUsing(fn ($state) => $state instanceof CloseMethod ? $state->getLabel() : ($state ?? '—')),
                                    TextEntry::make('institution_name')
                                        ->label(__('beneficiary.section.close_file.labels.institution_name'))
                                        ->visible(fn () => CloseMethod::isValue($record->close_method, CloseMethod::TRANSFER_TO)),
                                    TextEntry::make('beneficiary_request')
                                        ->label(__('beneficiary.section.close_file.labels.beneficiary_request'))
                                        ->visible(fn () => CloseMethod::isValue($record->close_method, CloseMethod::BENEFICIARY_REQUEST)),
                                    TextEntry::make('other_details')
                                        ->label(__('beneficiary.section.close_file.labels.other_details'))
                                        ->visible(fn () => CloseMethod::isValue($record->close_method, CloseMethod::OTHER)),
                                    TextEntry::make('close_situation')
                                        ->label(__('beneficiary.section.close_file.labels.close_situation'))
                                        ->html()
                                        ->columnSpanFull(),
                                    TextEntry::make('confirm_closure_criteria')
                                        ->label(__('beneficiary.section.close_file.labels.confirm_closure_criteria'))
                                        ->formatStateUsing(fn (?bool $state): string => $state ? __('general.yes') : __('general.no')),
                                    TextEntry::make('confirm_documentation')
                                        ->label(__('beneficiary.section.close_file.labels.confirm_documentation'))
                                        ->formatStateUsing(fn (?bool $state): string => $state ? __('general.yes') : __('general.no')),
                                ]),
                        ]),
                ]),
        ]);
    }

    private function formatAdmittanceReason(mixed $state): string
    {
        if ($state === null || $state === '') {
            return '—';
        }
        if (is_array($state) || $state instanceof \Illuminate\Support\Collection) {
            $labels = collect($state)->map(fn (mixed $v): string => $this->admittanceReasonToLabel($v))->all();

            return implode(', ', $labels);
        }

        return $this->admittanceReasonToLabel($state);
    }

    private function admittanceReasonToLabel(mixed $value): string
    {
        if ($value instanceof AdmittanceReason) {
            return $value->getLabel();
        }
        if (is_string($value)) {
            return AdmittanceReason::tryFrom($value)?->getLabel() ?? $value;
        }

        return (string) $value;
    }

    protected function hasInfolist(): bool
    {
        return true;
    }

    protected function getEditFileDetailsAction(): Action
    {
        return Action::make('edit_file_details')
            ->label(__('general.action.edit'))
            ->icon(Heroicon::OutlinedPencilSquare)
            ->link()
            ->visible(fn (): bool => CaseResource::canEdit($this->getParentRecord()))
            ->slideOver()
            ->modalHeading(__('beneficiary.section.close_file.headings.file_details'))
            ->schema([
                \App\Forms\Components\DatePicker::make('date')
                    ->label(__('beneficiary.section.close_file.labels.date'))
                    ->required(),
                \App\Forms\Components\DatePicker::make('admittance_date')
                    ->label(__('beneficiary.section.close_file.labels.admittance_date'))
                    ->required(),
                \App\Forms\Components\DatePicker::make('exit_date')
                    ->label(__('beneficiary.section.close_file.labels.exit_date'))
                    ->required(),
                Select::make('specialist_id')
                    ->label(__('beneficiary.section.close_file.labels.case_manager'))
                    ->options(fn (): array => $this->getSpecialistOptions())
                    ->required()
                    ->searchable(),
            ])
            ->fillForm(fn (): array => $this->getRecord()->only([
                'date',
                'admittance_date',
                'exit_date',
                'specialist_id',
            ]))
            ->action(function (array $data): void {
                $this->getRecord()->update($data);

                Notification::make()
                    ->success()
                    ->title(__('filament-actions::edit.single.notifications.saved.title'))
                    ->send();
            });
    }

    protected function getEditGeneralDetailsAction(): Action
    {
        return Action::make('edit_general_details')
            ->label(__('general.action.edit'))
            ->icon(Heroicon::OutlinedPencilSquare)
            ->link()
            ->visible(fn (): bool => CaseResource::canEdit($this->getParentRecord()))
            ->slideOver()
            ->modalHeading(__('beneficiary.section.close_file.headings.general_details'))
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
            ])
            ->fillForm(fn (): array => [
                ...$this->getRecord()->only([
                    'admittance_reason',
                    'admittance_details',
                    'close_method',
                    'institution_name',
                    'beneficiary_request',
                    'other_details',
                    'close_situation',
                    'confirm_closure_criteria',
                    'confirm_documentation',
                ]),
                'admittance_reason' => is_array($this->getRecord()->admittance_reason)
                    ? array_values($this->getRecord()->admittance_reason)
                    : [],
            ])
            ->action(function (array $data): void {
                if (isset($data['admittance_reason']) && is_array($data['admittance_reason'])) {
                    $data['admittance_reason'] = array_values($data['admittance_reason']);
                }

                $this->getRecord()->update($data);

                Notification::make()
                    ->success()
                    ->title(__('filament-actions::edit.single.notifications.saved.title'))
                    ->send();
            });
    }

    /**
     * @return array<int|string, string>
     */
    protected function getSpecialistOptions(): array
    {
        $parent = $this->getParentRecord();
        if (! $parent instanceof Beneficiary) {
            return [];
        }

        return $parent->specialistsTeam()
            ->with(['user:id,first_name,last_name', 'roleForDisplay:id,name'])
            ->get()
            ->mapWithKeys(fn (Specialist $specialist): array => [$specialist->id => $specialist->name_role])
            ->all();
    }
}
