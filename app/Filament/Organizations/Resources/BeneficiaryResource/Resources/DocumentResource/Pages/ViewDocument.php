<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Resources\DocumentResource\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\DocumentPreview;
use App\Infolists\Components\EnumEntry;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;

class ViewDocument extends ViewRecord
{
    protected static string $resource = BeneficiaryResource\Resources\DocumentResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function getBreadcrumbs(): array
    {
        $parentRecord = $this->getParentRecord();

        return BeneficiaryBreadcrumb::make($parentRecord)
            ->getBreadcrumbs('documents.index');
    }

    protected function getHeaderActions(): array
    {
        $parentRecord = $this->getParentRecord();

        return [
            BackAction::make()
                ->url(static::getResource()::getUrl('index', [
                    'beneficiary' => $parentRecord,
                ])),

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

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->hiddenLabel()
                ->columns()
                ->schema([
                    EnumEntry::make('type')
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
        $parentRecord = $this->getParentRecord();

        $action->authorize($resource::canDelete($this->getRecord()))
            ->successRedirectUrl(static::getResource()::getUrl('index', [
                'beneficiary' => $parentRecord,
            ]));
    }
}
