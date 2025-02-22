<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToDetailedEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\TableRepeater;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Awcodes\TableRepeater\Header;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditDetailedEvaluation extends EditRecord
{
    use RedirectToDetailedEvaluation;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_evaluation_details.title');
    }

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_detailed_evaluation');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.detailed_evaluation.label'));
    }

    public static function getSchema(): array
    {
        return [
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    TableRepeater::make('detailedEvaluationSpecialists')
                        ->relationship('detailedEvaluationSpecialists')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.specialists'))
                        ->defaultItems(3)
                        ->addActionLabel(__('beneficiary.action.add_row'))
                        ->showLabels(false)
                        ->deletable()
                        ->headers([
                            Header::make('full_name')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.full_name')),

                            Header::make('institution')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.institution')),

                            Header::make('relationship')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.relationship')),

                            Header::make('date')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.contact_date')),
                        ])
                        ->schema([
                            TextInput::make('full_name')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.full_name'))
                                ->maxLength(50),

                            TextInput::make('institution')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.institution'))
                                ->maxLength(50)
                                ->default(fn () => Filament::getTenant()->institution->name),

                            TextInput::make('relationship')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.relationship'))
                                ->maxLength(50),

                            DatePicker::make('date')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.contact_date')),
                        ]),

                    Repeater::make('meetings')
                        ->relationship('meetings')
                        ->columns()
                        ->addActionLabel(__('beneficiary.action.add_meet_row'))
                        ->label(__('beneficiary.section.detailed_evaluation.labels.meetings'))
                        ->schema([
                            TextInput::make('specialist')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.specialist'))
                                ->placeholder(__('beneficiary.placeholder.full_name'))
                                ->maxLength(50)
                                ->required(),

                            DatePicker::make('date')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.date'))
                                ->placeholder(__('beneficiary.placeholder.date'))
                                ->required(),

                            TextInput::make('location')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.location'))
                                ->placeholder(__('beneficiary.placeholder.meet_location'))
                                ->maxLength(50),

                            TextInput::make('observations')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                                ->placeholder(__('beneficiary.placeholder.relevant_details'))
                                ->maxLength(200),
                        ]),
                ]),
        ];
    }
}
