<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Enums\UserStatus;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                TextColumn::make('user.full_name')
                    ->label(__('beneficiary.section.specialists.labels.name')),

                TextColumn::make('role.name')
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->color(Color::Gray),

                TextColumn::make('user.status')
                    ->label(__('beneficiary.section.specialists.labels.status')),
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
            ->filters([
                SelectFilter::make('status')
                    ->label(__('beneficiary.section.specialists.labels.status'))
                    ->options(UserStatus::options())
                    ->searchable()
                    ->modifyQueryUsing(
                        fn (Builder $query, array $state): Builder => $state['value']
                            ? $query->whereRelation('user', 'status', $state['value'])
                            : $query
                    ),

                SelectFilter::make('roles')
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->options(
                        Role::query()
                            ->active()
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->modifyQueryUsing(
                        fn (Builder $query, $state): Builder => $state['value']
                            ? $query->whereJsonContains('roles', $state['value'])
                            : $query
                    ),
            ])
            ->heading(__('beneficiary.section.specialists.title'));
    }

    /**
     * @return array
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('role_id')
                ->label(__('beneficiary.section.specialists.labels.roles'))
                ->relationship('role', 'name')
                ->required()
                ->live(),

            Select::make('user_id')
                ->label(__('beneficiary.section.specialists.labels.name'))
                ->options(fn (Get $get) => $get('role_id') ? Filament::getTenant()
                    ->users
                    ->load('rolesInOrganization')
                    ->filter(fn (User $user) => $user->rolesInOrganization
                        ->filter(fn (Role $role) => $role->id === (int) $get('role_id'))
                        ->count())
                    ->pluck('full_name', 'id') :
                    [])
                ->disabled(fn (Get $get) => ! $get('role_id'))
                ->required(),

            Hidden::make('beneficiary_id')
                ->formatStateUsing(fn () => $this->record->id),
        ];
    }
}
