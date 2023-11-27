<?php

declare(strict_types=1);

namespace App\Filament\Pages\Profile;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo as BasePersonalInfo;

class UserPersonalInfo extends BasePersonalInfo
{
    public array $only = [
        'first_name',
        'last_name',
        'email',
    ];

    protected function getProfileFormSchema(): array
    {
        $groupFields = [];

        if ($this->hasAvatars) {
            $groupFields[] = SpatieMediaLibraryFileUpload::make('avatar')
                ->label(__('filament-breezy::default.fields.avatar'))
                ->avatar()
                ->collection('avatars')
                ->conversion('large');
        }

        $groupFields[] = Group::make()
            ->columnSpan(2)
            ->columns(2)
            ->schema([
                TextInput::make('first_name'),
                TextInput::make('last_name'),

                $this->getEmailComponent()
                    ->columnSpanFull(),
            ]);

        return $groupFields;
    }
}
