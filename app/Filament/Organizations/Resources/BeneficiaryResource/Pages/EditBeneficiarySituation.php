<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditBeneficiarySituation extends EditRecord
{
    use RedirectToInitialEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbsForInitialEvaluation();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.beneficiary_situation.label'));
    }

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->relationship('beneficiarySituation')
                ->schema([
                    TextInput::make('moment_of_evaluation')
                        ->label(__('beneficiary.section.initial_evaluation.labels.moment_of_evaluation'))
                        ->placeholder(__('beneficiary.placeholder.moment_of_evaluation'))
                        ->maxLength(100),
                    RichEditor::make('description_of_situation')
                        ->label(__('beneficiary.section.initial_evaluation.labels.description_of_situation'))
                        ->placeholder(__('beneficiary.placeholder.description_of_situation'))
                        ->maxLength(5000),
                ]),
        ];
    }

    public static function getInfoListSchema(): array
    {
        return [
            InfolistGroup::make()
                ->relationship('beneficiarySituation')
                ->schema([
                    TextEntry::make('moment_of_evaluation')
                        ->label(__('beneficiary.section.initial_evaluation.labels.moment_of_evaluation'))
                        ->placeholder(__('beneficiary.placeholder.moment_of_evaluation')),
                    TextEntry::make('description_of_situation')
                        ->label(__('beneficiary.section.initial_evaluation.labels.description_of_situation'))
                        ->placeholder(__('beneficiary.placeholder.description_of_situation')),
                ]),
        ];
    }
}
