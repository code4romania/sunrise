<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserInstitutionResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Admin\Resources\UserInstitutionResource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditUserInstitution extends EditRecord
{
    use HasParentResource;

    protected static string $resource = UserInstitutionResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->maxWidth('3xl')
                ->columns()
                ->schema(self::getSchema()),
        ]);
    }

    public static function getSchema(): array
    {
        return [
            TextInput::make('first_name')
                ->label(__('institution.labels.first_name')),

            TextInput::make('last_name')
                ->label(__('institution.labels.last_name')),

            TextInput::make('email')
                ->label(__('institution.labels.email')),

            TextInput::make('phone')
                ->label(__('institution.labels.phone')),
        ];
    }
}
