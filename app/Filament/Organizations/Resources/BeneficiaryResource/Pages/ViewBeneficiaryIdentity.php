<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\EnumEntry;
use App\Infolists\Components\Location;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiaryIdentity extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.edit_identity.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns()
            ->schema([
                Tabs::make()
                    ->tabs([
                        Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                            ->schema(static::getBeneficiaryIdentityFormSchema()),

                        Tab::make(__('beneficiary.section.identity.tab.children'))
                            ->schema(static::getChildrenIdentityFormSchema()),
                    ]),
            ]);
    }

    public static function getBeneficiaryIdentityFormSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.identity.tab.beneficiary'))
                ->columnSpan(1)
                ->columns()
                ->headerActions([
                    Action::make('edit')
                        ->label(__('general.action.edit'))
                        ->url(fn ($record) => BeneficiaryResource::getUrl('edit_identity', ['record' => $record]))
                        ->link(),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    Grid::make()
                        ->maxWidth('3xl')
                        ->schema([
                            TextEntry::make('last_name')
                                ->label(__('field.last_name'))
                                ->placeholder(__('placeholder.last_name')),

                            TextEntry::make('first_name')
                                ->label(__('field.first_name'))
                                ->placeholder(__('placeholder.first_name')),

                            TextEntry::make('prior_name')
                                ->label(__('field.prior_name'))
                                ->placeholder(__('placeholder.prior_name')),

                            TextEntry::make('civil_status')
                                ->label(__('field.civil_status'))
                                ->placeholder(__('placeholder.civil_status')),

                            TextEntry::make('cnp')
                                ->label(__('field.cnp'))
                                ->placeholder(__('placeholder.cnp')),

                            EnumEntry::make('gender')
                                ->label(__('field.gender'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('birthdate')
                                ->label(__('field.birthdate')),

                            TextEntry::make('birthplace')
                                ->label(__('field.birthplace'))
                                ->placeholder(__('placeholder.birthplace')),

                            TextEntry::make('citizenship_id')
                                ->label(__('field.citizenship'))
                                ->placeholder(__('placeholder.citizenship'))
                                ->formatStateUsing(fn ($record) => $record->citizenship?->name),

                            TextEntry::make('ethnicity_id')
                                ->label(__('field.ethnicity'))
                                ->placeholder(__('placeholder.ethnicity'))
                                ->formatStateUsing(fn ($record) => $record->ethnicity?->name),

                            TextEntry::make('id_serial')
                                ->label(__('field.id_serial'))
                                ->placeholder(__('placeholder.id_serial')),

                            TextEntry::make('id_number')
                                ->label(__('field.id_number'))
                                ->placeholder(__('placeholder.id_number')),

                            Location::make('legal_residence')
                                ->city()
                                ->address()
                                ->environment(),

                            Location::make('effective_residence')
                                ->city()
                                ->address()
                                ->environment(),

                            TextEntry::make('primary_phone')
                                ->label(__('field.primary_phone'))
                                ->placeholder(__('placeholder.phone')),

                            TextEntry::make('backup_phone')
                                ->label(__('field.backup_phone'))
                                ->placeholder(__('placeholder.phone')),

                            TextEntry::make('contact_notes')
                                ->label(__('field.contact_notes'))
                                ->placeholder(__('placeholder.contact_notes'))
                                ->columnSpanFull(),

                            EnumEntry::make('studies')
                                ->label(__('field.studies'))
                                ->placeholder(__('placeholder.contact_notes')),

                            EnumEntry::make('occupation')
                                ->label(__('field.occupation'))
                                ->placeholder(__('placeholder.contact_notes')),

                            TextEntry::make('workplace')
                                ->label(__('field.workplace'))
                                ->placeholder(__('placeholder.contact_notes')),
                        ]),
                ]),
        ];
    }

    public static function getChildrenIdentityFormSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.identity.tab.children'))
                ->columnSpan(1)
                ->columns()
                ->headerActions([
                    Action::make('edit')
                        ->label(__('general.action.edit'))
                        ->url(fn ($record) => BeneficiaryResource::getUrl('edit_identity', ['record' => $record]))
                        ->link(),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    Grid::make()
                        ->hidden(fn ($record) => $record->doesnt_have_children)
                        ->maxWidth('3xl')
                        ->schema([
                            TextEntry::make('children_total_count')
                                ->label(__('field.children_total_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),

                            TextEntry::make('children_care_count')
                                ->label(__('field.children_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),

                            TextEntry::make('children_under_10_care_count')
                                ->label(__('field.children_under_10_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),

                            TextEntry::make('children_10_18_care_count')
                                ->label(__('field.children_10_18_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),

                            TextEntry::make('children_18_care_count')
                                ->label(__('field.children_18_care_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),

                            TextEntry::make('children_accompanying_count')
                                ->label(__('field.children_accompanying_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),
                        ]),

                    RepeatableEntry::make('children')
                        ->label(__('enum.notifier.child'))
                        ->columnSpanFull()
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('field.child_name')),

                            TextEntry::make('age')
                                ->label(__('field.age')),

                            TextEntry::make('address')
                                ->label(__('field.current_address')),

                            TextEntry::make('status')
                                ->label(__('field.child_status')),
                        ]),

                    TextEntry::make('children_notes')
                        ->label(__('field.children_notes'))
                        ->placeholder(__('placeholder.other_relevant_details'))
                        ->columnSpanFull(),
                ]),
        ];
    }
}
