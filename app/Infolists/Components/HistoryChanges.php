<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HistoryChanges extends Entry
{
    protected string $view = 'infolists.components.history-changes';

    protected function setUp(): void
    {
        $this->columnSpanFull();
    }

    public function getFields(): Collection
    {
        $state = $this->getState();

        $old = collect($state->get('old'));
        $new = collect($state->get('attributes'));

        return $old->keys()
            ->merge($new->keys())
            ->unique();
    }

    public function getFieldLabel(string $field): string
    {
        $translatePaths = [
            'field',
            'beneficiary.section.identity.labels',
            'beneficiary.section.personal_information.label',
            'beneficiary.section.initial_evaluation.labels',
            'beneficiary.section.detailed_evaluation.labels',
            'beneficiary.section.specialists.labels',
            'beneficiary.section.documents.labels',
        ];

        $field = Str::replace('_id', '', $field);
        foreach ($translatePaths as $path) {
            $key = "$path.$field";

            if (__($key) !== $key) {
                return __($key);
            }
        }

        return $field;
    }
}
