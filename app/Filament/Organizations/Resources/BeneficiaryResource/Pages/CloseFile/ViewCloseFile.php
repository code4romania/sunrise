<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use App\Enums\CloseMethod;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiaryIdentity;
use App\Infolists\Components\Actions\Edit;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;

class ViewCloseFile extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.close_file.titles.create');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())->getBreadcrumbsCloseFile();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label(__('beneficiary.section.close_file.actions.delete'))
                ->outlined()
                ->icon('heroicon-o-trash')
                ->modalHeading(__('beneficiary.section.close_file.headings.modal_delete'))
                ->modalDescription(__('beneficiary.section.close_file.labels.modal_delete_description'))
                ->modalSubmitActionLabel(__('beneficiary.section.close_file.actions.delete'))
                ->modalIcon()
                ->modalAlignment(Alignment::Left)
                // ->successNotificationTitle(__('beneficiary.section.close_file.notifications.delete_success'))
                ->successRedirectUrl(self::getResource()::getUrl('view', ['record' => $this->getRecord()]))
                ->using(fn (Beneficiary $record) => $record->closeFile->delete()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tabs\Tab::make(__('beneficiary.section.close_file.headings.file_details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.close_file.headings.file_details_simple'))
                                ->maxWidth('3xl')
                                ->columns()
                                ->relationship('closeFile')
                                ->headerActions([
                                    Edit::make('edit_details')
                                        ->url(self::getResource()::getUrl('edit_close_file_details', ['record' => $this->getRecord()])),
                                ])
                                ->schema([
                                    TextEntry::make('date')
                                        ->label(__('beneficiary.section.close_file.labels.date')),

                                    TextEntry::make('number')
                                        ->label(__('beneficiary.section.close_file.labels.number')),

                                    TextEntry::make('admittance_date')
                                        ->label(__('beneficiary.section.close_file.labels.admittance_date')),

                                    TextEntry::make('exit_date')
                                        ->label(__('beneficiary.section.close_file.labels.exit_date')),

                                    TextEntry::make('caseManager.full_name')
                                        ->label(__('beneficiary.section.close_file.labels.case_manager')),

                                ]),
                        ]),
                    Tabs\Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                        ->maxWidth('3xl')
                        ->schema(ViewBeneficiaryIdentity::identitySchemaForOtherPage($this->getRecord())),
                    Tabs\Tab::make(__('beneficiary.section.close_file.headings.general_details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.close_file.headings.general_details'))
                                ->maxWidth('3xl')
                                ->columns()
                                ->relationship('closeFile')
                                ->headerActions([
                                    Edit::make('edit_details')
                                        ->url(self::getResource()::getUrl('edit_close_file_general_details', ['record' => $this->getRecord()])),
                                ])
                                ->schema([
                                    TextEntry::make('admittance_reason')
                                        ->label(__('beneficiary.section.close_file.labels.admittance_reason')),

                                    TextEntry::make('admittance_details')
                                        ->label(__('beneficiary.section.close_file.labels.admittance_details'))
                                        ->placeholder(__('beneficiary.section.close_file.placeholders.admittance_details')),

                                    TextEntry::make('close_method')
                                        ->label(__('beneficiary.section.close_file.labels.close_method')),

                                    TextEntry::make('institution_name')
                                        ->label(__('beneficiary.section.close_file.labels.institution_name'))
                                        ->placeholder(__('beneficiary.section.close_file.placeholders.institution_name'))
                                        ->visible(fn (Beneficiary $record) => CloseMethod::isValue($record->closeFile->close_method, CloseMethod::TRANSFER_TO)),

                                    TextEntry::make('beneficiary_request')
                                        ->label(__('beneficiary.section.close_file.labels.beneficiary_request'))
                                        ->placeholder(__('beneficiary.section.close_file.placeholders.add_details'))
                                        ->visible(fn (Beneficiary $record) => CloseMethod::isValue($record->closeFile->close_method, CloseMethod::BENEFICIARY_REQUEST)),

                                    TextEntry::make('other_details')
                                        ->label(__('beneficiary.section.close_file.labels.other_details'))
                                        ->placeholder(__('beneficiary.section.close_file.placeholders.add_details'))
                                        ->visible(fn (Beneficiary $record) => CloseMethod::isValue($record->closeFile->close_method, CloseMethod::OTHER)),

                                    TextEntry::make('close_situation')
                                        ->label(__('beneficiary.section.close_file.labels.close_situation'))
                                        ->html()
                                        ->columnSpanFull(),

                                ]),
                        ]),
                ]),
        ]);
    }
}
