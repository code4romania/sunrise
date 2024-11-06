<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Filament\Admin\Resources\InstitutionResource;
use App\Forms\Components\Repeater;
use App\Models\Organization;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditInstitutionCenters extends EditRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('view', [
            'record' => $this->getRecord(),
            'activeRelationManager' => 'organizations',
        ]);
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

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Repeater::make('organizations')
                ->maxWidth('3xl')
                ->hiddenLabel()
                ->columns()
                ->minItems(1)
                ->relationship('organizations')
                ->addActionLabel(__('institution.actions.add_organization'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('institution.labels.center_name'))
                        ->placeholder(__('organization.placeholders.center_name'))
                        ->maxLength(200)
                        ->required(),

                    TextInput::make('short_name')
                        ->label(__('organization.field.short_name'))
                        ->placeholder(__('organization.placeholders.center_short_name'))
                        ->maxLength(50),

                    TextInput::make('main_activity')
                        ->label(__('organization.field.main_activity'))
                        ->placeholder(__('organization.placeholders.main_activity'))
                        ->columnSpanFull()
                        ->maxLength(200)
                        ->required(),

                    SpatieMediaLibraryFileUpload::make('social_service_licensing_certificate')
                        ->label(__('institution.labels.social_service_licensing_certificate'))
                        ->helperText(__('institution.helper_texts.social_service_licensing_certificate'))
                        ->collection('social_service_licensing_certificate')
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('logo')
                        ->label(__('institution.labels.logo_center'))
                        ->helperText(__('institution.helper_texts.logo'))
                        ->collection('logo')
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('organization_header')
                        ->label(__('institution.labels.organization_header'))
                        ->helperText(__('institution.helper_texts.organization_header'))
                        ->collection('organization_header')
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function afterSave()
    {
        $this->getRecord()
            ->organizations
            ?->each(
                fn (Organization $organization) => $organization->load('admins')
                    ->admins()
                    ->attach(
                        $this->getRecord()
                            ->admins
                            ->pluck('id')
                            ->diff(
                                $organization->admins
                                    ->pluck('id')
                            )
                    )
            );
    }
}
