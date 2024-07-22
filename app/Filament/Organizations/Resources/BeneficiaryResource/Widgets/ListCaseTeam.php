<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class ListCaseTeam extends BaseWidget
{
    public ?Model $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->team()->limit(4))
            ->heading(__('beneficiary.section.specialists.title'))
            ->paginated(false)
            ->headerActions([
                Tables\Actions\Action::make('view')
                    ->label(__('general.action.view_details'))
                    ->link()
                    ->url(fn () => BeneficiaryResource::getUrl('view_specialists', ['record' => $this->record])),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('roles')
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->badge()
                    ->color(Color::Gray)
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->summarize(
                        Tables\Columns\Summarizers\Summarizer::make('aaaaa')
                            ->label(function () {
                                $diff = $this->record->team()->count() - 4;
                                dd($diff <= 0 ? '' :
                                    __('beneficiary.section.specialists.labels.summarize', ['count' => $diff]));

                                return $diff <= 0 ? '' :
                                __('beneficiary.section.specialists.labels.summarize', ['count' => $diff]);
                            })
                            ->using(fn ($record) => $record->team()->count())
                            ->visible(fn () => $this->record->team()->count() - 4 > 0)
                    ),
                Tables\Columns\TextColumn::make('user_id')
                    ->label(__('beneficiary.section.specialists.labels.name'))
                    ->formatStateUsing(fn ($record) => $record->user->getFilamentName()),
            ]);
    }
}
