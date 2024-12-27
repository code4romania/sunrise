<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\DocumentResource\Pages;

use App\Actions\BackAction;
use App\Concerns\HasParentResource;
use App\Enums\DocumentType;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\DocumentResource;
use App\Models\Document;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use App\Tables\Columns\DateColumn;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListDocuments extends ListRecords
{
    use HasParentResource;

    protected static string $resource = DocumentResource::class;

    public function __construct()
    {
        activity()->disableLogging();
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)
            ->getBreadcrumbs('documents.index');
    }

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.documents.title.page');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $this->parent])),

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
                ->relationship(null)
                ->successRedirectUrl(fn (Document $record) => static::getParentResource()::getUrl('documents.view', [
                    'parent' => $this->parent,
                    'record' => $record,
                ])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('beneficiary'))
            ->heading(__('beneficiary.section.documents.title.table'))
            ->actionsColumnLabel(__('general.action.actions'))
            ->columns([
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
            ])
            ->actions([
                ViewAction::make('view')
                    ->label(__('general.action.view_details'))
                    ->color('primary')
                    ->url(fn (Document $record) => self::getParentResource()::getUrl('documents.view', [
                        'parent' => $this->parent,
                        'record' => $record,
                    ])),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('beneficiary.section.documents.labels.type'))
                    ->options(DocumentType::options())
                    ->searchable(),
            ])
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading(__('beneficiary.helper_text.documents'))
            ->emptyStateDescription(__('beneficiary.helper_text.documents_2'))
            ->emptyStateActions([
                CreateAction::make()
                    ->modalHeading(__('beneficiary.section.documents.title.add_modal'))
                    ->label(__('beneficiary.section.documents.actions.add'))
                    ->createAnother(false)
                    ->outlined()
                    ->size(ActionSize::ExtraLarge)
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
            ]);
    }
}
