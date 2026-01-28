<?php

declare(strict_types=1);

namespace App\Filament\Admin\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInstitutionResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->maxWidth('3xl')
                ->columns()
                ->schema(self::getFormComponents()),
        ]);
    }

    public static function getFormComponents(): array
    {
        return [
            TextInput::make('first_name')
                ->label(__('institution.labels.first_name'))
                ->placeholder(__('institution.placeholders.first_name'))
                ->maxLength(50)
                ->required(),

            TextInput::make('last_name')
                ->label(__('institution.labels.last_name'))
                ->placeholder(__('institution.placeholders.last_name'))
                ->maxLength(50)
                ->required(),

            TextInput::make('email')
                ->label(__('institution.labels.email'))
                ->placeholder(__('institution.placeholders.email'))
                ->maxLength(50)
                ->email()
                ->unique()
                ->required(),

            TextInput::make('phone')
                ->label(__('institution.labels.phone'))
                ->placeholder(__('institution.placeholders.phone'))
                ->maxLength(14)
                ->tel()
                ->required(),

            \Filament\Forms\Components\Hidden::make('ngo_admin')
                ->default(1),
        ];
    }
}
