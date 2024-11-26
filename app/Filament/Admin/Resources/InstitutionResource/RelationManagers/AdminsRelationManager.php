<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\RelationManagers;

use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Resources\UserInstitutionResource\Pages\EditUserInstitution;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminsRelationManager extends RelationManager
{
    protected static string $relationship = 'admins';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ...EditUserInstitution::getSchema(),

                Hidden::make('ngo_admin')
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('roles'))
            ->heading(__('institution.headings.admin_users'))
            ->columns([
                TextColumn::make('first_name')
                    ->label(__('institution.labels.first_name')),

                TextColumn::make('last_name')
                    ->label(__('institution.labels.last_name')),

                TextColumn::make('roles.name')
                    ->label(__('institution.labels.roles')),

                TextColumn::make('status')
                    ->label(__('institution.labels.account_status')),

                TextColumn::make('last_login_at')
                    ->label(__('institution.labels.last_login_at')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('institution.actions.add_ngo_admin'))
                    ->modalHeading(__('institution.actions.add_ngo_admin'))
                    ->createAnother(false),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(
                        fn ($record) => InstitutionResource::getUrl('user.view', [
                            'parent' => $this->getOwnerRecord(),
                            'record' => $record,
                        ])
                    ),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('institution.headings.ngo_admin');
    }
}
