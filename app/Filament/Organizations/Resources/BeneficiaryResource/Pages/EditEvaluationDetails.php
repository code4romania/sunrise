<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Organization;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditEvaluationDetails extends EditRecord
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
                DatePicker::make('registered_date')
                    ->label(__('beneficiary.section.initial_evaluation.labels.registered_date'))
                    ->required(),
                TextInput::make('file_number')
                    ->label(__('beneficiary.section.initial_evaluation.labels.file_number'))
                    ->placeholder(__('beneficiary.placeholder.file_number')),
                Select::make('specialist_id')
                    ->label(__('beneficiary.section.initial_evaluation.labels.specialist'))
                    ->placeholder(__('beneficiary.placeholder.specialist'))
                    ->required()
                    ->default(auth()->user()->id)
                    ->options(
                        fn ($record) => Organization::find(Filament::getTenant()->id)
                            ->with('users')
                            ->first()
                            ->users
                            ->map(fn ($item) => [
                                'full_name' => $item->first_name . ' ' . $item->last_name,
                                'id' => $item->id,
                            ])
                            ->pluck('full_name', 'id')
                    ),
                Textarea::make('method_of_identifying_the_service')
                    ->label(__('beneficiary.section.initial_evaluation.labels.method_of_identifying_the_service'))
                    ->placeholder(__('beneficiary.placeholder.method_of_identifying_the_service'))
                    ->columnSpanFull(),
            ])
                ->columns()
                ->relationship('evaluateDetails'),
        ];
    }
}
