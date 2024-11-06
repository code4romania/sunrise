<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Enums\CaseStatus;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CloseFileWidget extends BaseWidget
{
    public ?Beneficiary $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record
                    ->closeFile()
            )
            ->paginated(false)
            ->heading(__('beneficiary.section.close_file.headings.widget'))
            ->headerActions([
                Tables\Actions\Action::make('view_monitoring')
                    ->label(__('general.action.view_details'))
                    ->link()
                    ->visible(fn () => $this->record->closeFile)
                    ->url(BeneficiaryResource::getUrl('view_close_file', ['record' => $this->record])),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('beneficiary.section.close_file.labels.close_date')),
                Tables\Columns\TextColumn::make('close_method')
                    ->label(__('beneficiary.section.close_file.labels.close_method')),
            ])
            ->emptyStateHeading(__('beneficiary.section.close_file.headings.widget_empty_state'))
            ->emptyStateActions([
                Tables\Actions\Action::make('create_close_file')
                    ->label(__('beneficiary.section.close_file.actions.create_widget'))
                    ->outlined()
                    ->disabled(fn () => ! CaseStatus::isValue($this->record->status, CaseStatus::CLOSED))
                    ->url(BeneficiaryResource::getUrl('create_close_file', ['record' => $this->record])),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
