<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Schemas\InstitutionResourceSchema;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditInstitutionDetails extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = InstitutionResource::class;

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            InstitutionResource::getUrl() => __('institution.headings.list_title'),
            InstitutionResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(InstitutionResourceSchema::getFormSchemaForDetails());
    }

    public function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),
        ];
    }

    public static function getFormComponents(): array
    {
        return InstitutionResourceSchema::getFormSchemaForDetails();
    }
}
