<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\RelationManagers;

use App\Filament\Admin\Resources\InstitutionResource\Resources\UserInstitutionResource;
use Filament\Schemas\Schema;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Filament\Admin\Schemas\UserInstitutionResourceSchema;
use App\Tables\Columns\DateTimeColumn;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminsRelationManager extends RelationManager
{
    use PreventSubmitFormOnEnter;

    protected static string $relationship = 'admins';

    protected static ?string $relatedResource = UserInstitutionResource::class;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return UserInstitutionResourceSchema::form($schema);
    }

    public function table(Table $table): Table
    {
        $ownerRecord = $this->getOwnerRecord();

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

                TextColumn::make('userStatus.status')
                    ->label(__('institution.labels.account_status')),

                DateTimeColumn::make('last_login_at')
                    ->label(__('institution.labels.last_login_at')),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label(__('institution.actions.add_ngo_admin'))
                    ->modalHeading(__('institution.actions.add_ngo_admin'))
                    ->createAnother(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('general.action.view_details')),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('institution.headings.ngo_admin');
    }
}
