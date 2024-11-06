<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\RoleResource\Pages;
use App\Forms\Components\Spacer;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->maxWidth('3xl')
                    ->schema([
                        Placeholder::make('description')
                            ->hiddenLabel()
                            ->content(__('nomenclature.helper_texts.role_page_description')),

                        TextInput::make('name')
                            ->label(__('nomenclature.labels.role_name'))
                            ->placeholder(__('nomenclature.placeholders.role_name')),

                        Spacer::make(),

                        Placeholder::make(__('nomenclature.helper_texts.role_page_default_permissions')),

                        CheckboxList::make('case_permissions')
                            ->label(__('nomenclature.labels.case_permissions'))
                            ->options(CasePermission::options())
                            ->columnSpanFull(),

                        CheckboxList::make('ngo_admin_permissions')
                            ->label(__('nomenclature.labels.ngo_admin_permissions'))
                            ->options(AdminPermission::options())
                            ->columnSpanFull(),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->withCount(['users'])
                    ->with(['organizations'])
            )
            ->heading(__('nomenclature.headings.roles_table'))
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_role')),

            ])
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.role_name')),

                TextColumn::make('institutions')
                    ->label(__('nomenclature.labels.institutions'))
                    ->formatStateUsing(fn ($record) => $record->organizations->unique('institution_id')->count())
                    ->default(0),

                TextColumn::make('organizations')
                    ->label(__('nomenclature.labels.centers'))
                    ->default(0)
                    ->formatStateUsing(fn ($record) => $record->organizations?->unique()->count()),

                TextColumn::make('users_count')
                    ->label(__('nomenclature.labels.users')),

                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GeneralStatus::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('nomenclature.actions.edit')),
            ])
            ->emptyStateHeading(__('nomenclature.labels.empty_state_role_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
