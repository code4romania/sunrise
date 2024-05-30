<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditDetailedEvaluation extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbsForDetailedEvaluation();
    }

    public static function getSchema(): array
    {
        return [
            TableRepeater::make('specialists')
                ->columnSpan('full')
                ->relationship('specialists')
                ->label(__('beneficiary.section.detailed_evaluation.labels.specialists'))
                ->defaultItems(3)
                ->addActionLabel(__('beneficiary.action.add_row'))
                ->showLabels(false)
                ->deletable()
                ->schema([
                    TextInput::make('full_name')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.full_name'))
                        ->maxLength(50),

                    TextInput::make('institution')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.institution'))
                        ->maxLength(50)
                        ->default(fn () => Filament::getTenant()->name),

                    TextInput::make('relationship')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.relationship'))
                        ->maxLength(50),

                    DatePicker::make('date')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.contact_date')),
                ]),

            Repeater::make('meetings')
                ->relationship('meetings')
                ->columnSpan(1)
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
        ];
    }
}
