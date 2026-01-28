<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Pages;

use Filament\Schemas\Schema;
use App\Actions\BackAction;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource;
use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Actions\ActivateUserAction;
use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Actions\DeactivateUserAction;
use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Actions\ResendInvitationAction;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\DateTimeEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewUserInstitution extends ViewRecord
{
    protected static string $resource = UserInstitutionResource::class;
    protected static ?string $parentResource = InstitutionResource::class;

    protected function getRedirectUrl(): ?string
    {
        $parentRecord = $this->getParentRecord();

        return InstitutionResource::getUrl('view', [
            'record' => $parentRecord,
            'activeRelationManager' => 'admins',
        ]);
    }

    public function getBreadcrumbs(): array
    {
        $parentRecord = $this->getParentRecord();
        $record = $this->getRecord();

        return [
            InstitutionResource::getUrl() => __('institution.headings.list_title'),
            InstitutionResource::getUrl('view', ['record' => $parentRecord]) => $parentRecord->name,
            static::getResource()::getUrl('view', [
                'institution' => $parentRecord,
                'record' => $record,
            ]) => $record->full_name,
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

    public function infolist(Schema $schema): Schema
    {
        $record = $this->getRecord();
        $parentRecord = $this->getParentRecord();

        return $schema->components([
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
                ->heading(__('user.heading.specialist_details'))
                ->headerActions([
                    EditAction::make()
                        ->url(function () use ($record, $parentRecord) {
                            return static::getResource()::getUrl('edit', [
                                'institution' => $parentRecord,
                                'record' => $record,
                            ]);
                        }),
                ])
                ->maxWidth('3xl')
                ->columns()
                ->schema([
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
