<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages\Profile;

use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\SimplePage;
use Filament\Actions\Action;

class UserPersonalInfo extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.organizations.pages.profile.user-personal-info';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Filament::getCurrentOrDefaultPanel()->auth()->user();
        $this->form->fill($user->only(['first_name', 'last_name', 'email']));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->statePath('data')
            ->model(Filament::getCurrentOrDefaultPanel()->auth()->user())
            ->components([
                SpatieMediaLibraryFileUpload::make('avatar')
                    ->label(__('user.labels.avatar'))
                    ->avatar()
                    ->collection('avatar')
                    ->conversion('large'),

                Group::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('field.first_name'))
                            ->required(),

                        TextInput::make('last_name')
                            ->label(__('field.last_name'))
                            ->required(),

                        TextInput::make('email')
                            ->label(__('field.email'))
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->email()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('general.action.save'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $user = Filament::getCurrentOrDefaultPanel()->auth()->user();
        $user->update($this->form->getState());
        
        $this->notify('success', __('user.notifications.profile_updated'));
    }
}
