<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToDetailedEvaluation;
use App\Enums\Occupation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Location;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditBeneficiaryPartner extends EditRecord
{
    use RedirectToDetailedEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbsForDetailedEvaluation();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.partner.label'));
    }

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->relationship('partner')
                ->columns()
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                        ->schema([
                            TextInput::make('last_name')
                                ->label(__('field.last_name'))
                                ->placeholder(__('beneficiary.placeholder.last_name'))
                                ->maxLength(50),

                            TextInput::make('first_name')
                                ->label(__('field.first_name'))
                                ->placeholder(__('beneficiary.placeholder.first_name'))
                                ->maxLength(50),

                            TextInput::make('age')
                                ->label(__('field.age'))
                                ->placeholder(__('beneficiary.placeholder.age'))
                                ->maxLength(2),

                            Select::make('occupation')
                                ->label(__('field.occupation'))
                                ->placeholder(__('beneficiary.placeholder.occupation'))
                                ->options(Occupation::options())
                                ->enum(Occupation::class)
                                ->native(false),

                            Location::make('legal_residence')
                                ->city()
                                ->address()
                                ->environment(false),

                            Checkbox::make('same_as_legal_residence')
                                ->label(__('field.same_as_legal_residence'))
                                ->live()
                                ->afterStateUpdated(function (bool $state, Set $set) {
                                    if ($state) {
                                        $set('effective_residence_county_id', null);
                                        $set('effective_residence_city_id', null);
                                        $set('effective_residence_address', null);
                                        $set('effective_residence_environment', null);
                                    }
                                })
                                ->columnSpanFull(),

                            Location::make('effective_residence')
                                ->city()
                                ->address()
                                ->hidden(function (Get $get) {
                                    return $get('same_as_legal_residence');
                                }),

                            Textarea::make('observations')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                                ->placeholder(__('beneficiary.placeholder.partner_relevant_observations'))
                                ->maxLength(500),
                        ]),
                ]),
        ];
    }
}
