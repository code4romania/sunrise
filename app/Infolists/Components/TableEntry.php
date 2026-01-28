<?php

declare(strict_types=1);

namespace App\Infolists\Components;

use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Infolists\Components\RepeatableEntry;

class TableEntry extends RepeatableEntry
{
    protected string $view = 'infolists.components.table-entry';

    protected array | Closure $headers = [];

    protected array | Closure $columnWidths = [];

    protected bool|Closure $withoutHeader = false;

    protected bool | string | Closure | null $emptyLabel = null;

    protected string | Closure | null $headersAlignment = null;

    protected string $breakPoint = 'md';

    public function breakPoint(string $breakPoint = 'md'): static
    {
        $this->breakPoint = $breakPoint;

        return $this;
    }

    public function columnWidths(array | Closure $widths = []): static
    {
        $this->columnWidths = $widths;

        return $this;
    }

    public function withoutHeader(bool|Closure $condition = true): static
    {
        $this->withoutHeader = $condition;

        return $this;
    }

    public function emptyLabel(bool|string|Closure|null $label = null): static
    {
        $this->emptyLabel = $label;

        return $this;
    }

    public function alignHeaders(string|Closure $alignment = 'left'): static
    {
        $this->headersAlignment = $alignment;

        return $this;
    }

    public function shouldHideHeader(): bool
    {
        return $this->evaluate($this->withoutHeader);
    }

    public function getBreakPoint(): string
    {
        return $this->breakPoint;
    }

    public function getColumnWidths(): array
    {
        return $this->evaluate($this->columnWidths);
    }

    public function getEmptyLabel(): bool|string|null
    {
        return $this->evaluate($this->emptyLabel);
    }

    public function getHeadersAlignment(): string
    {
        return $this->evaluate($this->headersAlignment) ?? 'left';
    }

    public function getHeaders(): array
    {
        $mergedHeaders = [];

        $customHeaders = $this->evaluate($this->headers);

        foreach ($this->getDefaultChildComponents() as $field) {
            if ($field instanceof Hidden || $field->isHidden()) {
                continue;
            }

            $key = method_exists($field, 'getName') ? $field->getName() : $field->getId();

            $isRequired = false;

            if (property_exists($field, 'isRequired') && \is_bool($field->isRequired())) {
                $isRequired = $field->isRequired();

                if (property_exists($field, 'isMarkedAsRequired') && \is_bool($field->isMarkedAsRequired)) {
                    $isRequired = $field->isRequired() && $field->isMarkedAsRequired;
                }
            }

            $item = [
                'label' => $customHeaders[$key] ?? $field->getLabel(),
                'width' => $this->getColumnWidths()[$key] ?? null,
                'required' => $isRequired,
            ];

            $mergedHeaders[method_exists($field, 'getName') ? $field->getName() : $field->getId()] = $item;
        }

        $this->headers = $mergedHeaders;

        return $this->evaluate($this->headers);
    }
}
