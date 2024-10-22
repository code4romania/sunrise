<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation;

use App\Concerns\RedirectToInitialEvaluation;
use App\Enums\Frequency;
use App\Enums\Violence;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Infolists\Components\EnumEntry;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditViolence extends EditRecord
{
    use RedirectToInitialEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_violence.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbsForInitialEvaluation();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.violence.label'));
    }

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Section::make()
                ->relationship('violence')
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    Select::make('violence_types')
                        ->label(__('beneficiary.section.initial_evaluation.labels.violence_type'))
                        ->placeholder(__('beneficiary.placeholder.violence_type'))
                        ->options(Violence::options())
                        ->multiple()
                        ->required(),

                    Select::make('violence_primary_type')
                        ->label(__('beneficiary.section.initial_evaluation.labels.violence_primary_type'))
                        ->placeholder(__('beneficiary.placeholder.violence_primary_type'))
                        ->options(Violence::options())
                        ->required(),

                    Select::make('frequency_violence')
                        ->label(__('beneficiary.section.initial_evaluation.labels.frequency_violence'))
                        ->placeholder(__('beneficiary.placeholder.frequency_violence'))
                        ->options(Frequency::options())
                        ->required(),

                    RichEditor::make('description')
                        ->label(__('beneficiary.section.initial_evaluation.labels.description'))
                        ->placeholder(__('beneficiary.placeholder.description'))
                        ->helperText(__('beneficiary.helper_text.violence_description'))
                        ->columnSpanFull()
                        ->maxLength(5000),
                ]),
        ];
    }

    public static function getInfoListSchema(): array
    {
        return [
            Group::make()
                ->relationship('violence')
                ->columns()
                ->schema([
                    TextEntry::make('violence_types')
                        ->label(__('beneficiary.section.initial_evaluation.labels.violence_type')),

                    EnumEntry::make('violence_primary_type')
                        ->label(__('beneficiary.section.initial_evaluation.labels.violence_primary_type'))
                        ->placeholder(__('beneficiary.placeholder.violence_primary_type')),

                    EnumEntry::make('frequency_violence')
                        ->label(__('beneficiary.section.initial_evaluation.labels.frequency_violence'))
                        ->placeholder(__('beneficiary.placeholder.frequency_violence')),

                    TextEntry::make('description')
                        ->label(__('beneficiary.section.initial_evaluation.labels.description'))
                        ->placeholder(__('beneficiary.placeholder.description'))
                        ->columnSpanFull(),
                ]),

        ];
    }
}
