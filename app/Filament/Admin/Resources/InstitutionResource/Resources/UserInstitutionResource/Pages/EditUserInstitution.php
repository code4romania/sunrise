<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Pages;

use App\Filament\Admin\Schemas\UserInstitutionResourceSchema;
use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditUserInstitution extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = UserInstitutionResource::class;

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

    public function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return UserInstitutionResourceSchema::form($schema);
    }

    public static function getFormComponents(): array
    {
        return UserInstitutionResourceSchema::getFormComponents();
    }
}
