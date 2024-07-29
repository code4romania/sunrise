<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Enums\DocumentType;
use App\Models\Document;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class ViewDocuments extends BaseWidget
{
    public ?Model $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => Document::query()
                    ->where('beneficiary_id', $this?->record->id)
            )
            ->columns([
                TextColumn::make('date')
                    ->label(__('beneficiary.section.documents.labels.date')),
                TextColumn::make('name')
                    ->label(__('beneficiary.section.documents.labels.name')),
                TextColumn::make('type')
                    ->label(__('beneficiary.section.documents.labels.type'))
                    ->formatStateUsing(fn ($state) => $state->label()),
                TextColumn::make('observations')
                    ->label(__('beneficiary.section.documents.labels.observations')),
            ])
            ->actions([
                EditAction::make('view')
                    ->form($this->getSchema())
                    ->modalHeading(__('beneficiary.section.documents.title.edit_modal'))
                    ->label(__('general.action.view_details'))
                    ->icon(null)
                    ->modalSubmitActionLabel(__('general.action.save'))
                    ->modalCancelActionLabel(__('general.action.cancel')),
            ])
            ->actionsColumnLabel(__('general.action.actions'))
            ->headerActions([
                CreateAction::make()
                    ->form($this->getSchema())
                    ->modalHeading(__('beneficiary.section.documents.title.add_modal'))
                    ->label(__('beneficiary.section.documents.actions.add'))
                    ->createAnother(false)
                    ->modalSubmitActionLabel(__('beneficiary.section.documents.actions.create'))
                    ->modalCancelActionLabel(__('general.action.cancel')),
            ])
            ->heading(__('beneficiary.section.documents.title.table'));
    }

    /**
     * @return array
     */
    public function getSchema(): array
    {
        return [
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
                    'application/pdf', 'pdf',
                    'application/msword','doc', 'docx', 'xls', 'csv',
                    'image/*', 'tiff'])
                ->maxSize(25000)
                ->helperText(__('beneficiary.helper_text.document_file'))
                ->required(),

            Hidden::make('beneficiary_id')
                ->formatStateUsing(fn () => $this->record->id),
        ];
    }
}
