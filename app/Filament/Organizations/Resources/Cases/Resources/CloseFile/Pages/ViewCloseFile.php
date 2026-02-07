<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\CloseFile\Pages;

use App\Actions\BackAction;
use App\Enums\AdmittanceReason;
use App\Enums\CloseMethod;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\CloseFile\CloseFileResource;
use App\Infolists\Components\DateEntry;
use App\Models\Beneficiary;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewCloseFile extends ViewRecord
{
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
}
