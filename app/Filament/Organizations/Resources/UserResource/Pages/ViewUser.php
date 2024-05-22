<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\UserResource\Pages;

use App\Enums\UserStatus;
use App\Filament\Organizations\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            ...[Group::make([
                Placeholder::make('status')
                    ->content(fn (User $record) => $record->status?->label()),
                Placeholder::make('updated_at')
                    ->content(fn (User $record) => $record->updated_at),
            ])
                ->columns()
                ->columnSpanFull()],
            ...UserResource::getSchema(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make('edit'),

            UserResource\Actions\DeactivateUserAction::make(),

            Actions\Action::make('reset_password')
                ->label(__('user.actions.reset_password'))
                ->visible(fn (User $record) => UserStatus::isValue($record->status, UserStatus::ACTIVE))
                ->action(fn (User $record) => $record->resetPassword()),

            UserResource\Actions\ResendInvitationAction::make(),
        ];
    }

    public function getBreadcrumb(): string
    {
        return $this->record->getFilamentName();
    }

    public function getHeading(): string|Htmlable
    {
        return $this->record->getFilamentName();
    }
}
