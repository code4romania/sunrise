<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Enums\Role;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CaseTeam extends BaseWidget
{
    public ?Beneficiary $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->team())
            ->columns([
                TextColumn::make('user.first_name')
                    ->label(__('beneficiary.section.specialists.labels.name'))
                    ->formatStateUsing(fn ($record) => $record->user->getFilamentName()),
                TextColumn::make('roles_string')
                    ->default(
                        fn ($record) => $record->roles
                            ->map(fn ($item) => $item->label())
                            ->join(', ')
                    )
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->color(Color::Gray)
                    ->sortable(
                        query: fn (Builder $query, string $direction): Builder => $query
                            ->orderBy('roles', $direction)
                    ),
                TextColumn::make('user.password_set_at')
                    ->label(__('beneficiary.section.specialists.labels.status'))
                    ->default(0)
                    ->formatStateUsing(
                        fn ($state) => $state ? __('user.status.active') : __('user.status.inactive')
                    )
                    ->sortable(
                        query: fn (Builder $query, string $direction): Builder => $query
                            ->select(['case_teams.*', 'users.password_set_at'])
                            ->join('users', 'users.id', '=', 'user_id')
                            ->orderBy('users.password_set_at', $direction)
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form($this->getFormSchema())
                    ->label(__('beneficiary.section.specialists.add_action'))
                    ->modalHeading(__('beneficiary.section.specialists.heading.add_modal'))
                    ->createAnother(false)
                    ->modalSubmitActionLabel(),
            ])
            ->actions([
                EditAction::make()
                    ->form($this->getFormSchema())
                    ->label(__('beneficiary.section.specialists.change_action'))
                    ->modalHeading(__('beneficiary.section.specialists.heading.edit_modal'))
                    ->extraModalFooterActions([
                        DeleteAction::make()
                            ->cancelParentActions()
                            ->label(__('beneficiary.section.specialists.action.delete'))
                            ->modalHeading(__('beneficiary.section.specialists.heading.delete_modal'))
                            ->icon(null),
                    ]),
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
                ->options(fn () => User::getTenantOrganizationUsers())
                ->required(),

            Select::make('roles')
                ->label(__('beneficiary.section.specialists.labels.roles'))
                ->options(fn () => Role::options())
                ->multiple()
                ->required(),

            Hidden::make('beneficiary_id')
                ->formatStateUsing(fn () => $this->record->id),
        ];
    }
}
