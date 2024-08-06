<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class CaseTeamListWidget extends BaseWidget
{
    public ?Model $record = null;

    private int $limit = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->record->team()->limit($this->limit))
            ->heading(__('beneficiary.section.specialists.title'))
            ->paginated(false)
            ->headerActions([
                Action::make('view')
                    ->label(__('general.action.view_details'))
                    ->link()
                    ->url(fn () => BeneficiaryResource::getUrl('view_specialists', ['record' => $this->record])),
            ])
            ->columns([
                TextColumn::make('roles_string')
                    ->label(__('beneficiary.section.specialists.labels.role'))
                    ->default(
                        fn ($record) =>  $record->roles
                            ?->map(fn ($item) => $item->label())
                            ->join(', ') ?? '-'
                    ),

                TextColumn::make('user_id')
                    ->label(__('beneficiary.section.specialists.labels.name'))
                    ->formatStateUsing(fn ($record) => $record->user->getFilamentName()),
            ])
            ->contentFooter(function () {
                $diff = max(0, $this->record->team()->count() - $this->limit);

                if (! $diff) {
                    return null;
                }

                return view('tables.footer', [
                    'content' => trans_choice('beneficiary.section.specialists.labels.summarize', $diff),
                    'colspan' => 2,
                ]);
            });
    }
}
