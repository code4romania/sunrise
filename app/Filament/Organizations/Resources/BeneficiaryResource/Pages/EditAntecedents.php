<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToPersonalInformation;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditAntecedents extends EditRecord
{
    use RedirectToPersonalInformation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.edit_antecedents.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbs('view_personal_information');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.antecedents'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(static::getPersonalInformationFormSchema());
    }

    public static function getPersonalInformationFormSchema(): array
    {
        return [
            Section::make()
                ->schema(static::antecedentsSection()),
        ];
    }

    public static function antecedentsSection(): array
    {
        return [
            Group::make()
                ->maxWidth('3xl')
                ->schema([
                    Grid::make()
                        ->schema([
                            Select::make('has_police_reports')
                                ->label(__('field.has_police_reports'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            TextInput::make('police_report_count')
                                ->label(__('field.police_report_count'))
                                ->placeholder(__('placeholder.number'))
                                ->visible(fn (Get $get) => Ternary::isYes($get('has_police_reports')))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(999),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('has_medical_reports')
                                ->label(__('field.has_medical_reports'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Ternary::options())
                                ->enum(Ternary::class)
                                ->live(),

                            TextInput::make('medical_report_count')
                                ->label(__('field.medical_report_count'))
                                ->placeholder(__('placeholder.number'))
                                ->visible(fn (Get $get) => Ternary::isYes($get('has_medical_reports')))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(999),
                        ]),
                ]),
        ];
    }
}
