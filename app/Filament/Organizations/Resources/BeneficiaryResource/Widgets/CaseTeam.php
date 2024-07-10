<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Enums\Role;
use App\Models\CaseTeam as CaseTeamModel;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class CaseTeam extends BaseWidget
{
    public ?Model $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => CaseTeamModel::query()
                    ->where('beneficiary_id', $this->record->id)
            )
            ->columns([
                TextColumn::make('user.first_name')
                    ->label(__('beneficiary.section.specialists.labels.name'))
                    ->formatStateUsing(fn ($record) => $record->user->getFilamentName()),
                TextColumn::make('roles')
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->badge()
                    ->color(Color::Gray)
                    ->formatStateUsing(fn ($state) => $state->label()),
                TextColumn::make('user.password_set_at')
                    ->label(__('beneficiary.section.specialists.labels.status'))
                    ->default(0)
                    ->formatStateUsing(
                        fn ($state) => $state ? __('user.status.active') : __('user.status.inactive')
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form($this->getFormSchema())
                    ->modalHeading(__('beneficiary.section.specialists.heading.add_modal'))
                    ->label(__('beneficiary.section.specialists.add_action')),
            ])
            ->actions([
                EditAction::make()
                    ->form($this->getFormSchema())
                    ->modalHeading(__('beneficiary.section.specialists.heading.edit_modal'))
                    ->extraModalFooterActions([
                        DeleteAction::make(),
                    ])
                    ->label(__('beneficiary.section.specialists.change_action')),
            ])
            ->heading(__('beneficiary.section.specialists.title'));
    }

    /**
     * @return array
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('user_id')
                ->label(__('beneficiary.section.specialists.labels.name'))
                ->options(fn () => User::getTenantOrganizationUsers()),

            Select::make('roles')
                ->options(fn () => Role::options())
                ->multiple(),

            Hidden::make('beneficiary_id')
                ->formatStateUsing(fn () => $this->record->id),
        ];
    }
}
