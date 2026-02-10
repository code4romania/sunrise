<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Pages;

use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use App\Filament\Admin\Resources\Institutions\Schemas\InstitutionFormSchema;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateInstitution extends CreateRecord
{
    use HasWizard;

    protected static string $resource = InstitutionResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('institution.headings.create_title');
    }

    public function getBreadcrumb(): string
    {
        return __('institution.headings.create_title');
    }

    protected function getSteps(): array
    {
        return [
            Step::make(__('institution.headings.institution_details'))
                ->schema($this->getInstitutionDetailsSchema()),
            Step::make(__('institution.headings.center_details'))
                ->schema([
                    TextEntry::make('center_details')
                        ->hiddenLabel()
                        ->state(__('institution.placeholders.center_details')),
                    ...$this->getCentersSchema(),
                ]),
            Step::make(__('institution.headings.ngo_admin'))
                ->schema([
                    TextEntry::make('ngo_admins')
                        ->hiddenLabel()
                        ->state(__('institution.placeholders.ngo_admins')),
                    ...$this->getAdminsSchema(),
                ]),
        ];
    }

    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    protected function getInstitutionDetailsSchema(): array
    {
        return InstitutionFormSchema::getSchema();
    }

    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    protected function getCentersSchema(): array
    {
        return [
            Repeater::make('organizations')
                ->relationship()
                ->hiddenLabel()
                ->minItems(1)
                ->addActionLabel(__('institution.actions.add_organization'))
                ->schema([
                    Section::make()
                        ->hiddenLabel()
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label(__('institution.labels.center_name'))
                                ->placeholder(__('organization.placeholders.center_name'))
                                ->required()
                                ->maxLength(200),
                            TextInput::make('short_name')
                                ->label(__('organization.field.short_name'))
                                ->placeholder(__('organization.placeholders.center_short_name'))
                                ->maxLength(50),
                            TextInput::make('main_activity')
                                ->label(__('organization.field.main_activity'))
                                ->placeholder(__('organization.placeholders.main_activity'))
                                ->required()
                                ->maxLength(200)
                                ->columnSpanFull(),
                            SpatieMediaLibraryFileUpload::make('social_service_licensing_certificate')
                                ->label(__('institution.labels.social_service_licensing_certificate'))
                                ->helperText(__('institution.helper_texts.social_service_licensing_certificate'))
                                ->collection('social_service_licensing_certificate')
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                ->columnSpanFull(),
                            SpatieMediaLibraryFileUpload::make('logo')
                                ->label(__('institution.labels.logo_center'))
                                ->helperText(__('institution.helper_texts.logo'))
                                ->collection('logo')
                                ->acceptedFileTypes(['image/*'])
                                ->columnSpanFull(),
                            SpatieMediaLibraryFileUpload::make('organization_header')
                                ->label(__('institution.labels.organization_header'))
                                ->helperText(__('institution.helper_texts.organization_header'))
                                ->collection('organization_header')
                                ->acceptedFileTypes(['image/*'])
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    protected function getAdminsSchema(): array
    {
        return [
            Repeater::make('admins')
                ->relationship()
                ->minItems(1)
                ->addActionLabel(__('institution.actions.add_admin'))
                ->schema([
                    Section::make()
                        ->hiddenLabel()
                        ->columns(2)
                        ->schema([
                            TextInput::make('first_name')
                                ->label(__('institution.labels.first_name'))
                                ->placeholder(__('institution.placeholders.first_name'))
                                ->required()
                                ->maxLength(50),
                            TextInput::make('last_name')
                                ->label(__('institution.labels.last_name'))
                                ->placeholder(__('institution.placeholders.last_name'))
                                ->required()
                                ->maxLength(50),
                            TextInput::make('email')
                                ->label(__('institution.labels.email'))
                                ->placeholder(__('institution.placeholders.email'))
                                ->email()
                                ->required()
                                ->unique(User::class, 'email', ignorable: fn (?Model $record) => $record)
                                ->maxLength(255),
                            TextInput::make('phone_number')
                                ->label(__('institution.labels.phone'))
                                ->placeholder(__('institution.placeholders.phone'))
                                ->tel()
                                ->required()
                                ->maxLength(14),
                            Hidden::make('ngo_admin')
                                ->default(1),
                        ]),
                ]),
        ];
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label(__('institution.account_type.finalize'));
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label(__('institution.account_type.cancel'));
    }
}
