<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryHistoryResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryHistoryResource;
use App\Infolists\Components\HistoryChanges;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiaryHistories extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = BeneficiaryHistoryResource::class;

    protected string $relationshipKey = 'subject_id';

    public function __construct()
    {
        activity()->disableLogging();
    }

    public function getBreadcrumbs(): array
    {
        return array_merge(
            BeneficiaryBreadcrumb::make($this->parent)
                ->getBreadcrumbs('beneficiary-histories.index'),
            [self::getResource()::getEventLabel($this->getRecord())]
        );
    }

    public function getTitle(): string|Htmlable
    {
        return self::getResource()::getEventLabel($this->getRecord());
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->columns()
                ->maxWidth('3xl')
                ->schema([
                    HistoryChanges::make('changes'),
                ]),

        ]);
    }
}
