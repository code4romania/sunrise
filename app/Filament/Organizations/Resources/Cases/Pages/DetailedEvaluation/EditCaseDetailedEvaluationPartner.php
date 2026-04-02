<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Enums\Occupation;
use App\Filament\Organizations\Concerns\InteractsWithBeneficiaryDetailsPanel;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\CountyCitySelect;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditCaseDetailedEvaluationPartner extends EditRecord
{
    use InteractsWithBeneficiaryDetailsPanel;
    use PreventSubmitFormOnEnter;
    use SavesPartnerAddresses;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.wizard.partner.label');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record->getBreadcrumb(),
            CaseResource::getUrl('view_detailed_evaluation', ['record' => $record]) => __('beneficiary.breadcrumb.wizard_detailed_evaluation'),
            '' => __('beneficiary.wizard.partner.label'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_detailed_evaluation', ['record' => $this->getRecord()])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return CaseResource::getUrl('view_detailed_evaluation', ['record' => $this->getRecord()]).'?tab='.\Illuminate\Support\Str::slug(__('beneficiary.wizard.partner.label'));
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->mutateFormDataBeforeFillPartner($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->capturePartnerAddressDataBeforeSave($data);
    }

    public function afterSave(): void
    {
        $this->afterSavePartnerAddresses();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                    ->relationship('partner')
                    ->maxWidth('3xl')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('last_name')
                                    ->label(__('field.last_name'))
                                    ->placeholder(__('beneficiary.placeholder.partner_last_name'))
                                    ->maxLength(50),
                                TextInput::make('first_name')
                                    ->label(__('field.first_name'))
                                    ->placeholder(__('beneficiary.placeholder.partner_first_name'))
                                    ->maxLength(50),
                                TextInput::make('age')
                                    ->label(__('field.age'))
                                    ->placeholder(__('beneficiary.placeholder.partner_age'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99)
                                    ->validationAttribute(__('field.age')),
                                Select::make('occupation')
                                    ->label(__('field.occupation'))
                                    ->placeholder(__('beneficiary.placeholder.occupation'))
                                    ->options(Occupation::options())
                                    ->enum(Occupation::class),
                            ]),
                        Grid::make()
                            ->schema([
                                ...CountyCitySelect::make()
                                    ->countyField('legal_residence.county_id')
                                    ->cityField('legal_residence.city_id')
                                    ->countyLabel(__('field.legal_residence_county'))
                                    ->cityLabel(__('field.legal_residence_city'))
                                    ->countyPlaceholder(__('placeholder.county'))
                                    ->cityPlaceholder(__('placeholder.city'))
                                    ->required(false)
                                    ->countyAfterStateUpdated(function (Set $set, Get $get): void {
                                        if ($get('same_as_legal_residence')) {
                                            $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                            $set('effective_residence.city_id', null);
                                        }
                                    })
                                    ->cityAfterStateUpdated(function (Set $set, Get $get, $state): void {
                                        if ($get('same_as_legal_residence')) {
                                            $set('effective_residence.city_id', $state);
                                        }
                                    })
                                    ->schema(),
                                TextInput::make('legal_residence.address')
                                    ->label(__('field.legal_residence_address'))
                                    ->placeholder(__('placeholder.address'))
                                    ->maxLength(50),
                            ]),
                        Checkbox::make('same_as_legal_residence')
                            ->label(__('field.same_as_legal_residence'))
                            ->live()
                            ->afterStateUpdated(function (bool $state, Set $set, Get $get): void {
                                if (! $state) {
                                    $set('effective_residence.county_id', null);
                                    $set('effective_residence.city_id', null);
                                    $set('effective_residence.address', null);
                                }
                                if ($state) {
                                    $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                    $set('effective_residence.city_id', $get('legal_residence.city_id'));
                                    $set('effective_residence.address', $get('legal_residence.address'));
                                }
                            })
                            ->columnSpanFull(),
                        Grid::make()
                            ->schema([
                                ...CountyCitySelect::make()
                                    ->countyField('effective_residence.county_id')
                                    ->cityField('effective_residence.city_id')
                                    ->countyLabel(__('field.effective_residence_county'))
                                    ->cityLabel(__('field.effective_residence_city'))
                                    ->countyPlaceholder(__('placeholder.county'))
                                    ->cityPlaceholder(__('placeholder.city'))
                                    ->required(false)
                                    ->countyDisabled(fn (Get $get): bool => (bool) $get('same_as_legal_residence'))
                                    ->cityDisabled(fn (Get $get): bool => $get('same_as_legal_residence') || ! $get('effective_residence.county_id'))
                                    ->schema(),
                                TextInput::make('effective_residence.address')
                                    ->label(__('field.effective_residence_address'))
                                    ->placeholder(__('placeholder.address'))
                                    ->maxLength(50)
                                    ->disabled(fn (Get $get): bool => (bool) $get('same_as_legal_residence')),
                            ]),
                        Textarea::make('observations')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                            ->placeholder(__('beneficiary.placeholder.partner_relevant_observations'))
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
