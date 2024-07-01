<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Rules\ValidCNP;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Contracts\Support\Htmlable;

class CreateBeneficiary extends CreateRecord
{
    use HasWizard;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.create.title');
    }

    public function getBreadcrumb(): string
    {
        return $this->getTitle();
    }

    protected function getSteps(): array
    {
        return [
            Step::make('consent')
                ->label(__('beneficiary.wizard.consent.label'))
                ->schema([
                    Grid::make()
                        ->maxWidth('3xl')
                        ->schema([
                            Checkbox::make('consent')
                                ->label(__('field.create_beneficiary_consent'))
                                ->required()
                                ->accepted()
                                ->columnSpanFull(),

                            TextInput::make('cnp')
                                ->label(__('field.cnp'))
                                ->nullable()
                                ->rule(new ValidCNP)
                                ->hidden()
                                ->lazy(),
                        ]),
                ]),

            Step::make('beneficiary')
                ->label(__('beneficiary.wizard.beneficiary.label'))
                ->schema(EditBeneficiaryIdentity::getBeneficiaryIdentityFormSchema()),

            Step::make('children')
                ->label(__('beneficiary.wizard.children.label'))
                ->schema(EditBeneficiaryIdentity::getChildrenIdentityFormSchema()),

            Step::make('personal_information')
                ->label(__('beneficiary.wizard.personal_information.label'))
                ->schema([
                    Section::make(__('beneficiary.section.personal_information.section.beneficiary'))
                        ->columns()
                        ->schema(EditBeneficiaryPersonalInformation::beneficiarySection()),

                    Section::make(__('beneficiary.section.personal_information.section.aggressor'))
                        ->schema([
                            Group::make()
                                ->relationship('aggressor')
                                ->columns()
                                ->schema(EditBeneficiaryPersonalInformation::aggressorSection()),
                        ]),

                    Section::make(__('beneficiary.section.personal_information.section.antecedents'))
                        ->columns()
                        ->schema(EditBeneficiaryPersonalInformation::antecedentsSection()),

                    Section::make(__('beneficiary.section.personal_information.section.flow'))
                        ->columns()
                        ->schema(EditBeneficiaryPersonalInformation::flowSection()),
                ]),
        ];
    }
}
