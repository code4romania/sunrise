<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Pages;

use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListInstitutions extends ListRecords
{
    protected static string $resource = InstitutionResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('institution.headings.list_title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('selectAccountType')
                ->label(__('institution.actions.create'))
                ->modalHeading(__('institution.account_type.modal_title'))
                ->form([
                    Radio::make('account_type')
                        ->label('')
                        ->options([
                            'full' => __('institution.account_type.full_access'),
                            'community' => __('institution.account_type.community_only'),
                        ])
                        ->descriptions([
                            'full' => __('institution.account_type.full_access_description'),
                            'community' => __('institution.account_type.community_only_description'),
                        ])
                        ->default('full')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    if ($data['account_type'] === 'full') {
                        $this->redirect(InstitutionResource::getUrl('create'));
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->info()
                            ->title(__('institution.account_type.coming_soon'))
                            ->send();
                    }
                })
                ->modalSubmitActionLabel(__('institution.account_type.next_step'))
                ->modalCancelActionLabel(__('institution.account_type.cancel')),
        ];
    }
}
