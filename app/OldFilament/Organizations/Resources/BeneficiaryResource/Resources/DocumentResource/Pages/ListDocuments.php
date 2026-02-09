<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Resources\DocumentResource\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Document;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Size;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListDocuments extends ListRecords
{
    protected static string $resource = BeneficiaryResource\Resources\DocumentResource::class;

    public function __construct()
    {
        activity()->disableLogging();
    }

    public function getBreadcrumbs(): array
    {
        $parentRecord = $this->getParentRecord();

        return BeneficiaryBreadcrumb::make($parentRecord)
            ->getBreadcrumbs('documents.index');
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.documents.title.page');
    }

    protected function getHeaderActions(): array
    {
        $parentRecord = $this->getParentRecord();

        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $parentRecord])),

            Actions\CreateAction::make()
                ->modalHeading(__('beneficiary.section.documents.title.add_modal'))
                ->label(__('beneficiary.section.documents.actions.add'))
                ->createAnother(false)
                ->modalSubmitActionLabel(__('beneficiary.section.documents.actions.create'))
                ->modalCancelActionLabel(__('general.action.cancel'))
                ->mutateDataUsing(function (array $data) use ($parentRecord) {
                    $data['beneficiary_id'] = $parentRecord->id;

                    return $data;
                })
                ->relationship(null)
                ->successRedirectUrl(fn (Document $record) => static::getResource()::getUrl('view', [
                    'beneficiary' => $parentRecord,
                    'record' => $record,
                ])),
        ];
    }

    public function table(Table $table): Table
    {
        return \App\Filament\Organizations\Schemas\DocumentResourceSchema::table($table)
            ->emptyStateActions([
                Actions\CreateAction::make()
                    ->modalHeading(__('beneficiary.section.documents.title.add_modal'))
                    ->label(__('beneficiary.section.documents.actions.add'))
                    ->createAnother(false)
                    ->outlined()
                    ->size(Size::ExtraLarge)
                    ->modalSubmitActionLabel(__('beneficiary.section.documents.actions.create'))
                    ->modalCancelActionLabel(__('general.action.cancel'))
                    ->mutateDataUsing(function (array $data) {
                        $data['beneficiary_id'] = $this->getParentRecord()->id;

                        return $data;
                    })
                    ->successRedirectUrl(fn (Document $record) => static::getResource()::getUrl('view', [
                        'record' => $record,
                    ])),
            ]);
    }
}
