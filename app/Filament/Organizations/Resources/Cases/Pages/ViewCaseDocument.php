<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Infolists\Components\DocumentPreview;
use App\Models\Beneficiary;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;

class ViewCaseDocument extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    protected ?Beneficiary $beneficiary = null;

    public function mount(int|string $record): void
    {
        $this->beneficiary = $this->resolveRecord($record);
        $documentId = request()->route('document');
        $this->record = Document::query()
            ->where('beneficiary_id', $this->beneficiary->id)
            ->where('id', $documentId)
            ->firstOrFail();

        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(CaseResource::canView($this->beneficiary), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name ?? __('case.view.documents');
    }

    public function getBreadcrumbs(): array
    {
        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $this->beneficiary]) => $this->beneficiary instanceof Beneficiary ? $this->beneficiary->getBreadcrumb() : '',
            CaseResource::getUrl('edit_case_documents', ['record' => $this->beneficiary]) => __('case.view.documents'),
            '' => $this->getRecord()->name,
        ];
    }

    protected function getHeaderActions(): array
    {
        $document = $this->getRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('edit_case_documents', ['record' => $this->beneficiary])),
            Action::make('download')
                ->label(__('beneficiary.section.documents.actions.download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->outlined()
                ->action(function () use ($document): mixed {
                    $media = $document->getFirstMedia('document_file');
                    if (! $media) {
                        return null;
                    }

                    return response()->download($media->getPath(), $media->file_name);
                }),
            DeleteAction::make()
                ->label(__('beneficiary.section.documents.actions.delete'))
                ->modalHeading(__('beneficiary.section.documents.title.delete_modal'))
                ->modalDescription(__('beneficiary.section.documents.labels.delete_description'))
                ->modalAlignment(Alignment::Left)
                ->successRedirectUrl(CaseResource::getUrl('edit_case_documents', ['record' => $this->beneficiary])),
        ];
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->inlineLabel(true)
            ->record($this->getRecord());
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(2)
                ->schema([
                    TextEntry::make('type')
                        ->label(__('beneficiary.section.documents.labels.type'))
                        ->formatStateUsing(fn ($state) => $state?->getLabel() ?? '—'),
                    TextEntry::make('observations')
                        ->label(__('beneficiary.section.documents.labels.observations')),
                ]),
            DocumentPreview::make()
                ->collection('document_file')
                ->columnSpanFull(),
        ]);
    }
}
