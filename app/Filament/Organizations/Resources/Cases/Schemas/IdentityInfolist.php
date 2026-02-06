<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Schemas;

use App\Enums\AddressType;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\DateEntry;
use App\Infolists\Components\EnumEntry;
use App\Infolists\Components\Location;
use App\Infolists\Components\Notice;
use App\Models\Beneficiary;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class IdentityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                            ->maxWidth('3xl')
                            ->schema(static::beneficiarySectionSchema()),

                        Tab::make(__('beneficiary.section.identity.tab.children'))
                            ->maxWidth('3xl')
                            ->schema(static::childrenSectionSchema()),
                    ]),
            ]);
    }

    /**
     * @return array<int, Section>
     */
    public static function beneficiarySectionSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.identity.tab.beneficiary'))
                ->headerActions([
                    EditAction::make('edit')
                        ->url(fn (Beneficiary $record): string => CaseResource::getUrl('edit_identity', ['record' => $record])),
                ])
                ->schema(static::identityFieldsSchema()),
        ];
    }

    /**
     * @return array<int, Section|Grid|TextEntry>
     */
    public static function childrenSectionSchema(): array
    {
        return [
            Notice::make('empty_children')
                ->state(__('beneficiary.section.identity.empty_children'))
                ->visible(fn (Beneficiary $record): bool => (bool) $record->doesnt_have_children),
            Section::make(__('beneficiary.section.identity.tab.children'))
                ->hidden(fn (Beneficiary $record): bool => (bool) $record->doesnt_have_children)
                ->headerActions([
                    EditAction::make('edit')
                        ->url(fn (Beneficiary $record): string => CaseResource::getUrl('edit_children', ['record' => $record])),
                ])
                ->schema(static::childrenFieldsSchema()),
        ];
    }

    /**
     * Identity fields schema for embedding in other pages (e.g. detailed evaluation view).
     *
     * @return array<int, mixed>
     */
    public static function getIdentityFieldsSchemaForEmbedding(): array
    {
        return static::identityFieldsSchema();
    }

    /**
     * @return array<int, mixed>
     */
    protected static function identityFieldsSchema(): array
    {
        return [
            Grid::make()
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
                    DateEntry::make('birthdate')
                        ->label(__('field.birthdate')),
                    TextEntry::make('age')
                        ->label(__('field.age'))
                        ->formatStateUsing(fn ($state): string => $state ? "{$state} ani" : '—'),
                    TextEntry::make('birthplace')
                        ->label(__('field.birthplace'))
                        ->placeholder(__('placeholder.birthplace')),
                    EnumEntry::make('citizenship')
                        ->label(__('field.citizenship'))
                        ->placeholder(__('placeholder.citizenship')),
                    EnumEntry::make('ethnicity')
                        ->label(__('field.ethnicity'))
                        ->placeholder(__('placeholder.ethnicity')),
                    TextEntry::make('id_type')
                        ->label(__('field.id_type')),
                    TextEntry::make('id_serial')
                        ->label(__('field.id_serial'))
                        ->placeholder(__('placeholder.id_serial')),
                    TextEntry::make('id_number')
                        ->label(__('field.id_number'))
                        ->placeholder(__('placeholder.id_number')),
                    Location::make(AddressType::LEGAL_RESIDENCE->value)
                        ->relationship(AddressType::LEGAL_RESIDENCE->value)
                        ->city()
                        ->address()
                        ->environment(),
                    TextEntry::make('same_as_legal_residence')
                        ->label(__('field.same_as_legal_residence'))
                        ->formatStateUsing(fn ($state): string => $state ? __('general.yes') : __('general.no')),
                    Location::make(AddressType::EFFECTIVE_RESIDENCE->value)
                        ->relationship(AddressType::EFFECTIVE_RESIDENCE->value)
                        ->city()
                        ->address()
                        ->environment(),
                    TextEntry::make('primary_phone')
                        ->label(__('field.primary_phone'))
                        ->placeholder(__('placeholder.phone')),
                    TextEntry::make('backup_phone')
                        ->label(__('field.backup_phone'))
                        ->placeholder(__('placeholder.phone')),
                    TextEntry::make('email')
                        ->label(__('beneficiary.section.identity.labels.email')),
                    TextEntry::make('social_media')
                        ->label(__('beneficiary.section.identity.labels.social_media')),
                    TextEntry::make('contact_person_name')
                        ->label(__('beneficiary.section.identity.labels.contact_person_name')),
                    TextEntry::make('contact_person_phone')
                        ->label(__('beneficiary.section.identity.labels.contact_person_phone')),
                    TextEntry::make('contact_notes')
                        ->label(__('field.contact_notes'))
                        ->placeholder(__('placeholder.contact_notes'))
                        ->columnSpanFull(),
                    TextEntry::make('notes')
                        ->label(__('field.notes'))
                        ->placeholder(__('placeholder.notes'))
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected static function childrenFieldsSchema(): array
    {
        return [
            Grid::make()
                ->hidden(fn (Beneficiary $record): bool => (bool) $record->doesnt_have_children)
                ->schema([
                    TextEntry::make('children_total_count')
                        ->label(__('field.children_total_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric(),
                    TextEntry::make('children_care_count')
                        ->label(__('field.children_care_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric(),
                    TextEntry::make('children_under_18_care_count')
                        ->label(__('field.children_under_18_care_count'))
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
            Section::make(__('enum.notifier.child'))
                ->compact()
                ->hidden(fn (Beneficiary $record): bool => (bool) $record->doesnt_have_children)
                ->schema([
                    RepeatableEntry::make('children')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->columns()
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('field.child_name'))
                                ->hiddenLabel(),
                            DateEntry::make('birthdate')
                                ->label(__('field.birthdate'))
                                ->hiddenLabel(),
                            TextEntry::make('age')
                                ->label(__('field.age'))
                                ->hiddenLabel(),
                            TextEntry::make('gender')
                                ->label(__('field.gender'))
                                ->hiddenLabel(),
                            TextEntry::make('current_address')
                                ->label(__('field.current_address'))
                                ->hiddenLabel(),
                            TextEntry::make('status')
                                ->label(__('field.child_status'))
                                ->hiddenLabel(),
                            TextEntry::make('workspace')
                                ->label(__('field.workspace'))
                                ->hiddenLabel(),
                        ]),
                ]),
            TextEntry::make('children_notes')
                ->label(__('field.children_notes'))
                ->placeholder(__('placeholder.other_relevant_details'))
                ->columnSpanFull()
                ->hidden(fn (Beneficiary $record): bool => (bool) $record->doesnt_have_children),
        ];
    }
}
