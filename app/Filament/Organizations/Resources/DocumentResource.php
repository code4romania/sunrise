<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Enums\DocumentType;
use App\Models\Document;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string $parentResource = BeneficiaryResource::class;

    public static function getRecordTitle(Model|null $record): string|null|Htmlable
    {
        return $record->name;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->label(__('beneficiary.section.documents.labels.type'))
                    ->required()
                    ->options(DocumentType::options())
                    ->enum(DocumentType::class),

                TextInput::make('name')
                    ->label(__('beneficiary.section.documents.labels.name'))
                    ->placeholder(__('beneficiary.placeholder.file_name'))
                    ->required(),

                Textarea::make('observations')
                    ->placeholder(__('beneficiary.placeholder.observations'))
                    ->label(__('beneficiary.section.documents.labels.observations')),

                SpatieMediaLibraryFileUpload::make('document_file')
                    ->label(__('beneficiary.section.documents.labels.document_file'))
                    ->openable()
                    ->downloadable()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'text/csv',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/*',
                    ])
                    ->maxSize(function () {
                        debug('resource');

                        return 1024 * 1024 * 50;
                    })
                    ->helperText(__('beneficiary.helper_text.document_file'))
                    ->required(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
}
