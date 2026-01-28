<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource\Pages\EditUserInstitution;
use App\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;

class CreateInstitution extends CreateRecord
{
    use HasWizard;
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;

    protected static string $resource = InstitutionResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('institution.headings.create_title');
    }

    public function getBreadcrumb(): string
    {
        return __('institution.headings.create_title');
    }

    public function getSteps(): array
    {
        return [
            Step::make(__('institution.headings.institution_details'))
                ->schema(EditInstitutionDetails::getFormComponents()),

            Step::make(__('institution.headings.center_details'))
                ->schema([
                    Placeholder::make('center_details')
                        ->hiddenLabel()
                        ->maxWidth('3xl')
                        ->content(__('institution.placeholders.center_details')),

                    ...\App\Filament\Admin\Schemas\InstitutionResourceSchema::getFormSchemaForCenters(),
                ]),

            Step::make(__('institution.headings.ngo_admin'))
                ->schema([
                    Placeholder::make('ngo_admins')
                        ->hiddenLabel()
                        ->maxWidth('3xl')
                        ->content(__('institution.placeholders.ngo_admins')),

                    Repeater::make('admins')
                        ->maxWidth('3xl')
                        ->hiddenLabel()
                        ->columns()
                        ->minItems(1)
                        ->relationship('admins')
                        ->addActionLabel(__('institution.actions.add_admin'))
                        ->schema([
                            ...EditUserInstitution::getFormComponents(),

                            Hidden::make('ngo_admin')
                                ->default(1),
                        ]),
                ]),
        ];
    }
}
