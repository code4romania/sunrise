<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\Widgets;

use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Models\Role;
use App\Models\Specialist;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class CaseTeamWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $record = $this->record;

        return $table
            ->query(
                $record
                    ? $record->specialistsTeam()->with(['roleForDisplay', 'user'])->getQuery()
                    : Specialist::query()->whereRaw('1 = 0')
            )
            ->heading(__('case.view.case_team'))
            ->columns([
                TextColumn::make('roleForDisplay.name')
                    ->label(__('beneficiary.section.specialists.labels.roles'))
                    ->placeholder(__('beneficiary.section.specialists.labels.empty_state_role')),
                TextColumn::make('user.full_name')
                    ->label(__('beneficiary.section.specialists.labels.name')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('beneficiary.section.specialists.add_action'))
                    ->modalHeading(__('beneficiary.section.specialists.heading.add_modal'))
                    ->schema($this->getCaseTeamFormSchema())
                    ->mutateDataUsing(function (array $data) use ($record): array {
                        $data['specialistable_id'] = $record->getKey();
                        $data['specialistable_type'] = $record->getMorphClass();

                        return $data;
                    })
                    ->using(function (array $data): Specialist {
                        return $this->record->specialistsTeam()->create($data);
                    })
                    ->createAnother(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(__('beneficiary.section.specialists.heading.edit_modal'))
                    ->schema($this->getCaseTeamEditFormSchema())
                    ->fillForm(fn (Specialist $record): array => [
                        'role_id' => $record->role_id,
                        'user_id' => $record->user_id,
                    ])
                    ->using(function (Specialist $record, array $data): void {
                        $record->update($data);
                    }),
                DeleteAction::make()
                    ->label(__('beneficiary.section.specialists.action.delete'))
                    ->modalHeading(__('beneficiary.section.specialists.heading.delete_modal')),
            ])
            ->emptyStateHeading(__('beneficiary.section.specialists.title'))
            ->emptyStateDescription(__('case.view.see_details'))
            ->emptyStateIcon('heroicon-o-user-group');
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getCaseTeamFormSchema(): array
    {
        return [
            Select::make('role_id')
                ->label(__('beneficiary.section.specialists.labels.roles'))
                ->options(
                    Role::query()
                        ->active()
                        ->orderBy('sort')
                        ->pluck('name', 'id')
                        ->all()
                )
                ->searchable()
                ->live()
                ->required()
                ->afterStateUpdated(fn (Set $set) => $set('user_id', null)),

            Select::make('user_id')
                ->label(__('beneficiary.section.specialists.labels.name'))
                ->options(function (Get $get): array {
                    $roleId = $get('role_id');
                    if (! $roleId) {
                        return [];
                    }
                    $tenantId = Filament::getTenant()?->id;
                    if (! $tenantId) {
                        return [];
                    }

                    return User::query()
                        ->whereHas('rolesInOrganization', fn ($q) => $q->where('roles.id', $roleId))
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->get()
                        ->pluck('full_name', 'id')
                        ->all();
                })
                ->searchable()
                ->required()
                ->disableOptionWhen(function (string $value, Get $get): bool {
                    $roleId = $get('role_id');
                    if (! $roleId || ! $this->record) {
                        return false;
                    }

                    return $this->record->specialistsTeam()
                        ->where('role_id', (int) $roleId)
                        ->pluck('user_id')
                        ->contains((int) $value);
                }),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getCaseTeamEditFormSchema(): array
    {
        return [
            Select::make('role_id')
                ->label(__('beneficiary.section.specialists.labels.roles'))
                ->options(
                    Role::query()
                        ->active()
                        ->orderBy('sort')
                        ->pluck('name', 'id')
                        ->all()
                )
                ->disabled(),

            Select::make('user_id')
                ->label(__('beneficiary.section.specialists.labels.name'))
                ->options(function (Get $get): array {
                    $roleId = $get('role_id');
                    if (! $roleId) {
                        return [];
                    }

                    return User::query()
                        ->whereHas('rolesInOrganization', fn ($q) => $q->where('roles.id', $roleId))
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->get()
                        ->pluck('full_name', 'id')
                        ->all();
                })
                ->searchable()
                ->required(),
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
