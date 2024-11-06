<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\CommunityProfileResource\Pages;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Models\CommunityProfile;
use App\Models\Service;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class CommunityProfileResource extends Resource
{
    protected static ?string $model = CommunityProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'community-profile';

    protected static ?int $navigationSort = 21;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.community._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.community.profile');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditCommunityProfile::route('/'),
        ];
    }

    public static function form(Form $form): Form
    {
        $services = Service::pluck('name', 'id');

        return $form
            ->schema([
                Section::make()
                    ->columns(4)
                    ->schema([
                        Group::make()
                            ->columnSpan(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('community.field.name'))
                                    ->placeholder(__('community.placeholder.name'))
                                    ->maxLength(200)
                                    ->required(),

                                TableRepeater::make('services')
                                    ->relationship()
                                    ->reorderable(false)
                                    ->hideLabels()
                                    ->addActionLabel(__('service.action.create'))
                                    ->minItems(1)
                                    ->schema([
                                        Hidden::make('model_type')
                                            ->default(app(CommunityProfile::class)->getMorphClass()),

                                        Select::make('service_id')
                                            ->label(__('community.field.service'))
                                            ->options($services)
                                            ->required(),

                                        Toggle::make('is_visible')
                                            ->label(__('community.field.service_visible')),

                                        Toggle::make('is_available')
                                            ->label(__('community.field.service_available')),

                                    ]),

                                Select::make('counties')
                                    ->relationship('counties', 'name')
                                    ->label(__('community.field.location'))
                                    ->placeholder(__('community.placeholder.location'))
                                    ->multiple()
                                    ->preload(),
                            ]),

                        RichEditor::make('description')
                            ->label(__('community.field.description'))
                            ->placeholder(__('community.placeholder.description'))
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label(__('community.field.logo'))
                            ->helperText(__('community.help.logo'))
                            ->image()
                            ->collection('logo')
                            ->conversion('large')
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label(__('community.field.email'))
                            ->placeholder(__('placeholder.email'))
                            ->email(),

                        TextInput::make('phone')
                            ->label(__('community.field.phone'))
                            ->placeholder(__('placeholder.phone'))
                            ->tel(),

                        TextInput::make('website')
                            ->label(__('community.field.website'))
                            ->placeholder(__('placeholder.url'))
                            ->url(),
                    ]),
            ]);
    }
}
