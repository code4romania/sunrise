<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource;
use App\Models\Activity;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListBeneficiaryHistories extends ListRecords
{
    use HasParentResource;

    protected static string $resource = BeneficiaryHistoryResource::class;

    private mixed $withRelations;

    protected string $relationshipKey = 'subject_id';

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)
            ->getHistoryBreadcrumbs();
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.section.history.titles.list');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn () => Activity::whereMorphedTo('subject', $this->parent))
            ->heading(__('beneficiary.section.history.headings.table'))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('beneficiary.section.history.labels.date'))
                    ->date(),

                TextColumn::make('time')
                    ->label(__('beneficiary.section.history.labels.time'))
                    ->state(fn ($record) => $record->created_at)
                    ->time(),

                TextColumn::make('causer.full_name')
                    ->label(__('beneficiary.section.history.labels.user')),

                TextColumn::make('description')
                    ->label(__('beneficiary.section.history.labels.description')),

                TextColumn::make('subject_type')
                    ->label(__('beneficiary.section.history.labels.section'))
                    ->formatStateUsing(fn ($state) => $state === 'beneficiary' ? ucfirst($state) : 'Beneficiary, ' . ucfirst($state)),

                TextColumn::make('subsection')
                    ->label(__('beneficiary.section.history.labels.subsection')),
            ])
            ->actionsColumnLabel(__('beneficiary.section.history.labels.view_action'))
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->color('primary')
                    ->url(fn (Activity $record) => self::getParentResource()::getUrl('beneficiary-histories.view', [
                        'parent' => $this->parent,
                        'record' => $record,
                    ])),
            ]);
    }

    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        return $query;
    }
}
