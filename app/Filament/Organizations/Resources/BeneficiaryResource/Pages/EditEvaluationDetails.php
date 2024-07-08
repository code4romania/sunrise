<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToInitialEvaluation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\User;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditEvaluationDetails extends EditRecord
{
    use RedirectToInitialEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getBreadcrumbsForInitialEvaluation();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.details.label'));
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->columns()
                ->relationship('evaluateDetails')
                ->schema([
                    DatePicker::make('registered_date')
                        ->label(__('beneficiary.section.initial_evaluation.labels.registered_date'))
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
            InfolistGroup::make()
                ->columns()
                ->relationship('evaluateDetails')
                ->schema([
                    TextEntry::make('registered_date')
                        ->label(__('beneficiary.section.initial_evaluation.labels.registered_date')),
                    TextEntry::make('file_number')
                        ->label(__('beneficiary.section.initial_evaluation.labels.file_number'))
                        ->placeholder(__('beneficiary.placeholder.file_number')),
                    TextEntry::make('specialist_id')
                        ->label(__('beneficiary.section.initial_evaluation.labels.specialist'))
                        ->placeholder(__('beneficiary.placeholder.specialist'))
                        ->formatStateUsing(function ($state) {
                            $user = User::find($state)->first();

                            return $user->first_name . ' ' . $user->last_name;
                        }),
                    TextEntry::make('method_of_identifying_the_service')
                        ->label(__('beneficiary.section.initial_evaluation.labels.method_of_identifying_the_service'))
                        ->placeholder(__('beneficiary.placeholder.method_of_identifying_the_service'))
                        ->columnSpanFull(),
                ]),
        ];
    }
}
