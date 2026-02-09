<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\Widgets;

use App\Enums\DocumentType;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Number;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentsWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $record = $this->record;

        return $table
            ->query(
                $record
                    ? $record->documents()->getQuery()
                    : Document::query()->whereRaw('1 = 0')
            )
            ->heading(__('case.view.documents'))
            ->columns([
                TextColumn::make('date')
                    ->label(__('beneficiary.section.documents.labels.date'))
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('beneficiary.section.documents.labels.type'))
                    ->formatStateUsing(fn ($state) => $state?->getLabel() ?? '—')
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('beneficiary.section.documents.labels.name')),
                TextColumn::make('observations')
                    ->label(__('beneficiary.section.documents.labels.observations'))
                    ->limit(40),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('beneficiary.section.documents.actions.add'))
                    ->modalHeading(__('beneficiary.section.documents.title.add_modal'))
                    ->schema($this->getDocumentFormSchema())
                    ->mutateDataUsing(function (array $data) use ($record): array {
                        $data['beneficiary_id'] = $record->getKey();
                        $data['date'] = $data['date'] ?? now()->format('Y-m-d');

                        return $data;
                    })
                    ->using(function (array $data): Document {
                        $file = $data['document_file'] ?? null;
                        unset($data['document_file']);
                        $document = $this->record->documents()->create($data);
                        if ($file) {
                            $files = is_array($file) ? $file : [$file];
                            foreach ($files as $f) {
                                if ($f instanceof TemporaryUploadedFile && $f->exists()) {
                                    $document->addMediaFromString($f->get())
                                        ->usingFileName($f->getClientOriginalName())
                                        ->withCustomProperties(['contentType' => $f->getMimeType()])
                                        ->toMediaCollection('document_file');
                                }
                            }
                        }

                        return $document;
                    })
                    ->createAnother(false),
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('general.action.view_details'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Document $record): string => $record->name)
                    ->modalContent(fn (Document $record) => view('infolists.components.document-preview', [
                        'getFile' => fn () => $record->getFirstMedia('document_file'),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('general.action.close'))
                    ->modalWidth('7xl'),
                Action::make('download')
                    ->label(__('beneficiary.section.documents.actions.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Document $record): mixed {
                        $media = $record->getFirstMedia('document_file');
                        if (! $media) {
                            return null;
                        }

                        return response()->download($media->getPath(), $media->file_name);
                    })
                    ->visible(fn (Document $record): bool => $record->getFirstMedia('document_file') !== null),
                DeleteAction::make()
                    ->label(__('beneficiary.section.documents.actions.delete'))
                    ->modalHeading(__('beneficiary.section.documents.title.delete_modal'))
                    ->modalDescription(__('beneficiary.section.documents.labels.delete_description')),
            ])
            ->emptyStateHeading(__('case.view.empty_documents'))
            ->emptyStateDescription(__('case.view.upload_document'))
            ->emptyStateIcon('heroicon-o-document');
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private function getDocumentFormSchema(): array
    {
        return [
            Select::make('type')
                ->label(__('beneficiary.section.documents.labels.type'))
                ->options(DocumentType::options())
                ->enum(DocumentType::class)
                ->required(),
            TextInput::make('name')
                ->label(__('beneficiary.section.documents.labels.name'))
                ->placeholder(__('beneficiary.placeholder.file_name'))
                ->maxLength(200)
                ->required(),
            DatePicker::make('date')
                ->label(__('beneficiary.section.documents.labels.date'))
                ->default(now()->format('Y-m-d')),
            Textarea::make('observations')
                ->label(__('beneficiary.section.documents.labels.observations'))
                ->placeholder(__('beneficiary.placeholder.observations'))
                ->maxLength(500),
            SpatieMediaLibraryFileUpload::make('document_file')
                ->label(__('beneficiary.section.documents.labels.document_file'))
                ->collection('document_file')
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
                ->maxSize(config('media-library.max_file_size', 10 * 1024 * 1024))
                ->helperText(__('beneficiary.helper_text.document_file', [
                    'size' => Number::fileSize(config('media-library.max_file_size', 10 * 1024 * 1024)),
                ]))
                ->required(),
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
