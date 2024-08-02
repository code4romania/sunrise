<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToInitialEvaluation;
use App\Enums\AggravatingFactorsSchema;
use App\Enums\Helps;
use App\Enums\RiskFactorsSchema;
use App\Enums\Ternary;
use App\Enums\VictimPerceptionOfTheRiskSchema;
use App\Enums\ViolenceHistorySchema;
use App\Enums\ViolencesTypesSchema;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Infolists\Components\EnumEntry;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditRiskFactors extends EditRecord
{
    use RedirectToInitialEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_risk_factors.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbsForInitialEvaluation();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.risk_factors.label'));
    }

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->relationship('riskFactors')
                ->schema([
                    Section::make(__('beneficiary.section.initial_evaluation.heading.violence_history'))
                        ->schema(self::getViolenceHistorySchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.violences_types'))
                        ->schema(self::getViolencesTypesSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.risk_factors'))
                        ->schema(self::getRiskFactorsSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.victim_perception_of_the_risk'))
                        ->schema(self::getVictimPerceptionOfTheRiskSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.aggravating_factors'))
                        ->schema(self::getAggravatingFactorsSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.social_support'))
                        ->columns()
                        ->schema(self::getSocialSupportSchema()),
                ]),
        ];
    }

    public static function getViolenceHistorySchema(): array
    {
        $enumOptions = ViolenceHistorySchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getViolencesTypesSchema(): array
    {
        $enumOptions = ViolencesTypesSchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getRiskFactorsSchema(): array
    {
        $enumOptions = RiskFactorsSchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getVictimPerceptionOfTheRiskSchema(): array
    {
        $enumOptions = VictimPerceptionOfTheRiskSchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getAggravatingFactorsSchema(): array
    {
        $enumOptions = AggravatingFactorsSchema::options();

        return self::getSchemaFromEnum($enumOptions);
    }

    public static function getSocialSupportSchema(): array
    {
        return [
            Select::make('extended_family_can_provide')
                ->label(__('beneficiary.section.initial_evaluation.labels.extended_family_can_provide'))
                ->multiple()
                ->options(Helps::options()),
            Select::make('friends_can_provide')
                ->label(__('beneficiary.section.initial_evaluation.labels.friends_can_provide'))
                ->multiple()
                ->options(Helps::options()),
        ];
    }

    public static function getInfoListSchema(): array
    {
        return [
            InfolistGroup::make()
                ->relationship('riskFactors')
                ->schema([
                    InfolistSection::make(fn (Beneficiary $record) => self::getViolenceHeading($record))
                        ->collapsed()
                        ->schema(self::getViolenceHistoryInfolistSchema()),
                    InfolistSection::make(fn (Beneficiary $record) => self::getViolencesTypesHeading($record))
                        ->collapsed()
                        ->schema(self::getViolencesTypesInfolistSchema()),
                    InfolistSection::make(fn (Beneficiary $record) => self::getRiskFactorsHeading($record))
                        ->collapsed()
                        ->schema(self::getRiskFactorsInfolistSchema()),
                    InfolistSection::make(fn (Beneficiary $record) => self::getVictimPerceptionOfTherRiskHeading($record))
                        ->collapsed()
                        ->schema(self::getVictimPerceptionOfTheRiskInfolistSchema()),
                    InfolistSection::make(fn (Beneficiary $record) => self::getAggravatingFactorsHeading($record))
                        ->collapsed()
                        ->schema(self::getAggravatingFactorsInfolistSchema()),
                    InfolistSection::make(__('beneficiary.section.initial_evaluation.heading.social_support'))
                        ->collapsed()
                        ->columns()
                        ->schema(self::getSocialSupportInfolistSchema()),
                ]),
        ];
    }

    public static function getViolenceHistoryInfolistSchema(): array
    {
        $enumOptions = ViolenceHistorySchema::options();

        return self::getInfolistSchemaFromEnum($enumOptions);
    }

    public static function getViolencesTypesInfolistSchema(): array
    {
        $enumOptions = ViolencesTypesSchema::options();

        return self::getInfolistSchemaFromEnum($enumOptions);
    }

    public static function getRiskFactorsInfolistSchema(): array
    {
        $enumOptions = RiskFactorsSchema::options();

        return self::getInfolistSchemaFromEnum($enumOptions);
    }

    public static function getVictimPerceptionOfTheRiskInfolistSchema(): array
    {
        $enumOptions = VictimPerceptionOfTheRiskSchema::options();

        return self::getInfolistSchemaFromEnum($enumOptions);
    }

    public static function getAggravatingFactorsInfolistSchema(): array
    {
        $enumOptions = AggravatingFactorsSchema::options();

        return self::getInfolistSchemaFromEnum($enumOptions);
    }

    public static function getSocialSupportInfolistSchema(): array
    {
        return [
            EnumEntry::make('extended_family_can_provide')
                ->label(__('beneficiary.section.initial_evaluation.labels.extended_family_can_provide'))
                ->badge(),
            EnumEntry::make('friends_can_provide')
                ->label(__('beneficiary.section.initial_evaluation.labels.friends_can_provide'))
                ->badge(),
        ];
    }

    private static function getViolenceHeading(Beneficiary $record): string
    {
        $fields = ViolenceHistorySchema::values();

        $trueAnswers = self::getTrueAnswers($record->riskFactors->risk_factors, $fields);

        return __('beneficiary.section.initial_evaluation.heading.violence_history') . ' ' .
            __('general.true_answers', ['total_answers' => \count($fields), 'true_answers' => $trueAnswers]);
    }

    private static function getViolencesTypesHeading(Beneficiary $record): string
    {
        $fields = ViolencesTypesSchema::values();
        $trueAnswers = self::getTrueAnswers($record->riskFactors->risk_factors, $fields);

        return __('beneficiary.section.initial_evaluation.heading.violences_types') . ' ' .
            __('general.true_answers', ['total_answers' => \count($fields), 'true_answers' => $trueAnswers]);
    }

    private static function getRiskFactorsHeading(Beneficiary $record): string
    {
        $fields = RiskFactorsSchema::values();
        $trueAnswers = self::getTrueAnswers($record->riskFactors->risk_factors, $fields);

        return __('beneficiary.section.initial_evaluation.heading.risk_factors') . ' ' .
            __('general.true_answers', ['total_answers' => \count($fields), 'true_answers' => $trueAnswers]);
    }

    private static function getVictimPerceptionOfTherRiskHeading(Beneficiary $record): string
    {
        $fields = VictimPerceptionOfTheRiskSchema::values();
        $trueAnswers = self::getTrueAnswers($record->riskFactors->risk_factors, $fields);

        return __('beneficiary.section.initial_evaluation.heading.victim_perception_of_the_risk') . ' ' .
            __('general.true_answers', ['total_answers' => \count($fields), 'true_answers' => $trueAnswers]);
    }

    private static function getAggravatingFactorsHeading(Beneficiary $record): string
    {
        $fields = AggravatingFactorsSchema::values();
        $trueAnswers = self::getTrueAnswers($record->riskFactors->risk_factors, $fields);

        return __('beneficiary.section.initial_evaluation.heading.aggravating_factors') . ' ' .
            __('general.true_answers', ['total_answers' => \count($fields), 'true_answers' => $trueAnswers]);
    }

    private static function getTrueAnswers(array $riskFactors, array $fields): int
    {
        $count = 0;
        foreach ($fields as $field) {
            if (empty($riskFactors[$field]['value'])) {
                continue;
            }

            if (Ternary::isYes($riskFactors[$field]['value'])) {
                $count++;
            }
        }

        return $count;
    }

    public static function getSchemaFromEnum(array $enumOptions): array
    {
        $fields = [];
        foreach ($enumOptions as $key => $value) {
            $fields[] = Radio::make('risk_factors.' . $key . '.value')
                ->label($value)
                ->inline()
                ->inlineLabel(false)
                ->options(Ternary::options())
                ->enum(Ternary::class);
            $fields[] = TextInput::make('risk_factors.' . $key . '.description')
                ->hiddenLabel()
                ->placeholder(__('beneficiary.placeholder.observations'))
                ->maxLength(100);
        }

        return $fields;
    }

    public static function getInfolistSchemaFromEnum(array $enumOptions): array
    {
        $fields = [];
        foreach ($enumOptions as $key => $value) {
            $fields[] = TextEntry::make('risk_factors.' . $key . '.value')
                ->label($value)
                ->formatStateUsing(function ($record, $state) use ($key) {
                    $result = \is_int($state)
                        ? Ternary::tryFrom($state)?->label()
                        : null;

                    $result ??= '-';

                    $description = data_get($record->riskFactors->risk_factors, "{$key}.description");

                    if (filled($description)) {
                        $result .= " ({$description})";
                    }

                    return $result;
                })
                ->inlineLabel(false);
        }

        return $fields;
    }
}
