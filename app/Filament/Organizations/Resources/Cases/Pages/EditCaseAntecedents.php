<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToPersonalInformation;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditCaseAntecedents extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToPersonalInformation;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_antecedents.title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_personal_information', ['record' => $record]) => __('beneficiary.page.personal_information.title'),
            '' => __('beneficiary.page.edit_antecedents.title'),
        ];
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.antecedents'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema(self::antecedentsSection()),
        ]);
    }

    /**
     * @return array<int, Group>
     */
    protected static function antecedentsSection(): array
    {
        return [
            Group::make()
                ->maxWidth('3xl')
                ->relationship('antecedents')
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
                                ->visible(fn (Get $get): bool => Ternary::isYes($get('has_police_reports')))
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
                                ->visible(fn (Get $get): bool => Ternary::isYes($get('has_medical_reports')))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(999),
                        ]),

                    TextInput::make('observations')
                        ->label(__('field.antecedents_observations'))
                        ->placeholder(__('placeholder.observations'))
                        ->maxLength(1000),
                ]),
        ];
    }
}
