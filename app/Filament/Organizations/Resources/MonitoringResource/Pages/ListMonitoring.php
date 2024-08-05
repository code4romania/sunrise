<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ListMonitoring extends ListRecords
{
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

//    public function getBreadcrumbs(): array
//    {
//        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForDocuments();
//    }
//
//    public function getTitle(): string|Htmlable
//    {
//        return __('beneficiary.section.documents.title.page');
//    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(fn () => self::getParentResource()::getUrl('monitorings.create', [
                    'parent' => $this->parent,
                ])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id'),
            TextColumn::make('number'),
            TextColumn::make('date'),
            TextColumn::make('interval'),
            TextColumn::make('team'),
        ])
            ->emptyStateHeading('aaaaaa');
    }
}
