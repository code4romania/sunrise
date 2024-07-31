<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\DocumentResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\DocumentResource;
use App\Models\Document;
use App\Services\Breadcrumb\Beneficiary as BeneficiaryBreadcrumb;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListDocuments extends ListRecords
{
    use HasParentResource;

    protected static string $resource = DocumentResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForDocuments();
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.documents.title.page');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalHeading(__('beneficiary.section.documents.title.add_modal'))
                ->label(__('beneficiary.section.documents.actions.add'))
                ->createAnother(false)
                ->modalSubmitActionLabel(__('beneficiary.section.documents.actions.create'))
                ->modalCancelActionLabel(__('general.action.cancel'))
                ->mutateFormDataUsing(function (array $data) {
                    $data[$this->getParentRelationshipKey()] = $this->parent->id;

                    return $data;
                })
                ->successRedirectUrl(fn (Document $record) => static::getParentResource()::getUrl('documents.view', [
                    'parent' => $this->parent,
                    'record' => $record,
                ])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('beneficiary.section.documents.title.table'))
            ->actionsColumnLabel(__('general.action.actions'))
            ->columns([
                TextColumn::make('date')
                    ->label(__('beneficiary.section.documents.labels.date'))
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('beneficiary.section.documents.labels.name')),

                TextColumn::make('type')
                    ->label(__('beneficiary.section.documents.labels.type'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->sortable(),

                TextColumn::make('observations')
                    ->label(__('beneficiary.section.documents.labels.observations'))
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make('view')
                    ->url(fn (Document $record) => self::getParentResource()::getUrl('documents.view', [
                        'parent' => $this->parent,
                        'record' => $record,
                    ])),
            ]);
    }
}
