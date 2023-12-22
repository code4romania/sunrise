<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\CommunityProfileResource\Pages;

use App\Filament\Organizations\Resources\CommunityProfileResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCommunityProfile extends EditRecord
{
    protected static string $resource = CommunityProfileResource::class;

    public function mount($record = null): void
    {
        $this->record = Filament::getTenant()->communityProfile;

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record->exists) {
            return Filament::getTenant()
                ->communityProfile()
                ->create($data);
        }

        return parent::handleRecordUpdate($record, $data);
    }
}
