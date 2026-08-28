<?php

declare(strict_types=1);

namespace App\Livewire\Nomenclator;

use App\Filament\Admin\Resources\Benefits\Tables\BenefitsTable as BenefitsTableConfig;
use App\Models\Benefit;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class BenefitsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return BenefitsTableConfig::configure($table);
    }

    protected function getTableQuery(): ?Builder
    {
        return Benefit::query();
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return 'nomenclator_benefits';
    }

    public function render()
    {
        return view('livewire.nomenclator.benefits-table');
    }
}
