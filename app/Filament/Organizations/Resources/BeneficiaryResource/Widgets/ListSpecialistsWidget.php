<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Enums\UserStatus;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Models\Role;
use App\Models\Specialist;
use App\Models\User;
use App\Models\UserRole;
use App\Tables\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ListSpecialistsWidget extends BaseWidget
{
    public ?Beneficiary $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record
                    ->specialistsTeam()
                    ->with(['user', 'role'])
            )
            ->columns([
                TextColumn::make('role.name')
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->default(__('beneficiary.section.specialists.labels.empty_state_role'))
                    ->color(Color::Gray),

                TextColumn::make('user.full_name')
                    ->label(__('beneficiary.section.specialists.labels.name')),

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
                    ->fillForm(function (Specialist $record) {
                        if (! $record->role_id) {
                            $record->role_id = -1;
                        }

                        return $record->toArray();
                    })
                    ->label(__('beneficiary.section.specialists.change_action'))
                    ->modalHeading(__('beneficiary.section.specialists.heading.edit_modal'))
                    ->extraModalFooterActions([
                        DeleteAction::make()
                            ->cancelParentActions()
                            ->label(__('beneficiary.section.specialists.action.delete'))
                            ->modalHeading(__('beneficiary.section.specialists.heading.delete_modal'))
                            ->link()
                            ->icon(null),
                    ])
                    ->modalExtraFooterActionsAlignment(Alignment::Left),
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

                SelectFilter::make('role_id')
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->options(
                        Role::query()
                            ->active()
                            ->pluck('name', 'id')
                    )
                    ->searchable(),
            ])
            ->heading(__('beneficiary.section.specialists.title'));
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('role_id')
                ->label(__('beneficiary.section.specialists.labels.roles'))
                ->options(
                    fn (string $operation, ?string $state) => $operation === 'edit' && $state === '-1' ?
                        [-1 => __('beneficiary.section.specialists.labels.empty_state_role')] :
                        UserRole::query()
                            ->with('role')
                            ->get()
                            ->pluck('role.name', 'role.id')
                )
                ->searchable()
                ->preload()
                ->afterStateUpdated(fn (Set $set) => $set('user_id', null))
                ->disabled(fn (string $operation) => $operation === 'edit')
                ->live()
                ->required(),

            Select::make('user_id')
                ->label(__('beneficiary.section.specialists.labels.name'))
                ->options(
                    function (Get $get, $state) {
                        $roleID = (int) $get('role_id');
                        if ($roleID === -1) {
                            if ($state) {
                                return [$state => User::find($state)->full_name];
                            }

                            return [];
                        }
                        $users = Cache::driver('array')
                            ->rememberForever(
                                'tenant_users',
                                fn () => Filament::getTenant()
                                    ->users
                                    ->load('rolesInOrganization')
                            );

                        return $users->filter(fn (User $user) => $user->hasRoleInOrganization($roleID))
                            ->pluck('full_name', 'id');
                    }
                )
                ->disableOptionWhen(
                    fn (Get $get, ?Specialist $record, string $value): bool => $this->record
                        ->specialistsTeam
                        ->filter(
                            fn (Specialist $specialist) => $specialist->role_id === (int) $get('role_id') && $specialist->user_id !== $record?->user_id
                        )
                        ->map(fn (Specialist $specialist) => $specialist->user_id)
                        ->contains($value) ||
                        ! User::query()
                            ->find($value)
                            ->userStatus
                            ->isActive()
                )
                ->searchable()
                ->preload()
                ->required(),

            Hidden::make('specialistable_id')
                ->formatStateUsing(fn () => $this->record->id),

            Hidden::make('specialistable_type')
                ->formatStateUsing(fn () => $this->record->getMorphClass()),
        ];
    }
}
