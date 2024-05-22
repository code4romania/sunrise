<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Resources\Pages\EditRecord;

class EditRequestedServices extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(
            self::getSchema()
        );
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->relationship('requestedServices')
                ->schema([
                    Section::make(__('beneficiary.section.initial_evaluation.heading.types_of_requested_services'))
                        ->schema(EditDetailedEvaluationResult::getRecommendationServicesSchema()),
                ]),
        ];
    }

    public static function getInfoListSchema(): array
    {
        return [
            InfolistGroup::make()
                ->relationship('requestedServices')
                ->schema([
                    InfolistSection::make(__('beneficiary.section.initial_evaluation.heading.types_of_requested_services'))
                        ->schema(EditDetailedEvaluationResult::getRecommendationServicesInfolistSchema()),
                ]),
        ];
    }
}
