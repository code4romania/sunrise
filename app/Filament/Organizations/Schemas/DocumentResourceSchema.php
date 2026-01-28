<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Schemas;

use App\Enums\DocumentType;
use App\Forms\Components\Select;
use App\Tables\Columns\DateColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class DocumentResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components(self::getFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('beneficiary'))
            ->heading(__('beneficiary.section.documents.title.table'))
            ->recordActionsColumnLabel(__('general.action.actions'))
            ->columns(self::getTableColumns())
            ->recordActions(self::getTableActions())
            ->filters(self::getTableFilters())
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading(__('beneficiary.helper_text.documents'))
            ->emptyStateDescription(__('beneficiary.helper_text.documents_2'));
    }

    public static function getTableColumns(): array
    {
        return [
            DateColumn::make('date')
                ->label(__('beneficiary.section.documents.labels.date'))
                ->sortable(),

            TextColumn::make('name')
                ->label(__('beneficiary.section.documents.labels.name')),

            TextColumn::make('type')
                ->label(__('beneficiary.section.documents.labels.type'))
                ->sortable(),

            TextColumn::make('observations')
                ->label(__('beneficiary.section.documents.labels.observations'))
                ->sortable(),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            \Filament\Actions\ViewAction::make('view')
                ->label(__('general.action.view_details'))
                ->color('primary')
                ->url(fn ($record) => \App\Filament\Organizations\Resources\BeneficiaryResource\Resources\DocumentResource::getUrl('view', [
                    'beneficiary' => $record->beneficiary,
                    'record' => $record,
                ])),
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            SelectFilter::make('type')
                ->label(__('beneficiary.section.documents.labels.type'))
                ->options(DocumentType::options())
                ->searchable(),
        ];
    }

    public static function getFormComponents(): array
    {
        return [
            Select::make('type')
                ->label(__('beneficiary.section.documents.labels.type'))
                ->options(DocumentType::options())
                ->enum(DocumentType::class)
                ->columnSpanFull()
                ->required(),

            TextInput::make('name')
                ->label(__('beneficiary.section.documents.labels.name'))
                ->placeholder(__('beneficiary.placeholder.file_name'))
                ->columnSpanFull()
                ->maxLength(200)
                ->required(),

            Textarea::make('observations')
                ->placeholder(__('beneficiary.placeholder.observations'))
                ->label(__('beneficiary.section.documents.labels.observations'))
                ->columnSpanFull()
                ->maxLength(500),

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
                ->maxSize(config('media-library.max_file_size'))
                ->helperText(__('beneficiary.helper_text.document_file', [
                    'size' => Number::fileSize(config('media-library.max_file_size')),
                ]))
                ->columnSpanFull()
                ->required(),
        ];
    }
}
