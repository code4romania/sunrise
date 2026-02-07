<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\Widgets\DocumentsWidget;
use App\Models\Beneficiary;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditCaseDocuments extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('case.view.documents');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            '' => __('case.view.documents'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema(fn (): array => $this->getWidgetsSchemaComponents([DocumentsWidget::class])),
            ]);
    }

    /**
     * Return empty so the table is only shown once in content(); otherwise it would also appear in the page footer.
     *
     * @return array<int, class-string<\Filament\Widgets\Widget>>
     */
    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function hasInfolist(): bool
    {
        return false;
    }
}
