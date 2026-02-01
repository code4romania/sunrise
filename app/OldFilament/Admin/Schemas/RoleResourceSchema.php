<?php

declare(strict_types=1);

namespace App\Filament\Admin\Schemas;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Enums\GeneralStatus;
use App\Forms\Components\Spacer;
use App\Tables\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoleResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components(self::getFormComponents());
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
            ->headerActions(self::getTableHeaderActions())
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->recordActions(self::getTableActions())
            ->emptyStateHeading(__('nomenclature.labels.empty_state_role_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getFormComponents(): array
    {
        return [
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    Placeholder::make('description')
                        ->hiddenLabel()
                        ->content(__('nomenclature.helper_texts.role_page_description')),

                    TextInput::make('name')
                        ->label(__('nomenclature.labels.role_name'))
                        ->placeholder(__('nomenclature.placeholders.role_name'))
                        ->maxLength(200),

                    Toggle::make('case_manager')
                        ->label(__('nomenclature.labels.role_case_manager')),

                    Spacer::make(),

                    Placeholder::make(__('nomenclature.helper_texts.role_page_default_permissions')),

                    CheckboxList::make('case_permissions')
                        ->label(__('nomenclature.labels.case_permissions'))
                        ->options(CasePermission::getOptionsWithoutCaseManager())
                        ->columnSpanFull(),

                    CheckboxList::make('ngo_admin_permissions')
                        ->label(__('nomenclature.labels.ngo_admin_permissions'))
                        ->options(AdminPermission::options())
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
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
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->options(GeneralStatus::options()),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public static function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('nomenclature.actions.add_role')),
        ];
    }
}
