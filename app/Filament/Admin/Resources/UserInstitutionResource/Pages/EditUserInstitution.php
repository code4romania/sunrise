<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserInstitutionResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\UserInstitutionResource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditUserInstitution extends EditRecord
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
