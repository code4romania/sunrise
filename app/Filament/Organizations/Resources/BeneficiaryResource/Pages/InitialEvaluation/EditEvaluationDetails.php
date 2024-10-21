<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation;

use App\Concerns\RedirectToInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Models\User;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditEvaluationDetails extends EditRecord
{
    use RedirectToInitialEvaluation;

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
            ->getBreadcrumbsForInitialEvaluation();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.details.label'));
    }

    public static function getSchema(): array
    {
        return [
            Section::make()
                ->relationship('evaluateDetails')
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    DatePicker::make('registered_date')
                        ->label(__('beneficiary.section.initial_evaluation.labels.registered_date'))
                        ->native(false)
                        ->required(),

                    TextInput::make('file_number')
                        ->label(__('beneficiary.section.initial_evaluation.labels.file_number'))
                        ->placeholder(__('beneficiary.placeholder.file_number'))
                        ->maxLength(50),

                    Select::make('specialist_id')
                        ->label(__('beneficiary.section.initial_evaluation.labels.specialist'))
                        ->placeholder(__('beneficiary.placeholder.specialist'))
                        ->required()
                        ->default(auth()->user()->id)
                        ->options(fn ($record) => User::getTenantOrganizationUsers()),

                    Textarea::make('method_of_identifying_the_service')
                        ->label(__('beneficiary.section.initial_evaluation.labels.method_of_identifying_the_service'))
                        ->placeholder(__('beneficiary.placeholder.method_of_identifying_the_service'))
                        ->columnSpanFull()
                        ->maxLength(2000),
                ]),

        ];
    }

    public static function getInfoListSchema(): array
    {
        return [
            Group::make()
                ->columns()
                ->relationship('evaluateDetails')
                ->schema([
                    TextEntry::make('registered_date')
                        ->label(__('beneficiary.section.initial_evaluation.labels.registered_date')),

                    TextEntry::make('file_number')
                        ->label(__('beneficiary.section.initial_evaluation.labels.file_number'))
                        ->placeholder(__('beneficiary.placeholder.file_number')),

                    TextEntry::make('specialist.full_name')
                        ->label(__('beneficiary.section.initial_evaluation.labels.specialist'))
                        ->placeholder(__('beneficiary.placeholder.specialist')),

                    TextEntry::make('method_of_identifying_the_service')
                        ->label(__('beneficiary.section.initial_evaluation.labels.method_of_identifying_the_service'))
                        ->placeholder(__('beneficiary.placeholder.method_of_identifying_the_service'))
                        ->columnSpanFull(),
                ]),
        ];
    }
}
