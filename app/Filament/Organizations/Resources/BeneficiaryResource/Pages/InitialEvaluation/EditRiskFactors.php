<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\InitialEvaluation;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToInitialEvaluation;
use App\Enums\AggravatingFactorsSchema;
use App\Enums\RiskFactorsSchema;
use App\Enums\VictimPerceptionOfTheRiskSchema;
use App\Enums\ViolenceHistorySchema;
use App\Enums\ViolencesTypesSchema;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Schemas\BeneficiaryResource\InitialEvaluationSchema;
use App\Infolists\Components\EnumEntry;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditRiskFactors extends EditRecord
{
    use RedirectToInitialEvaluation;
    use PreventSubmitFormOnEnter;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_risk_factors.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_initial_evaluation');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.risk_factors.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(InitialEvaluationSchema::getRiskFactorsFormComponents());
    }

    public static function getInfoListSchema(): array
    {
        return [
            Group::make()
                ->relationship('riskFactors')
                ->schema([
                    Section::make(fn (Beneficiary $record) => self::getViolenceHeading($record))
                        ->collapsed()
                        ->schema(self::getViolenceHistoryInfolistSchema()),
                    Section::make(fn (Beneficiary $record) => self::getViolencesTypesHeading($record))
                        ->collapsed()
                        ->schema(self::getViolencesTypesInfolistSchema()),
                    Section::make(fn (Beneficiary $record) => self::getRiskFactorsHeading($record))
                        ->collapsed()
                        ->schema(self::getRiskFactorsInfolistSchema()),
                    Section::make(fn (Beneficiary $record) => self::getVictimPerceptionOfTherRiskHeading($record))
                        ->collapsed()
                        ->schema(self::getVictimPerceptionOfTheRiskInfolistSchema()),
                    Section::make(fn (Beneficiary $record) => self::getAggravatingFactorsHeading($record))
                        ->collapsed()
                        ->schema(self::getAggravatingFactorsInfolistSchema()),
                    Section::make(__('beneficiary.section.initial_evaluation.heading.social_support'))
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
                ->formatStateUsing(
                    fn (Beneficiary $record, $state) => $record->riskFactors->extended_family_can_not_provide ?
                        __('beneficiary.section.initial_evaluation.labels.extended_family_can_not_provide') :
                        $state
                )
                ->badge(),

            EnumEntry::make('friends_can_provide')
                ->label(__('beneficiary.section.initial_evaluation.labels.friends_can_provide'))
                ->formatStateUsing(
                    fn (Beneficiary $record, $state) => $record->riskFactors->friends_can_not_provide ?
                        __('beneficiary.section.initial_evaluation.labels.friends_can_not_provide') :
                        $state
                )
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
                    $result = $state !== '-'
                        ? Ternary::tryFrom((int) $state)?->label()
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
