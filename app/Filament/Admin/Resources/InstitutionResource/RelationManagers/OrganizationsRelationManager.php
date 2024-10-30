<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\RelationManagers;

use App\Filament\Admin\Resources\InstitutionResource;
use App\Infolists\Components\SectionHeader;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;

class OrganizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'organizations';

    protected static string $view = 'infolists.infolist-relation-manager';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('institution.headings.center_details');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    SectionHeader::make('center_details')
                        ->state(__('institution.headings.center_details'))
                        ->action(
                            Action::make('edit_centers')
                                ->label(__('general.action.edit'))
                                ->icon('heroicon-o-pencil')
                                ->link()
                                ->url(InstitutionResource::getUrl('edit_institution_centers', ['record' => $this->getOwnerRecord()]))
                        ),

                    RepeatableEntry::make('organizations')
                        ->hiddenLabel()
                        ->columns()
                        ->schema($this->getOrganizationInfolistSchema()),
                ]),

        ])->state(['organizations' => $this->getOwnerRecord()->organizations->toArray()]);
    }

    public static function getOrganizationInfolistSchema(): array
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
                ->label(__('institution.labels.social_service_licensing_certificate'))
                ->columnSpanFull(),

            TextEntry::make('logo')
                ->label(__('institution.labels.logo_center'))
                ->columnSpanFull(),

            TextEntry::make('organization_header')
                ->label(__('institution.labels.organization_header'))
                ->columnSpanFull(),
        ];
    }
}
