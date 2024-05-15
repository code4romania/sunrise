<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;

class EditBeneficiarySituation extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Group::make([
                TextInput::make('moment_of_evaluation')
                    ->label(__('beneficiary.section.initial_evaluation.labels.moment_of_evaluation'))
                    ->placeholder(__('beneficiary.placeholder.moment_of_evaluation'))
                    ->maxLength(100),
                MarkdownEditor::make('description_of_situation')
                    ->label(__('beneficiary.section.initial_evaluation.labels.description_of_situation'))
                    ->placeholder(__('beneficiary.placeholder.description_of_situation'))
                    ->maxLength(5000),
            ])
                ->relationship('beneficiarySituation'),
        ];
    }

    public static function getInfoListSchema(): array
    {
        return [
            InfolistGroup::make([
                TextEntry::make('moment_of_evaluation')
                    ->label(__('beneficiary.section.initial_evaluation.labels.moment_of_evaluation'))
                    ->placeholder(__('beneficiary.placeholder.moment_of_evaluation')),
                TextEntry::make('description_of_situation')
                    ->label(__('beneficiary.section.initial_evaluation.labels.description_of_situation'))
                    ->placeholder(__('beneficiary.placeholder.description_of_situation')),
            ])
                ->relationship('beneficiarySituation'),
        ];
    }
}
