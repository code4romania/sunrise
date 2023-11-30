<?php

declare(strict_types=1);

namespace App\Filament\Resources\BeneficiaryResource\Pages;

use App\Filament\Resources\BeneficiaryResource;
use App\Rules\ValidCNP;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

class CreateBeneficiary extends CreateRecord
{
    use HasWizard;

    protected static string $resource = BeneficiaryResource::class;

    protected function getSteps(): array
    {
        return [
            Step::make('consent')
                ->label(__('beneficiary.wizard.consent.label'))
                ->schema([
                    Group::make()
                        ->columnSpan(3)
                        ->columns(2)
                        ->schema([
                            Checkbox::make('consent')
                                ->label(__('beneficiary.wizard.consent.fields.consent'))
                                ->required()
                                ->accepted()
                                ->columnSpanFull(),

                            TextInput::make('cnp')
                                ->label(__('beneficiary.wizard.consent.fields.cnp'))
                                ->nullable()
                                ->rule(new ValidCNP),
                        ]),
                ])
                ->columns(5)
                ->afterValidation(function ($state, Step $component) {
                }),

            Step::make('beneficiary')
                ->label(__('beneficiary.wizard.beneficiary.label'))
                ->schema([
                    // ...
                ]),

            Step::make('children')
                ->label(__('beneficiary.wizard.children.label'))
                ->schema([
                    // ...
                ]),

            Step::make('personal_information')
                ->label(__('beneficiary.wizard.personal_information.label'))
                ->schema([
                    // ...
                ]),

        ];
    }
}
