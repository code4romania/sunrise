<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\RelationManagers;

use Filament\Schemas\Schema;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\DocumentPreview;
use App\Models\Institution;
use Filament\Facades\Filament;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;

class OrganizationsRelationManager extends RelationManager
{
    use PreventSubmitFormOnEnter;

    protected static string $relationship = 'organizations';

    protected string $view = 'infolists.infolist-relation-manager';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('institution.headings.center_details');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->heading(__('institution.headings.center_details'))
                ->headerActions([
                    EditAction::make('edit_centers')
                        ->url(InstitutionResource::getUrl('edit_institution_centers', ['record' => $this->getOwnerRecord()])),
                ])
                ->maxWidth('3xl')
                ->schema([
                    RepeatableEntry::make('organizations')
                        ->hiddenLabel()
                        ->columns()
                        ->schema($this->getOrganizationInfolistSchema($this->getOwnerRecord())),
                ]),

        ])->state(['organizations' => $this->getOwnerRecord()->organizations->toArray()]);
    }

    public static function getOrganizationInfolistSchema(?Institution $institution = null): array
    {
        return [
            TextEntry::make('name')
                ->label(__('institution.labels.center_name')),

            TextEntry::make('short_name')
                ->label(__('organization.field.short_name')),

            TextEntry::make('main_activity')
                ->label(__('organization.field.main_activity'))
                ->columnSpanFull(),

            TextEntry::make('social_service_licensing_certificate')
                ->hiddenLabel()
                ->default(__('institution.labels.social_service_licensing_certificate'))
                ->extraAttributes(['class' => 'font-medium'])
                ->columnSpanFull(),

            DocumentPreview::make()
                ->columnSpanFull()
                ->record(function () use ($institution) {
                    if (! $institution) {
                        return Filament::getTenant();
                    }
                    static $organizationIndex = 0;

                    return $institution->organizations->get($organizationIndex++);
                })
                ->collection('social_service_licensing_certificate'),

            TextEntry::make('logo')
                ->hiddenLabel()
                ->default(__('institution.labels.logo_center'))
                ->extraAttributes(['class' => 'font-medium'])
                ->columnSpanFull(),

            DocumentPreview::make()
                ->columnSpanFull()
                ->record(function () use ($institution) {
                    if (! $institution) {
                        return Filament::getTenant();
                    }
                    static $organizationIndex = 0;

                    return $institution->organizations->get($organizationIndex++);
                })
                ->collection('logo'),

            TextEntry::make('organization_header')
                ->hiddenLabel()
                ->default(__('institution.labels.organization_header'))
                ->extraAttributes(['class' => 'font-medium'])
                ->columnSpanFull(),

            DocumentPreview::make()
                ->columnSpanFull()
                ->record(function () use ($institution) {
                    if (! $institution) {
                        return Filament::getTenant();
                    }
                    static $organizationIndex = 0;

                    return $institution->organizations->get($organizationIndex++);
                })
                ->collection('organization_header'),
        ];
    }
}
