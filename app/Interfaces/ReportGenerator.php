<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ReportGenerator
{
    public function getHorizontalHeader(): array;

    public function getHorizontalSubHeader(): ?array;

    public function getVerticalHeader(): array;

    public function getVerticalSubHeader(): ?array;

    public function getHorizontalSubHeaderKey(): ?string;

    public function getVerticalHeaderKey(): string;

    public function getVerticalSubHeaderKey(): ?string;

    public function getSelectedFields(): array|string;
}
