<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Concerns\RedirectToPersonalInformation;
use App\Enums\ActLocation;
use App\Enums\NotificationMode;
use App\Enums\Notifier;
use App\Enums\PresentationMode;
use App\Enums\ReferralMode;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Select;
use App\Rules\MultipleIn;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditFlowPresentation extends EditRecord
{
    use RedirectToPersonalInformation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        // TODO change title after merge #83
        return  __('beneficiary.page.edit_personal_information.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
        ]);
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->record)
            ->getPersonalInformationBreadcrumbs();
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.section.personal_information.section.flow'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(static::getPersonalInformationFormSchema());
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
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
                    Grid::make()
                        ->schema([
                            Select::make('presentation_mode')
                                ->label(__('field.presentation_mode'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(PresentationMode::options())
                                ->enum(PresentationMode::class)
                                ->native(false)
                                ->live(),

                            Select::make('referring_institution_id')
                                ->label(__('field.referring_institution'))
                                ->placeholder(__('placeholder.select_one'))
                                ->relationship('referringInstitution', 'name')
                                ->visible(fn (Get $get) => PresentationMode::isValue(
                                    $get('presentation_mode'),
                                    PresentationMode::FORWARDED
                                ))
                                ->native(false)
                                ->nullable(),

                            Select::make('referral_mode')
                                ->label(__('field.referral_mode'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(ReferralMode::options())
                                ->enum(ReferralMode::class)
                                ->visible(fn (Get $get) => PresentationMode::isValue(
                                    $get('presentation_mode'),
                                    PresentationMode::FORWARDED
                                ))
                                ->native(false)
                                ->nullable(),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('notifier')
                                ->label(__('field.notifier'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(Notifier::options())
                                ->enum(Notifier::class)
                                ->native(false)
                                ->live(),

                            Select::make('notification_mode')
                                ->label(__('field.notification_mode'))
                                ->placeholder(__('placeholder.select_one'))
                                ->options(NotificationMode::options())
                                ->native(false)
                                ->enum(NotificationMode::class),

                            TextInput::make('notifier_other')
                                ->label(__('field.notifier_other'))
                                ->visible(fn (Get $get) => Notifier::isValue(
                                    $get('notifier'),
                                    Notifier::OTHER
                                )),
                        ]),

                    Grid::make()
                        ->schema([
                            Select::make('act_location')
                                ->label(__('field.act_location'))
                                ->placeholder(__('placeholder.select_many'))
                                ->options(ActLocation::options())
                                ->rule(new MultipleIn(ActLocation::values()))
                                ->multiple()
                                ->live(),

                            TextInput::make('act_location_other')
                                ->label(__('field.act_location_other'))
                                ->visible(
                                    fn (Get $get) => collect($get('act_location'))
                                        ->filter(fn ($value) => ActLocation::isValue($value, ActLocation::OTHER))
                                        ->isNotEmpty()
                                ),
                        ]),
                    Select::make('first_called_institution_id')
                        ->label(__('field.first_called_institution'))
                        ->placeholder(__('placeholder.select_one'))
                        ->relationship('firstCalledInstitution', 'name')
                        ->native(false)
                        ->nullable(),

                    Select::make('other_called_institutions')
                        ->label(__('field.other_called_institutions'))
                        ->placeholder(__('placeholder.select_one'))
                        ->relationship('otherCalledInstitution', 'name')
                        ->multiple()
                        ->nullable(),
                ]),
        ];
    }
}
