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
        return $form->schema(
            array_merge(
                [
                    Group::make([
                        Placeholder::make('status')
                            ->content(fn (User $record) => $record->status?->label()),
                        Placeholder::make('updated_at')
                            ->content(fn (User $record) => $record->updated_at),
                    ])
                        ->columns()
                        ->columnSpanFull()],
                UserResource::getSchema()
            )
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make('edit'),

            Actions\Action::make('deactivate')
                ->label(__('user.actions.deactivate'))
                ->visible(fn (User $record) => UserStatus::isValue($record->status, UserStatus::ACTIVE))
                ->action(fn (User $record) => $record->deactivate()),

            Actions\Action::make('reset_password')
                ->label(__('user.actions.reset_password'))
                ->visible(fn (User $record) => UserStatus::isValue($record->status, UserStatus::ACTIVE))
                ->action(fn (User $record) => $record->resetPassword()),

            Actions\Action::make('resend_invitation')
                ->label(__('user.actions.resend_invitation'))
                ->visible(fn (User $record) => UserStatus::isValue($record->status, UserStatus::PENDING))
                ->action(fn (User $record) => $record->resendInvitation()),
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
