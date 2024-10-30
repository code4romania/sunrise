<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\InstitutionResource\Actions\InactivateInstitution;
use App\Infolists\Components\Location;
use App\Infolists\Components\SectionHeader;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewInstitution extends ViewRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function getActions(): array
    {
        return [
            InstitutionResource\Actions\ActivateInstitution::make(),

            InactivateInstitution::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make(__('institution.headings.institution_details'))
                        ->schema([
                            Section::make(__('institution.headings.institution_details'))
                                ->headerActions([
                                    Action::make('edit')
                                        ->label(__('general.action.edit'))
                                        ->icon('heroicon-o-pencil')
                                        ->link()
                                        ->url(self::$resource::getUrl('edit_institution_details', ['record' => $this->getRecord()])),
                                ])
                                ->maxWidth('3xl')
                                ->columns()
                                ->schema([
                                    TextEntry::make('name')
                                        ->label(__('organization.field.name')),

                                    TextEntry::make('short_name')
                                        ->label(__('organization.field.short_name')),

                                    TextEntry::make('type')
                                        ->label(__('organization.field.type')),

                                    TextEntry::make('cif')
                                        ->label(__('organization.field.cif')),

                                    TextEntry::make('main_activity')
                                        ->label(__('organization.field.main_activity')),

                                    Location::make()
                                        ->city(),

                                    TextEntry::make('address')
                                        ->label(__('organization.field.address')),

                                    TextEntry::make('phone')
                                        ->label(__('organization.field.phone')),

                                    TextEntry::make('reprezentative_name')
                                        ->label(__('organization.field.reprezentative_name')),

                                    TextEntry::make('reprezentative_email')
                                        ->label(__('organization.field.reprezentative_email')),

                                    TextEntry::make('website')
                                        ->label(__('organization.field.website')),

                                    TextEntry::make('organization_status')
                                        ->label(__('institution.labels.organization_status'))
                                        ->columnSpanFull(),

                                    TextEntry::make('social_service_provider_certificate')
                                        ->label(__('institution.labels.social_service_provider_certificate'))
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    Tab::make(__('institution.headings.center_details'))
                        ->schema([
                            Section::make()
                                ->schema([
                                    SectionHeader::make('center_details')
                                        ->state(__('institution.headings.center_details'))
                                        ->action(
                                            Action::make('edit_centers')
                                                ->label(__('general.action.edit'))
                                                ->icon('heroicon-o-pencil')
                                                ->link()
                                                ->url(self::$resource::getUrl('edit_institution_centers', ['record' => $this->getRecord()]))
                                        ),

                                    RepeatableEntry::make('organizations')
                                        ->maxWidth('3xl')
                                        ->hiddenLabel()
                                        ->columns()
                                        ->schema([
                                            TextEntry::make('name')
                                                ->label(__('institution.labels.center_name')),

                                            TextEntry::make('short_name')
                                                ->label(__('organization.field.short_name')),

                                            TextEntry::make('main_activity')
                                                ->label(__('organization.field.main_activity'))
                                                ->columnSpanFull(),

                                            TextEntry::make('social_service_licensing_certificate')
                                                ->label(__('institution.labels.social_service_licensing_certificate'))
                                                ->columnSpanFull(),

                                            TextEntry::make('logo')
                                                ->label(__('institution.labels.logo_center'))
                                                ->columnSpanFull(),

                                            TextEntry::make('organization_header')
                                                ->label(__('institution.labels.organization_header'))
                                                ->columnSpanFull(),
                                        ]),
                                ]),

                        ]),

                    Tab::make(__('institution.headings.ngo_admin'))
                        ->schema([
                            Section::make(__('institution.headings.admin_users'))
                                ->headerActions([
                                    Action::make('add_ngo_admin')
                                        ->label(__('institution.actions.add_ngo_admin'))
                                        ->form([
                                            TextInput::make('first_name')
                                                ->label(__('institution.labels.first_name')),

                                            TextInput::make('last_name')
                                                ->label(__('institution.labels.last_name')),

                                            TextInput::make('email')
                                                ->label(__('institution.labels.email')),

                                            TextInput::make('phone')
                                                ->label(__('institution.labels.phone')),

                                            Hidden::make('ngo_admin')
                                                ->default(1),
                                        ]),
                                ])
                                ->schema([
                                    RepeatableEntry::make('admins')
                                        ->hiddenLabel()
                                        ->action(Action::make('view_user'))
                                        ->columns(4)
                                        ->schema([
                                            SectionHeader::make('admin_users')
                                                ->action(
                                                    Action::make('view_user')
                                                        ->label(__('general.action.view_details'))
                                                        ->link()
                                                        ->url(
                                                            fn ($record) => self::$resource::getUrl('user.view', [
                                                                'parent' => $this->getRecord(),
                                                                'record' => $record,
                                                            ])
                                                        ),
                                                ),

                                            TextEntry::make('first_name')
                                                ->label(__('institution.labels.first_name')),

                                            TextEntry::make('last_name')
                                                ->label(__('institution.labels.last_name')),

                                            TextEntry::make('email')
                                                ->label(__('institution.labels.email')),

                                            TextEntry::make('phone')
                                                ->label(__('institution.labels.phone')),
                                        ]),
                                ]),
                        ]),
                ]),
        ]);
    }
}
