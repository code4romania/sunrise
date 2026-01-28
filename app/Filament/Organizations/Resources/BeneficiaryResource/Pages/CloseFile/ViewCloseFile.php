<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\CloseFile;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs\Tab;
use App\Actions\BackAction;
use App\Enums\CloseMethod;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\DateEntry;
use App\Infolists\Components\Notice;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
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
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_close_file');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $this->getRecord()])),

            DeleteAction::make()
                ->label(__('beneficiary.section.close_file.actions.delete'))
                ->outlined()
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->authorize(static::getResource()::canDelete($this->getRecord()->closeFile))
                ->modalHeading(__('beneficiary.section.close_file.headings.modal_delete'))
                ->modalDescription(__('beneficiary.section.close_file.labels.modal_delete_description'))
                ->modalSubmitActionLabel(__('beneficiary.section.close_file.actions.delete'))
                ->modalIcon()
                ->successNotificationTitle(__('beneficiary.section.close_file.notifications.delete_success'))
                ->successRedirectUrl(self::getResource()::getUrl('view', ['record' => $this->getRecord()]))
                ->using(fn (Beneficiary $record) => $record->closeFile->delete()),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make(__('beneficiary.section.close_file.headings.file_details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.close_file.headings.file_details_simple'))
                                ->columns()
                                ->relationship('closeFile')
                                ->headerActions([
                                    EditAction::make('edit_details')
                                        ->url(self::getResource()::getUrl('edit_close_file_details', ['record' => $this->getRecord()])),
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

                    Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make()
                                ->columns()
                                ->schema([
                                    Notice::make('identity')
                                        ->icon('heroicon-s-information-circle')
                                        ->state(__('beneficiary.section.identity.heading_description'))
                                        ->color('primary')
                                        ->action(
                                            \Filament\Actions\Action::make('view')
                                                ->label(__('beneficiary.section.identity.title'))
                                                ->url(self::$resource::getUrl('view_identity', ['record' => $this->getRecord()]))
                                                ->link(),
                                        ),

                                    TextEntry::make('last_name')
                                        ->label(__('field.last_name')),

                                    TextEntry::make('first_name')
                                        ->label(__('field.first_name')),

                                    TextEntry::make('cnp')
                                        ->label(__('field.cnp')),
                                ]),
                        ]),

                    Tab::make(__('beneficiary.section.close_file.headings.general_details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.close_file.headings.general_details'))
                                ->columns()
                                ->relationship('closeFile')
                                ->headerActions([
                                    EditAction::make('edit_details')
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
