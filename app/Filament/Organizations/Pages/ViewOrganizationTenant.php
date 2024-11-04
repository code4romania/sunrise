<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages;

use App\Filament\Admin\Resources\InstitutionResource\Pages\ViewInstitution;
use App\Filament\Admin\Resources\InstitutionResource\RelationManagers\OrganizationsRelationManager;
use App\Infolists\Components\Notice;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ViewOrganizationTenant extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static string $view = 'filament.organizations.pages.view-organization-tenant';

    protected static ?int $navigationSort = 31;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.configurations._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('organization.profile');
    }

    public function getTitle(): string|Htmlable
    {
        return __('organization.profile');
    }

    public function infolist()
    {
        return Infolist::make()
            ->schema([
                Section::make()
                    ->maxWidth('3xl')
                    ->schema([
                        Notice::make('notice')
                            ->icon('heroicon-s-information-circle')
                            ->state(__('organization.helper_texts.view_tenant_info'))
                            ->color('primary'),

                        Grid::make()
                            ->relationship('institution')
                            ->schema(ViewInstitution::getInfolistSchema()),

                        Grid::make()
                            ->schema(OrganizationsRelationManager::getOrganizationInfolistSchema()),
                    ]),

            ])
            ->state(Filament::getTenant()->load('institution')->toArray());
    }
}
