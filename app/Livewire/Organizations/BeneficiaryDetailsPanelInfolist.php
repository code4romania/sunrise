<?php

declare(strict_types=1);

namespace App\Livewire\Organizations;

use App\Filament\Organizations\Schemas\BeneficiaryDetailsPanelSchema;
use App\Models\Beneficiary;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class BeneficiaryDetailsPanelInfolist extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public Beneficiary $beneficiary;

    public function mount(int $beneficiaryId): void
    {
        $this->beneficiary = Beneficiary::query()
            ->with([
                'details',
                'aggressors',
                'flowPresentation',
                'legal_residence.city',
                'legal_residence.county',
                'effective_residence.city',
                'effective_residence.county',
            ])
            ->findOrFail($beneficiaryId);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->beneficiary)
            ->columns(1)
            ->schema(BeneficiaryDetailsPanelSchema::infolistComponents());
    }

    public function render()
    {
        return view('livewire.organizations.beneficiary-details-panel-infolist', [
            'infolist' => $this->cacheSchema('infolist'),
        ]);
    }
}
