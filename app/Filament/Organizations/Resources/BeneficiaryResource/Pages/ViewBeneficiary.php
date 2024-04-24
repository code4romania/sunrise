<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\EnumEntry;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiary extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.view.title', [
            'name' => $this->record->full_name,
            'id' => $this->record->id,
        ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns()
            ->schema([
                $this->identitySectionSection(),
                $this->personalInformationSection(),
            ]);
    }

    protected function identitySectionSection(): Section
    {
        return Section::make(__('beneficiary.section.identity.title'))
            ->columnSpan(1)
            ->columns()
            ->headerActions([
                Action::make('view')
                    ->label(__('general.action.view_details'))
                    ->url(fn ($record) => BeneficiaryResource::getUrl('view_identity', ['record' => $record]))
                    ->link(),
            ])
            ->extraAttributes([
                'class' => 'h-full',
            ])
            ->schema([
                TextEntry::make('age')
                    ->label(__('field.age'))
                    ->formatStateUsing(fn ($state) => trans_choice('general.age', $state)),

                TextEntry::make('birthdate')
                    ->label(__('field.birthdate'))
                    ->date(),

                EnumEntry::make('gender')
                    ->label(__('field.gender')),

                TextEntry::make('cnp')
                    ->label(__('field.cnp')),

                EnumEntry::make('civil_status')
                    ->label(__('field.civil_status')),

                TextEntry::make('children_care_count')
                    ->label(__('field.children_care_count')),

                TextEntry::make('legal_residence_address')
                    ->label(__('field.legal_residence_address'))
                    ->icon('heroicon-o-map-pin')
                    ->columnSpanFull(),

                TextEntry::make('primary_phone')
                    ->label(__('field.primary_phone'))
                    ->icon('heroicon-o-phone')
                    ->url(fn ($state) => "tel:{$state}"),

                TextEntry::make('backup_phone')
                    ->label(__('field.backup_phone'))
                    ->icon('heroicon-o-phone')
                    ->url(fn ($state) => "tel:{$state}"),

                TextEntry::make('notes')
                    ->label(__('field.notes'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->columnSpanFull(),
            ]);
    }

    protected function personalInformationSection(): Section
    {
        return Section::make(__('beneficiary.section.personal_information.title'))
            ->columnSpan(1)
            ->columns()
            ->headerActions([
                Action::make('view')
                    ->label(__('general.action.view_details'))
                    ->url(fn ($record) => BeneficiaryResource::getUrl('view_personal_information', ['record' => $record]))
                    ->link(),
            ])
            ->extraAttributes([
                'class' => 'h-full',
            ])
            ->schema([
                EnumEntry::make('presentation_mode')
                    ->label(__('field.presentation_mode')),

                TextEntry::make('referringInstitution.name')
                    ->label(__('field.referring_institution')),

                TextEntry::make('family_doctor_name')
                    ->label(__('field.family_doctor_name')),

                TextEntry::make('family_doctor_contact')
                    ->label(__('field.family_doctor_contact'))
                    ->icon('heroicon-o-phone')
                    ->url(fn ($state) => "tel:{$state}"),

                EnumEntry::make('aggressor.relationship')
                    ->label(__('field.aggressor_relationship')),

                EnumEntry::make('aggressor.gender')
                    ->label(__('field.aggressor_gender')),

                EnumEntry::make('aggressor.has_violence_history')
                    ->label(__('field.aggressor_has_violence_history')),

                EnumEntry::make('has_police_reports')
                    ->label(__('field.has_police_reports'))
                    ->suffix(fn ($record) => Ternary::isYes($record->has_police_reports)
                        ? " ({$record->police_report_count})" : null),

                EnumEntry::make('has_medical_reports')
                    ->label(__('field.has_medical_reports'))
                    ->suffix(fn ($record) => Ternary::isYes($record->has_medical_reports)
                        ? " ({$record->medical_report_count})" : null),

                EnumEntry::make('has_protection_order')
                    ->label(__('field.has_protection_order')),
            ]);
    }
}
