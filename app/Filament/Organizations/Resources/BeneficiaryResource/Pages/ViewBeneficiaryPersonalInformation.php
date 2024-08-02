<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\EnumEntry;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiaryPersonalInformation extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.personal_information.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getPersonalInformationBreadcrumbs();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns()
            ->schema(static::getPersonalInformationFormSchema());
    }

    public static function getPersonalInformationFormSchema(): array
    {
        return [
            Tabs::make()
                ->columnSpanFull()
                ->maxWidth('3xl')
                ->tabs([
                    Tab::make(__('beneficiary.section.personal_information.section.beneficiary'))
                        ->columns()
                        ->schema(static::beneficiarySection()),

                    Tab::make(__('beneficiary.section.personal_information.section.aggressor'))
                        ->schema(static::aggressorSection()),

                    Tab::make(__('beneficiary.section.personal_information.section.antecedents'))
                        ->columns()
                        ->schema(static::antecedentsSection()),

                    Tab::make(__('beneficiary.section.personal_information.section.flow'))
                        ->columns()
                        ->schema(static::flowSection()),
                ]),
        ];
    }

    protected static function beneficiarySection(): array
    {
        return [
            Section::make(__('beneficiary.section.personal_information.section.beneficiary'))
                ->columns(2)
                ->headerActions([
                    BeneficiaryResource\Actions\EditPersonalInformation::make('edit'),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    EnumEntry::make('has_family_doctor')
                        ->label(__('field.has_family_doctor'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('family_doctor_name')
                        ->label(__('field.family_doctor_name'))
                        ->placeholder(__('placeholder.name')),

                    TextEntry::make('family_doctor_contact')
                        ->label(__('field.family_doctor_contact'))
                        ->placeholder(__('placeholder.phone_or_email')),

                    Grid::make()
                        ->schema([
                            EnumEntry::make('psychiatric_history')
                                ->label(__('field.psychiatric_history'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('psychiatric_notes')
                                ->label(__('field.psychiatric_notes')),
                        ]),

                    Grid::make()
                        ->schema([
                            EnumEntry::make('criminal_history')
                                ->label(__('field.criminal_history'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('criminal_notes')
                                ->label(__('field.criminal_notes')),
                        ]),

                    EnumEntry::make('studies')
                        ->label(__('field.studies'))
                        ->placeholder(__('placeholder.studies')),

                    EnumEntry::make('occupation')
                        ->label(__('field.occupation'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('workplace')
                        ->label(__('field.workplace'))
                        ->placeholder(__('placeholder.workplace'))
                        ->columnSpanFull(),

                    EnumEntry::make('income')
                        ->label(__('field.income'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('elder_care_count')
                        ->label(__('field.elder_care_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric(),

                    EnumEntry::make('homeownership')
                        ->label(__('field.homeownership'))
                        ->placeholder(__('placeholder.select_one')),
                ]),
        ];
    }

    protected static function aggressorSection(): array
    {
        return [
            Section::make(__('beneficiary.section.personal_information.section.aggressor'))
                ->columns(2)
                ->headerActions([
                    BeneficiaryResource\Actions\EditPersonalInformation::make('edit'),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    RepeatableEntry::make('aggressor')
                        ->columns()
                        ->columnSpanFull()
                        ->hiddenLabel()
                        ->schema([
                            EnumEntry::make('relationship')
                                ->label(__('field.aggressor_relationship'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('age')
                                ->label(__('field.aggressor_age'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),

                            EnumEntry::make('gender')
                                ->label(__('field.aggressor_gender'))
                                ->placeholder(__('placeholder.select_one')),

                            EnumEntry::make('citizenship')
                                ->label(__('field.aggressor_citizenship'))
                                ->placeholder(__('placeholder.citizenship')),

                            EnumEntry::make('civil_status')
                                ->label(__('field.aggressor_civil_status'))
                                ->placeholder(__('placeholder.civil_status')),

                            EnumEntry::make('studies')
                                ->label(__('field.aggressor_studies'))
                                ->placeholder(__('placeholder.studies')),

                            EnumEntry::make('occupation')
                                ->label(__('field.aggressor_occupation'))
                                ->placeholder(__('placeholder.select_one')),

                            Grid::make()
                                ->schema([
                                    EnumEntry::make('has_violence_history')
                                        ->label(__('field.aggressor_has_violence_history'))
                                        ->placeholder(__('placeholder.select_one')),

                                    TextEntry::make('violence_types_string')
                                        ->label(__('field.aggressor_violence_types'))
                                        ->default(
                                            fn ($record) => $record->violence_types
                                                ?->map(fn ($item) => $item->label())
                                                ->join(', ')
                                        ),

                                ]),

                            Grid::make()
                                ->schema([
                                    EnumEntry::make('has_psychiatric_history')
                                        ->label(__('field.aggressor_has_psychiatric_history'))
                                        ->placeholder(__('placeholder.select_one')),

                                    TextEntry::make('psychiatric_history_notes')
                                        ->label(__('field.aggressor_psychiatric_history_notes')),
                                ]),

                            Grid::make()
                                ->schema([
                                    EnumEntry::make('has_drug_history')
                                        ->label(__('field.aggressor_has_drug_history'))
                                        ->placeholder(__('placeholder.select_one')),

                                    TextEntry::make('drugs_string')
                                        ->label(__('field.aggressor_drugs'))
                                        ->default(
                                            fn ($record) => $record->drugs
                                                ?->map(fn ($item) => $item->label())
                                                ->join(', ') ?? '-'
                                        ),
                                ]),

                            Grid::make()
                                ->schema([
                                    TextEntry::make('legal_history_string')
                                        ->label(__('field.aggressor_legal_history'))
                                        ->default(
                                            fn ($record) => $record->legal_history
                                                ?->map(fn ($item) => $item->label())
                                                ->join(', ') ?? '-'
                                        ),
                                ]),

                            Grid::make()
                                ->schema([
                                    EnumEntry::make('has_protection_order')
                                        ->label(__('field.has_protection_order'))
                                        ->placeholder(__('placeholder.select_one')),

                                    TextEntry::make('protection_order_notes')
                                        ->label(__('field.protection_order_notes')),
                                ]),
                        ]),
                ]),
        ];
    }

    protected static function antecedentsSection(): array
    {
        return [
            Section::make(__('beneficiary.section.personal_information.section.antecedents'))
                ->columns(2)
                ->headerActions([
                    BeneficiaryResource\Actions\EditPersonalInformation::make('edit'),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    Grid::make()
                        ->schema([
                            EnumEntry::make('has_police_reports')
                                ->label(__('field.has_police_reports'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('police_report_count')
                                ->label(__('field.police_report_count'))
                                ->placeholder(__('placeholder.number')),
                        ]),

                    Grid::make()
                        ->schema([
                            EnumEntry::make('has_medical_reports')
                                ->label(__('field.has_medical_reports'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('medical_report_count')
                                ->label(__('field.medical_report_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),
                        ]),
                ]),
        ];
    }

    protected static function flowSection(): array
    {
        return [
            Section::make(__('beneficiary.section.personal_information.section.flow'))
                ->columns(2)
                ->headerActions([
                    BeneficiaryResource\Actions\EditPersonalInformation::make('edit'),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    Grid::make()
                        ->schema([
                            EnumEntry::make('presentation_mode')
                                ->label(__('field.presentation_mode'))
                                ->placeholder(__('placeholder.select_one')),

                            EnumEntry::make('referring_institution_id')
                                ->label(__('field.referring_institution'))
                                ->placeholder(__('placeholder.select_one')),

                            EnumEntry::make('referral_mode')
                                ->label(__('field.referral_mode'))
                                ->placeholder(__('placeholder.select_one')),
                        ]),

                    EnumEntry::make('notifier')
                        ->label(__('field.notifier'))
                        ->placeholder(__('placeholder.select_one')),

                    EnumEntry::make('notification_mode')
                        ->label(__('field.notification_mode'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('notifier_other')
                        ->label(__('field.notifier_other')),

                    TextEntry::make('act_location_string')
                        ->label(__('field.act_location'))
                        ->default(
                            fn ($record) => $record->act_location
                                ?->map(fn ($item) => $item->label())
                                ->join(', ') ?? '-'
                        ),

                    TextEntry::make('act_location_other')
                        ->label(__('field.act_location_other')),

                    TextEntry::make('firstCalledInstitution.name')
                        ->label(__('field.first_called_institution'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('otherCalledInstitution_string')
                        ->label(__('field.other_called_institutions'))
                        ->default(
                            fn ($record) => $record->otherCalledInstitution
                                ?->map(fn ($item) => $item->name)
                                ->join(', ') ?? '-'
                        ),
                ]),
        ];
    }
}
