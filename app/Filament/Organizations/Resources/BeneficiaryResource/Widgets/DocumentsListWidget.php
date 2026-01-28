<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Concerns\HasViewContentFooter;
use App\Filament\Organizations\Resources\BeneficiaryResource\Resources\DocumentResource;
use App\Models\Beneficiary;
use App\Models\Document;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DocumentsListWidget extends BaseWidget
{
    use HasViewContentFooter;

    public ?Beneficiary $record = null;

    private int $limit = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->documents()->limit($this->limit))
            ->heading(__('beneficiary.section.documents.title.page'))
            ->headerActions([
                \Filament\Actions\Action::make('view')
                    ->label(__('general.action.view_details'))
                    ->url(fn () => DocumentResource::getUrl('index', [
                        'beneficiary' => $this->record,
                    ]))
                    ->link()
                    ->visible(fn () => $this->record->documents->count()),
            ])
            ->paginated(false)
            ->columns([
                TextColumn::make('type')
                    ->label(__('beneficiary.section.documents.labels.type')),

                TextColumn::make('name')
                    ->label(__('beneficiary.section.documents.labels.name')),
            ])
            ->contentFooter(
                $this->viewContentFooter($this->record->documents()->count(), 'beneficiary.section.documents.labels.summarize')
            )
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading(__('beneficiary.helper_text.documents'))
            ->emptyStateDescription(__('beneficiary.helper_text.documents_2'))
            ->emptyStateActions([
                CreateAction::make()
                    ->schema(\App\Filament\Organizations\Schemas\DocumentResourceSchema::getFormComponents())
                    ->modalHeading(__('beneficiary.section.documents.title.add_modal'))
                    ->label(__('beneficiary.section.documents.actions.add'))
                    ->outlined()
                    ->createAnother(false)
                    ->modalSubmitActionLabel(__('beneficiary.section.documents.actions.create'))
                    ->modalCancelActionLabel(__('general.action.cancel'))
                    ->mutateDataUsing(function (array $data) {
                        $data['beneficiary_id'] = $this->record->id;

                        return $data;
                    })
                    ->successRedirectUrl(fn (Document $record) => DocumentResource::getUrl('view', [
                        'beneficiary' => $this->record,
                        'record' => $record,
                    ])),
            ]);
    }
}
