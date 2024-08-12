<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Concerns\HasViewContentFooter;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\CaseTeam;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class CaseTeamListWidget extends BaseWidget
{
    use HasViewContentFooter;

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
                TextColumn::make('roles')
                    ->label(__('beneficiary.section.specialists.labels.role')),

                TextColumn::make('user_id')
                    ->label(__('beneficiary.section.specialists.labels.name'))
                    ->formatStateUsing(fn (CaseTeam $record) => $record->user->getFilamentName()),
            ])
            ->contentFooter(
                fn () => $this->viewContentFooter($this->record->team()->count(), 'beneficiary.section.specialists.labels.summarize')
            );
    }
}
