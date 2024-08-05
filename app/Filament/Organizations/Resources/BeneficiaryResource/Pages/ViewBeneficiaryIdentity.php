<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\EnumEntry;
use App\Infolists\Components\Location;
use App\Infolists\Components\Notice;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
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
        return  __('beneficiary.page.identity.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getIdentityBreadcrumbs();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                            ->maxWidth('3xl')
                            ->schema(static::getBeneficiaryIdentityFormSchema()),

                        Tab::make(__('beneficiary.section.identity.tab.children'))
                            ->maxWidth('3xl')
                            ->schema(static::getChildrenIdentityFormSchema()),
                    ]),
            ]);
    }

    public static function getBeneficiaryIdentityFormSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.identity.tab.beneficiary'))
                ->headerActions([
                    BeneficiaryResource\Actions\Edit::make('edit')
                        ->url(fn ($record) => BeneficiaryResource::getUrl('edit_identity', ['record' => $record])),
                ])
                ->schema(self::identitySchema()),
        ];
    }

    public static function identitySchemaForOtherPage(Beneficiary $record): array
    {
        return [
            Section::make()
                ->key('identity')
                ->columnSpan(1)
                ->columns()
                ->schema([
                    Notice::make('identity')
                        ->icon('heroicon-s-information-circle')
                        ->state(__('beneficiary.section.identity.heading_description'))
                        ->color('primary')
                        ->action(
                            Action::make('view')
                                ->label(__('beneficiary.section.identity.title'))
                                ->url(self::$resource::getUrl('view_identity', ['record' => $record]))
                                ->link(),
                        ),

                    ...self::identitySchema(),
                ]),
        ];
    }

    protected static function identitySchema(): array
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

                    TextEntry::make('birthdate')
                        ->label(__('field.birthdate')),

                    TextEntry::make('birthplace')
                        ->label(__('field.birthplace'))
                        ->placeholder(__('placeholder.birthplace')),

                    EnumEntry::make('citizenship')
                        ->label(__('field.citizenship'))
                        ->placeholder(__('placeholder.citizenship')),

                    EnumEntry::make('ethnicity')
                        ->label(__('field.ethnicity'))
                        ->placeholder(__('placeholder.ethnicity')),

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

                    TextEntry::make('email')
                        ->label(__('beneficiary.section.identity.labels.email'))
                        ->icon('heroicon-o-envelope'),

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
        ];
    }

    public static function getChildrenIdentityFormSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.identity.tab.children'))
                ->headerActions([
                    BeneficiaryResource\Actions\Edit::make('edit')
                        ->url(fn ($record) => BeneficiaryResource::getUrl('edit_children', ['record' => $record])),
                ])
                ->schema(self::childrenSchema()),
        ];
    }

    public static function childrenSchemaForOtherPage(Beneficiary $record): array
    {
        return [
            Section::make()
                ->columnSpan(1)
                ->columns()
                ->schema([
                    Notice::make('children')
                        ->icon('heroicon-s-information-circle')
                        ->state(__('beneficiary.section.identity.heading_description'))
                        ->color('primary')
                        ->action(
                            Action::make('view')
                                ->label(__('beneficiary.section.identity.title'))
                                ->url(self::$resource::getUrl('view_identity', ['record' => $record]))
                                ->link(),
                        ),

                    ...self::childrenSchema(),
                ]),
        ];
    }

    public static function childrenSchema(): array
    {
        return [
            Grid::make()
                ->hidden(fn ($record) => $record->doesnt_have_children)
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

                    TextEntry::make('birthdate')
                        ->label(__('field.birthdate')),

                    TextEntry::make('address')
                        ->label(__('field.current_address')),

                    TextEntry::make('status')
                        ->label(__('field.child_status')),
                ]),

            TextEntry::make('children_notes')
                ->label(__('field.children_notes'))
                ->placeholder(__('placeholder.other_relevant_details'))
                ->columnSpanFull(),
        ];
    }
}
