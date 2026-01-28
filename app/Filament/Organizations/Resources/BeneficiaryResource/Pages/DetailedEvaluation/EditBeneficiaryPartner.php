<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToDetailedEvaluation;
use App\Enums\AddressType;
use App\Enums\Occupation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Location;
use App\Forms\Components\Select;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditBeneficiaryPartner extends EditRecord
{
    use RedirectToDetailedEvaluation;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_beneficiary_partner.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_detailed_evaluation');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.partner.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                ->relationship('partner')
                ->maxWidth('3xl')
                ->columns()
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
                        ->enum(Occupation::class),

                    Location::make(AddressType::LEGAL_RESIDENCE->value)
                        ->relationship(AddressType::LEGAL_RESIDENCE->value)
                        ->city()
                        ->address()
                        ->addressMaxLength(50)
                        ->copyDataInPath(
                            fn (Get $get) => $get('same_as_legal_residence') ?
                                AddressType::EFFECTIVE_RESIDENCE->value :
                                null
                        ),

                    Checkbox::make('same_as_legal_residence')
                        ->label(__('field.same_as_legal_residence'))
                        ->live()
                        ->afterStateUpdated(function (bool $state, Set $set, Get $get) {
                            if (! $state) {
                                $set('effective_residence.county_id', null);
                                $set('effective_residence.city_id', null);
                                $set('effective_residence.address', null);
                                $set('effective_residence.environment', null);
                            }

                            if ($state) {
                                $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                $set('effective_residence.city_id', $get('legal_residence.city_id'));
                                $set('effective_residence.address', $get('legal_residence.address'));
                                $set('effective_residence.environment', $get('legal_residence.environment'));
                            }
                        })
                        ->columnSpanFull(),

                    Location::make(AddressType::EFFECTIVE_RESIDENCE->value)
                        ->relationship(AddressType::EFFECTIVE_RESIDENCE->value)
                        ->city()
                        ->address()
                        ->addressMaxLength(50)
                        ->disabled(function (Get $get) {
                            return $get('same_as_legal_residence');
                        }),

                    Textarea::make('observations')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                        ->placeholder(__('beneficiary.placeholder.partner_relevant_observations'))
                        ->maxLength(500),

                ]),
        ];
    }
}
