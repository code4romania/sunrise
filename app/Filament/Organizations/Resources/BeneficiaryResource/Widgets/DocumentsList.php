<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class DocumentsList extends BaseWidget
{
    public ?Model $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->documents()->limit(4))
            ->heading(__('beneficiary.section.documents.title.page'))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('beneficiary.section.documents.labels.type'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->summarize(
                        Tables\Columns\Summarizers\Summarizer::make('aaaaa')

                            ->label(
                                function () {
                                    $diff = $this->record->documents()->count() - 4;

                                    return $diff <= 0 ? '' :
                                    __('beneficiary.section.documents.labels.summarize', ['count' => $diff]);
                                }
                            )
                            ->using(fn () => $this->record->documents()->count())
                            ->visible(fn () => $this->record->documents()->count() - 4 > 0)
                    ),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('beneficiary.section.documents.labels.name')),
            ])
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading(__('beneficiary.helper_text.documents'))
            ->emptyStateDescription(__('beneficiary.helper_text.documents_2'))
            ->emptyStateActions([
                Tables\Actions\Action::make('edit')
                    ->label(__('beneficiary.section.documents.actions.add'))
                    ->url(fn () => BeneficiaryResource::getUrl('view_documents', ['record' => $this->record]))
                    ->outlined()
                    ->size(ActionSize::ExtraLarge),
            ]);
    }
}
