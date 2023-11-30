<?php

declare(strict_types=1);

namespace App\Filament\Resources\CommunityProfileResource\Pages;

use App\Filament\Resources\CommunityProfileResource;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCommunityProfile extends EditRecord
{
    protected static string $resource = CommunityProfileResource::class;

    public function mount($record = null): void
    {
        $this->record = filament()->getTenant()->communityProfile;

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(4)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('organization.field.name'))
                            ->maxLength(200)
                            ->columnSpan(3)
                            ->required(),

                        RichEditor::make('description')
                            ->label(__('organization.field.description'))
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
                            ->label(__('organization.field.logo'))
                            ->image()
                            ->collection('logo')
                            ->conversion('large')
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label(__('organization.field.email'))
                            ->email(),

                        TextInput::make('phone')
                            ->label(__('organization.field.phone'))
                            ->tel(),

                        TextInput::make('website')
                            ->label(__('organization.field.website'))
                            ->url(),
                    ]),
            ]);
    }
}
