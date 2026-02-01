<?php

declare(strict_types=1);

namespace App\Livewire\Nomenclator;

use App\Filament\Admin\Resources\Results\Tables\ResultsTable as ResultsTableConfig;
use App\Models\Result;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ResultsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return ResultsTableConfig::configure($table);
    }

    protected function getTableQuery(): ?Builder
    {
        return Result::query();
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return 'nomenclator_results';
    }

    public function render()
    {
        return view('livewire.nomenclator.results-table');
    }
}
