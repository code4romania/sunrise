<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DocumentPreview extends \Filament\Schemas\Components\Component
{
    protected string $view = 'infolists.components.document-preview';

    protected string|Closure $collection = 'default';

    protected Model | Closure | null $record = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function collection(string|Closure $collection = 'default'): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function getCollection(): string
    {
        return $this->evaluate($this->collection);
    }

    public function getFile(): ?Media
    {
        return $this->getRecord()
           ?->getFirstMedia($this->getCollection());
    }

    public function getRecord(bool $withContainerRecord = true): Model|array|null
    {
        $record = $this->evaluate($this->record);
        if ($record !== null) {
            return $record;
        }
        return parent::getRecord($withContainerRecord);
    }

    public function record(Model | Closure $record): static
    {
        $this->record = $record;

        return $this;
    }
}
