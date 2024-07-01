<?php

declare(strict_types=1);

namespace App\Livewire\Beneficiary;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ListDocuments extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?Model $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn () => $this->record->documents())
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('beneficiary.section.documents.labels.type'))
                    ->formatStateUsing(fn ($state) => $state->label()),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('beneficiary.section.documents.labels.name')),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultPaginationPageOption(5);
    }

    public function render(): View
    {
        return view('livewire.beneficiary.list-documents');
    }
}
