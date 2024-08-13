<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Concerns\HasViewContentFooter;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\DocumentResource;
use App\Models\Beneficiary;
use App\Models\Document;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\Action;
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
                Action::make('view')
                    ->label(__('general.action.view_details'))
                    ->url(fn () => BeneficiaryResource::getUrl('documents.index', ['parent' => $this->record]))
                    ->link()
                    ->visible(fn () => $this->record->documents->count()),
            ])
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('beneficiary.section.documents.labels.type')),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('beneficiary.section.documents.labels.name')),
            ])
            ->contentFooter(
                $this->viewContentFooter($this->record->documents()->count(), 'beneficiary.section.documents.labels.summarize')
            )
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading(__('beneficiary.helper_text.documents'))
            ->emptyStateDescription(__('beneficiary.helper_text.documents_2'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->form(DocumentResource::getSchema())
                    ->modalHeading(__('beneficiary.section.documents.title.add_modal'))
                    ->label(__('beneficiary.section.documents.actions.add'))
                    ->outlined()
                    ->size(ActionSize::ExtraLarge)
                    ->modalSubmitActionLabel(__('beneficiary.section.documents.actions.create'))
                    ->modalCancelActionLabel(__('general.action.cancel'))
                    ->mutateFormDataUsing(function (array $data) {
                        $data['beneficiary_id'] = $this->record->id;

                        return $data;
                    })
                    ->successRedirectUrl(fn (Document $record) => BeneficiaryResource::getUrl('documents.view', [
                        'parent' => $this->record,
                        'record' => $record,
                    ])),
            ]);
    }
}
