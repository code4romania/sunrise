<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages;

use App\Concerns\HasParentResource;
use App\Enums\ActivityDescription;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource;
use App\Filters\DateFilter;
use App\Models\Activity;
use App\Models\Beneficiary;
use App\Models\User;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use App\Tables\Columns\DateColumn;
use App\Tables\Filters\SelectFilter;
use Filament\Resources\Pages\ListRecords;
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

    public function __construct()
    {
        activity()->disableLogging();
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)
            ->getBreadcrumbs('beneficiary-histories.index');
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
            ->modifyQueryUsing(
                fn (Builder $query) => $query->whereMorphedTo('subject', $this->parent)
                    ->when(
                        ! auth()->user()->hasAccessToAllCases() && ! auth()->user()->isNgoAdmin(),
                        fn (Builder $query) => $query->whereHasMorph(
                            'subject',
                            Beneficiary::class,
                            fn (Builder $query) => $query->whereHas(
                                'specialistsTeam',
                                fn (Builder $query) => $query->where('user_id', auth()->id())
                            )
                        )
                    )
            )
            ->heading(__('beneficiary.section.history.headings.table'))
            ->columns([
                DateColumn::make('created_at')
                    ->label(__('beneficiary.section.history.labels.date')),

                TextColumn::make('time')
                    ->label(__('beneficiary.section.history.labels.time'))
                    ->state(fn ($record) => $record->created_at)
                    ->time(),

                TextColumn::make('causer.full_name')
                    ->label(__('beneficiary.section.history.labels.user')),

                TextColumn::make('description')
                    ->label(__('beneficiary.section.history.labels.description')),

                TextColumn::make('event')
                    ->label(__('beneficiary.section.history.labels.section'))
                    ->formatStateUsing(fn ($record) => self::getResource()::getEventLabel($record)),

                TextColumn::make('subsection')
                    ->label(__('beneficiary.section.history.labels.subsection'))
                    ->state(fn ($record) => self::getResource()::getSubsectionLabel($record)),
            ])
            ->recordActionsColumnLabel(__('beneficiary.section.history.labels.view_action'))
            ->recordActions([
                \Filament\Actions\ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->color('primary')
                    ->url(fn (Activity $record) => self::getParentResource()::getUrl('beneficiary-histories.view', [
                        'parent' => $this->parent,
                        'record' => $record,
                    ])),
            ])
            ->filters([
                SelectFilter::make('description')
                    ->label(__('beneficiary.section.history.labels.description'))
                    ->options(ActivityDescription::options())
                    ->modifyQueryUsing(
                        fn (Builder $query, $state) => $state['value']
                        ? $query->where('description', $state['value'])
                        : $query
                    ),

                SelectFilter::make('causer_id')
                    ->label(__('beneficiary.section.history.labels.user'))
                    ->options(User::getTenantOrganizationUsers()),

                DateFilter::make('created_at')
                    ->label(__('beneficiary.section.history.labels.date')),

            ]);
    }
}
