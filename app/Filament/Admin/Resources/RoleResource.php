<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Filament\Admin\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                            ->label(__('nomenclature.labels.role_name')),

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
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            // TODO remove index and page
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
