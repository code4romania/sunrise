<?php

declare(strict_types=1);

namespace App\Livewire\Nomenclator;

use App\Filament\Admin\Resources\Services\Tables\ServicesTable as ServicesTableConfig;
use App\Models\Service;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ServicesTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return ServicesTableConfig::configure($table);
    }

    protected function getTableQuery(): ?Builder
    {
        return Service::query();
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return 'nomenclator_services';
    }

    public function render()
    {
        return view('livewire.nomenclator.services-table');
    }
}
