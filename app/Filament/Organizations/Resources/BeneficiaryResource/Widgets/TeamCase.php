<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Enums\Role;
use App\Models\CaseTeam;
use App\Models\Organization;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class TeamCase extends BaseWidget
{
    public ?Model $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => CaseTeam::query()
                    ->where('beneficiary_id', $this->record->id)
            )
            ->columns([
                TextColumn::make('user.first_name')
                    ->label(__('beneficiary.section.specialists.labels.name'))
                    ->formatStateUsing(fn ($record) => $record->user->getFilamentName()),
                TextColumn::make('roles')
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->badge(),
                TextColumn::make('user.password_set_at')
                    ->label(__('beneficiary.section.specialists.labels.status'))
                    ->default(0)
                    ->formatStateUsing(
                        fn ($state) => $state ? __('user.status.active') : __('user.status.inactive')
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Select::make('user_id')
                            ->options(fn () => Organization::find(Filament::getTenant()->id)
                                ->with('users')
                                ->first()
                                ->users
                                ->map(fn ($item) => [
                                    'full_name' => $item->first_name . ' ' . $item->last_name,
                                    'id' => $item->id,
                                ])
                                ->pluck('full_name', 'id')),

                        Select::make('roles')
                            ->options(fn () => Role::options())
                            ->multiple(),

                        Hidden::make('beneficiary_id')
                            ->formatStateUsing(fn () => $this->record->id),
                    ])
                    ->label(__('beneficiary.section.specialists.add_action')),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        Select::make('user_id')
                            ->options(fn () => Organization::find(Filament::getTenant()->id)
                                ->with('users')
                                ->first()
                                ->users
                                ->map(fn ($item) => [
                                    'full_name' => $item->first_name . ' ' . $item->last_name,
                                    'id' => $item->id,
                                ])
                                ->pluck('full_name', 'id')),

                        Select::make('roles')
                            ->options(fn () => Role::options())
                            ->multiple(),
                    ])
                    ->extraModalFooterActions([
                        DeleteAction::make(),
                    ])
                    ->label(__('beneficiary.section.specialists.change_action')),
            ])
            ->heading(__('beneficiary.section.specialists.title'));
    }
}
