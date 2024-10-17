<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Widgets;

use App\Concerns\HasViewContentFooter;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CaseTeamListWidget extends BaseWidget
{
    use HasViewContentFooter;

    public ?Beneficiary $record = null;

    private int $limit = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record
                    ->specialistsTeam()
                    ->with(['role', 'user'])
                    ->limit($this->limit)
            )
            ->heading(__('beneficiary.section.specialists.title'))
            ->paginated(false)
            ->headerActions([
                Action::make('view')
                    ->label(__('general.action.view_details'))
                    ->link()
                    ->url(fn () => BeneficiaryResource::getUrl('view_specialists', ['record' => $this->record])),
            ])
            ->columns([
                TextColumn::make('role.name')
                    ->label(__('beneficiary.section.specialists.labels.role')),

                TextColumn::make('user.full_name')
                    ->label(__('beneficiary.section.specialists.labels.name')),
            ])
            ->contentFooter(
                $this->viewContentFooter($this->record->specialistsTeam()->count(), 'beneficiary.section.specialists.labels.summarize')
            );
    }
}
