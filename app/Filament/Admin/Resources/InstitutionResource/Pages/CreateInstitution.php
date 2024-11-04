<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\UserInstitutionResource\Pages\EditUserInstitution;
use App\Forms\Components\Repeater;
use App\Models\Organization;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

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

    public function getSteps(): array
    {
        return [
            Step::make(__('institution.headings.institution_details'))
                ->schema(EditInstitutionDetails::getSchema()),

            Step::make(__('institution.headings.center_details'))
                ->schema([
                    Placeholder::make('center_details')
                        ->hiddenLabel()
                        ->maxWidth('3xl')
                        ->content(__('institution.placeholders.center_details')),

                    ...EditInstitutionCenters::getSchema(),
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
                            ...EditUserInstitution::getSchema(),

                            Hidden::make('ngo_admin')
                                ->default(1),
                        ]),
                ]),
        ];
    }

    public function afterCreate()
    {
        $record = $this->getRecord();
        $admins = $record->admins;
        $organizations = $record->organizations;

        $organizations->each(fn (Organization $organization) => $organization->users()->attach($admins->pluck('id')));
    }
}
