<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserInstitutionResource\Pages;

use App\Actions\BackAction;
use App\Concerns\HasParentResource;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\UserInstitutionResource;
use App\Filament\Admin\Resources\UserInstitutionResource\Actions\ActivateUserAction;
use App\Filament\Admin\Resources\UserInstitutionResource\Actions\DeactivateUserAction;
use App\Filament\Admin\Resources\UserInstitutionResource\Actions\ResendInvitationAction;
use App\Infolists\Components\DateTimeEntry;
use App\Infolists\Components\SectionHeader;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewUserInstitution extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = UserInstitutionResource::class;

    protected function getRedirectUrl(): ?string
    {
        return self::getParentResource()::getUrl('view', [
            'record' => $this->parent,
            'activeRelationManager' => 'admins',
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            InstitutionResource::getUrl() => __('institution.headings.list_title'),
            InstitutionResource::getUrl('view', ['record' => $this->parent]) => $this->parent->name,
            InstitutionResource::getUrl('user.view', [
                'parent' => $this->parent,
                'record' => $this->getRecord(),
            ]) => $this->getRecord()->full_name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->full_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),

            ActivateUserAction::make(),

            DeactivateUserAction::make(),

            ResendInvitationAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    TextEntry::make('userStatus.status')
                        ->label(__('user.labels.status')),

                    DateTimeEntry::make('last_login_at')
                        ->label(__('user.labels.last_login_at_date_time')),
                ]),
            Section::make()
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    SectionHeader::make('edit_user')
                        ->state(__('user.heading.specialist_details'))
                        ->action(
                            Action::make('edit')
                                ->label(__('general.action.edit'))
                                ->link()
                                ->url(self::getParentResource()::getUrl('user.edit', [
                                    'parent' => $this->parent,
                                    'record' => $this->getRecord(),
                                ]))
                        ),

                    TextEntry::make('first_name')
                        ->label(__('user.labels.first_name')),
                    TextEntry::make('last_name')
                        ->label(__('user.labels.last_name')),
                    TextEntry::make('email')
                        ->label(__('user.labels.email')),
                    TextEntry::make('phone_number')
                        ->label(__('user.labels.phone_number')),
                ]),
        ]);
    }
}
