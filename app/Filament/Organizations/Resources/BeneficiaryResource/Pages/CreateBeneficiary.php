<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use App\Rules\ValidCNP;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

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

                            Placeholder::make('consent_placeholder')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->content(__('beneficiary.placeholder.consent')),

                            Placeholder::make('consent_placeholder')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->content(new HtmlString('<b>' . __('beneficiary.placeholder.check_beneficiary_exists') . '</b>')),

                            TextInput::make('cnp')
                                ->label(__('field.cnp'))
                                ->nullable()
                                ->rule(new ValidCNP)
                                ->hidden()
                                ->hintAction(
                                    Action::make('check_cnp')
                                        ->label(__('field.check'))
                                        ->action(function (Get $get, Set $set) {
                                            $beneficiary = Beneficiary::query()
                                                ->where('cnp', $get('cnp'))
                                                ->first();
                                            if ($beneficiary !== null) {
                                                $set('beneficiary_status', 1);
                                            } else {
                                                $set('beneficiary_status', 0);
                                            }
                                        }),
                                )
                                ->lazy(),

                            Hidden::make('beneficiary_status')
                                ->live(),

                            Placeholder::make('beneficiary_exists')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->content(new HtmlString(__('beneficiary.placeholder.beneficiary_exists')))
                                ->visible(fn (Get $get) => $get('beneficiary_status') === 1),

                            Placeholder::make('beneficiary_not_exists')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->content(new HtmlString(__('beneficiary.placeholder.beneficiary_not_exists')))
                                ->visible(fn (Get $get) => $get('beneficiary_status') === 0),
                        ]),
                ]),

            Step::make('beneficiary')
                ->label(__('beneficiary.wizard.beneficiary.label'))
                ->schema(EditBeneficiaryIdentity::getBeneficiaryIdentityFormSchema()),

            Step::make('children')
                ->label(__('beneficiary.wizard.children.label'))
                ->schema(EditChildrenIdentity::getChildrenIdentityFormSchema()),

            Step::make('personal_information')
                ->label(__('beneficiary.wizard.personal_information.label'))
                ->schema([
                    Section::make(__('beneficiary.section.personal_information.section.beneficiary'))
                        ->columns()
                        ->schema(EditBeneficiaryPersonalInformation::beneficiarySection()),

                    Section::make(__('beneficiary.section.personal_information.section.aggressor'))
                        ->schema([
                            Group::make()
                                ->columns()
                                ->schema(EditAggressor::aggressorSection()),
                        ]),

                    Section::make(__('beneficiary.section.personal_information.section.antecedents'))
                        ->columns()
                        ->schema(EditAntecedents::antecedentsSection()),

                    Section::make(__('beneficiary.section.personal_information.section.flow'))
                        ->columns()
                        ->schema(EditFlowPresentation::flowSection()),
                ]),
        ];
    }
}
