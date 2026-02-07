<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\RelationManagers;

use App\Filament\Admin\Resources\Institutions\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminsRelationManager extends RelationManager
{
    protected static string $relationship = 'admins';

    protected static ?string $relatedResource = UserResource::class;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('institution.headings.admin_users');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->heading(__('institution.headings.admin_users'))
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['roles', 'userStatus']))
            ->columns([
                TextColumn::make('last_name')
                    ->label(__('institution.labels.first_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('first_name')
                    ->label(__('institution.labels.last_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label(__('institution.labels.roles')),

                TextColumn::make('userStatus.status')
                    ->label(__('institution.labels.account_status'))
                    ->badge(),

                TextColumn::make('last_login_at')
                    ->label(__('institution.labels.last_login_at'))
                    ->date('d.m.Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('institution.actions.add_ngo_admin'))
                    ->modalHeading(__('institution.actions.add_ngo_admin'))
                    ->createAnother(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('general.action.view_details')),
                EditAction::make()
                    ->label(__('general.action.edit')),
                DeleteAction::make()
                    ->label(__('general.action.delete')),
            ])
            ->defaultSort('last_name', 'desc');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label(__('institution.labels.first_name'))
                    ->required()
                    ->maxLength(50),
                TextInput::make('last_name')
                    ->label(__('institution.labels.last_name'))
                    ->required()
                    ->maxLength(50),
                TextInput::make('email')
                    ->label(__('institution.labels.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->label(__('institution.labels.phone'))
                    ->tel()
                    ->maxLength(14),
                Hidden::make('ngo_admin')
                    ->default(1),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
