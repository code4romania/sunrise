<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Users\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('user.heading.specialist_details'))
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('last_name')
                                    ->label(__('user.labels.first_name'))
                                    ->required()
                                    ->maxLength(50),
                                TextInput::make('first_name')
                                    ->label(__('user.labels.last_name'))
                                    ->required()
                                    ->maxLength(50),
                                TextInput::make('email')
                                    ->label(__('user.labels.email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('phone_number')
                                    ->label(__('user.labels.phone_number'))
                                    ->tel()
                                    ->maxLength(14),
                                Hidden::make('ngo_admin')
                                    ->default(1),
                            ]),
                    ]),
            ]);
    }
}
