<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InstitutionResource\Pages;
use App\Filament\Admin\Resources\UserInstitutionResource\Pages\EditUserInstitution;
use App\Filament\Admin\Resources\UserInstitutionResource\Pages\ViewUserInstitution;
use App\Models\Institution;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->withCount(['organizations', 'beneficiaries', 'users'])
                    ->with(['county', 'city'])
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('institution.headings.institution_name')),

                TextColumn::make('county_and_city')
                    ->label(__('institution.headings.registered_office')),

                TextColumn::make('organizations_count')
                    ->label(__('institution.headings.centers')),

                TextColumn::make('beneficiaries_count')
                    ->label(__('institution.headings.cases')),

                TextColumn::make('users_count')
                    ->label(__('institution.headings.specialists')),

                TextColumn::make('status')
                    ->label(__('institution.headings.status')),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details')),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading(__('institution.headings.empty_state'))
            ->emptyStateDescription(null);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'view' => Pages\ViewInstitution::route('/{record}'),
            'edit_institution_details' => Pages\EditInstitutionDetails::route('/{record}/editInstitutionDetails'),
            'edit_institution_centers' => Pages\EditInstitutionCenters::route('/{record}/editCenters'),
            'user.view' => ViewUserInstitution::route('{parent}/user/{record}'),
            'user.edit' => EditUserInstitution::route('{parent}/user/{record}/edit'),
        ];
    }
}
