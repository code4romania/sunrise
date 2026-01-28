<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Admin\Resources\InstitutionResource\RelationManagers\OrganizationsRelationManager;
use App\Filament\Admin\Resources\InstitutionResource\RelationManagers\AdminsRelationManager;
use App\Actions\BackAction;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\InstitutionResource\Actions\ActivateInstitution;
use App\Filament\Admin\Resources\InstitutionResource\Actions\InactivateInstitution;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\DocumentPreview;
use App\Infolists\Components\Location;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewInstitution extends ViewRecord
{
    protected static string $resource = InstitutionResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            InstitutionResource::getUrl() => __('institution.headings.list_title'),
            InstitutionResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    protected function getActions(): array
    {
        return [
            BackAction::make()
                ->url(InstitutionResource::getUrl()),

            ActivateInstitution::make(),

            InactivateInstitution::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('institution.headings.institution_details'))
                ->headerActions([
                    EditAction::make()
                        ->url(self::$resource::getUrl('edit_institution_details', ['record' => $this->getRecord()])),
                ])
                ->maxWidth('3xl')
                ->columns()
                ->schema($this->getInfolistSchema()),
        ]);
    }

    public static function getInfolistSchema(): array
    {
        return [
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

            TextEntry::make('area')
                ->label(__('organization.field.area'))
                ->placeholder(__('organization.placeholders.area')),

            Location::make()
                ->cityLabel(__('organization.field.city'))
                ->countyLabel(__('organization.field.county'))
                ->addressLabel(__('organization.field.address'))
                ->city(),

            TextEntry::make('representative_person.name')
                ->label(__('organization.field.representative_name')),

            TextEntry::make('representative_person.email')
                ->label(__('organization.field.representative_email')),

            TextEntry::make('representative_person.phone')
                ->label(__('organization.field.representative_phone')),

            TextEntry::make('contact_person.name')
                ->label(__('organization.field.contact_person')),

            TextEntry::make('contact_person.email')
                ->label(__('organization.field.contact_person_email')),

            TextEntry::make('contact_person.phone')
                ->label(__('organization.field.contact_person_phone')),

            TextEntry::make('website')
                ->label(__('organization.field.website')),

            TextEntry::make('organization_status_placeholder')
                ->hiddenLabel()
                ->default(__('institution.labels.organization_status'))
                ->extraAttributes(['class' => 'font-medium'])
                ->columnSpanFull(),

            DocumentPreview::make()
                ->columnSpanFull()
                ->collection('organization_status'),

            TextEntry::make('social_service_provider_certificate_placeholder')
                ->hiddenLabel()
                ->default(__('institution.labels.social_service_provider_certificate'))
                ->extraAttributes(['class' => 'font-medium'])
                ->columnSpanFull(),

            DocumentPreview::make()
                ->columnSpanFull()
                ->collection('social_service_provider_certificate'),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('institution.headings.institution_details');
    }

}
