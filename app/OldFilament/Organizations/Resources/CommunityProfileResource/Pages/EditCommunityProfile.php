<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\CommunityProfileResource\Pages;

use App\Filament\Organizations\Resources\CommunityProfileResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditCommunityProfile extends EditRecord
{
    protected static string $resource = CommunityProfileResource::class;

    protected string $view = 'filament.organizations.pages.empty-page';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('community.headings.empty_state_title');
    }

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
