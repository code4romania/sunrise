<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Schemas;

use App\Forms\Components\Select;
use App\Models\CommunityProfile;
use App\Models\Service;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommunityProfileResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        $services = Service::pluck('name', 'id');

        return $schema
            ->components([
                Section::make()
                    ->columns(4)
                    ->schema(self::getFormComponents($services)),
            ]);
    }

    public static function getFormComponents($services): array
    {
        return [
            Group::make()
                ->columnSpan(3)
                ->schema([
                    TextInput::make('name')
                        ->label(__('community.field.name'))
                        ->placeholder(__('community.placeholder.name'))
                        ->maxLength(200)
                        ->required(),

                    Repeater::make('services')
                        ->relationship()
                        ->reorderable(false)
                        ->addActionLabel(__('service.action.create'))
                        ->minItems(1)
                        ->schema([
                            Hidden::make('model_type')
                                ->default(app(CommunityProfile::class)->getMorphClass()),

                            Select::make('service_id')
                                ->label(__('community.field.service'))
                                ->options($services)
                                ->required()
                                ->columnSpan(2),

                            Toggle::make('is_visible')
                                ->label(__('community.field.service_visible')),

                            Toggle::make('is_available')
                                ->label(__('community.field.service_available')),

                        ])
                        ->columns(4)
                        ->itemLabel(fn (array $state): ?string => $services[$state['service_id']] ?? null)
                        ->collapsible(),

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
        ];
    }
}
