<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages\Profile;

use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo as BasePersonalInfo;

class UserPersonalInfo extends BasePersonalInfo
{
    public array $only = [
        'first_name',
        'last_name',
        'email',
    ];

    public function mount(): void
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();

        $this->form->fill($this->user->only($this->only));
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->statePath('data')
            ->model($this->user)
            ->schema([
                SpatieMediaLibraryFileUpload::make('avatar')
                    ->label(__('filament-breezy::default.fields.avatar'))
                    ->avatar()
                    ->collection('avatar')
                    ->conversion('large'),

                Group::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('field.first_name')),

                        TextInput::make('last_name')
                            ->label(__('field.last_name')),

                        TextInput::make('email')
                            ->label(__('field.last_name'))
                            ->unique(ignoreRecord:true)
                            ->required()
                            ->email()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
