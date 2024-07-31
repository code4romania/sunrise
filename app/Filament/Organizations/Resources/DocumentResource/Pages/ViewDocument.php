<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\DocumentResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\DocumentResource;
use App\Infolists\Components\DocumentPreview;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;

class ViewDocument extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = DocumentResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForDocuments();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label(__('beneficiary.section.documents.actions.delete'))
                ->outlined()
                ->icon('heroicon-o-trash')
                ->modalHeading(__('beneficiary.section.documents.title.delete_modal'))
                ->modalAlignment(Alignment::Left)
                ->modalDescription(__('beneficiary.section.documents.labels.delete_description'))
                ->modalSubmitActionLabel(__('beneficiary.section.documents.actions.delete'))
                ->modalCancelActionLabel(__('general.action.cancel'))
                ->modalIcon(null),

            EditAction::make()
                ->record($this->getRecord())
                ->form(self::$resource::getSchema())
                ->modalHeading(__('beneficiary.section.documents.title.edit_modal'))
                ->label(__('beneficiary.section.documents.title.edit_modal'))
                ->icon('heroicon-o-pencil-square')
                ->modalSubmitActionLabel(__('general.action.save'))
                ->modalCancelActionLabel(__('general.action.cancel'))
                ->outlined(),

            Action::make('download')
                ->label(__('beneficiary.section.documents.actions.download'))
                ->outlined()
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function ($record) {
                    $mediaItem = $record->media->first();

                    return response()->download($mediaItem->getPath(), $mediaItem->file_name);
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->hiddenLabel()
                ->columns()
                ->schema([
                    TextEntry::make('type')
                        ->label(__('beneficiary.section.documents.labels.type')),
                    TextEntry::make('observations')
                        ->label(__('beneficiary.section.documents.labels.observations')),
                ]),
            DocumentPreview::make()
                ->columnSpanFull(),
        ]);
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $resource = static::getResource();

        $action->authorize($resource::canDelete($this->getRecord()))
            ->successRedirectUrl(static::getParentResource()::getUrl('documents.index', [
                'parent' => $this->parent,
            ]));
    }
}
