<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToPersonalInformation;
use App\Enums\ActLocation;
use App\Enums\NotificationMode;
use App\Enums\Notifier;
use App\Enums\PresentationMode;
use App\Enums\ReferralMode;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Rules\MultipleIn;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditCaseFlowPresentation extends EditRecord
{
    use PreventSubmitFormOnEnter;
    use RedirectToPersonalInformation;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_flow_presentation.title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_personal_information', ['record' => $record]) => __('beneficiary.page.personal_information.title'),
            '' => __('beneficiary.page.edit_flow_presentation.title'),
        ];
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.flow'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(static::getPersonalInformationFormSchema());
    }

    public static function getPersonalInformationFormSchema(): array
    {
        return [
            Section::make()
                ->schema(static::flowSection()),
        ];
    }

    public static function flowSection(): array
    {
        return [
            Grid::make(1)
                ->maxWidth('3xl')
                ->relationship('flowPresentation')
                ->schema([
                    Section::make(__('beneficiary.section.flow.presentation_and_referral'))
                        ->compact()
                        ->columnSpanFull()
                        ->columns(2)
                        ->schema([
                            Select::make('presentation_mode')
                                ->label(__('field.presentation_mode'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(PresentationMode::options())
                                ->enum(PresentationMode::class)
                                ->live(),

                            Select::make('referring_institution_id')
                                ->label(__('field.referring_institution'))
                                ->placeholder(__('placeholder.select_one'))
                                ->relationship('referringInstitution', 'name')
                                ->visible(fn (Get $get) => PresentationMode::isValue(
                                    $get('presentation_mode'),
                                    PresentationMode::FORWARDED
                                ))
                                ->nullable(),

                            CheckboxList::make('referral_mode')
                                ->label(__('field.referral_mode'))
                                ->options(ReferralMode::options())
                                ->visible(fn (Get $get) => PresentationMode::isValue(
                                    $get('presentation_mode'),
                                    PresentationMode::FORWARDED
                                ))
                                ->columns(2)
                                ->gridDirection('row')
                                ->columnSpanFull(),
                        ]),

                    Section::make(__('beneficiary.section.flow.notification'))
                        ->compact()
                        ->columnSpanFull()
                        ->columns(2)
                        ->schema([
                            Select::make('notifier')
                                ->label(__('field.notifier'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Notifier::options())
                                ->enum(Notifier::class)
                                ->live(),

                            Select::make('notification_mode')
                                ->label(__('field.notification_mode'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(NotificationMode::options())
                                ->enum(NotificationMode::class),

                            TextInput::make('notifier_other')
                                ->label(__('field.notifier_other'))
                                ->maxLength(100)
                                ->visible(fn (Get $get) => Notifier::isValue(
                                    $get('notifier'),
                                    Notifier::OTHER
                                ))
                                ->columnSpanFull(),
                        ]),

                    Section::make(__('beneficiary.section.flow.act_location'))
                        ->compact()
                        ->columnSpanFull()
                        ->columns(1)
                        ->schema([
                            Select::make('act_location')
                                ->label(__('field.act_location'))
                                ->placeholder(__('beneficiary.section.personal_information.placeholders.select_many'))
                                ->options(ActLocation::options())
                                ->rule(new MultipleIn(ActLocation::values()))
                                ->multiple()
                                ->live(),

                            TextInput::make('act_location_other')
                                ->label(__('field.act_location_other'))
                                ->maxLength(100)
                                ->visible(
                                    fn (Get $get) => collect($get('act_location'))
                                        ->filter(fn ($value) => ActLocation::isValue($value, ActLocation::OTHER))
                                        ->isNotEmpty()
                                ),
                        ]),

                    Section::make(__('beneficiary.section.flow.institutions_called'))
                        ->compact()
                        ->columnSpanFull()
                        ->columns(1)
                        ->schema([
                            Select::make('first_called_institution_id')
                                ->label(__('field.first_called_institution'))
                                ->placeholder(__('placeholder.select_one'))
                                ->relationship('firstCalledInstitution', 'name')
                                ->nullable(),

                            Select::make('other_called_institutions')
                                ->label(__('field.other_called_institutions'))
                                ->placeholder(__('beneficiary.section.personal_information.placeholders.select_many'))
                                ->relationship('otherCalledInstitution', 'name')
                                ->multiple()
                                ->preload()
                                ->nullable(),
                        ]),
                ]),
        ];
    }
}
