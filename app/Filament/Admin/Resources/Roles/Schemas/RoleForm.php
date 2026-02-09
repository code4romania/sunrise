<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Schemas;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make()
                            ->hiddenLabel()
                            ->description(__('nomenclature.helper_texts.role_page_description'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('nomenclature.labels.role_name'))
                                    ->placeholder(__('nomenclature.placeholders.role_name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpanFull(),
                                Toggle::make('case_manager')
                                    ->label(__('nomenclature.labels.role_case_manager')),
                            ]),

                        Section::make(__('nomenclature.helper_texts.role_page_default_permissions'))
                            ->schema([
                                CheckboxList::make('case_permissions')
                                    ->label(__('nomenclature.labels.case_permissions'))
                                    ->options(CasePermission::getOptionsWithoutCaseManager())
                                    ->columns(1)
                                    ->columnSpanFull(),

                                CheckboxList::make('ngo_admin_permissions')
                                    ->label(__('nomenclature.labels.ngo_admin_permissions'))
                                    ->options(AdminPermission::options())
                                    ->columns(1)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
