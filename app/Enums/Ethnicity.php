<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum Ethnicity: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case ROMANIAN = 'romanian';
    case HUNGARIAN = 'hungarian';
    case ROMA = 'roma';
    case UKRAINIAN = 'ukrainian';
    case GERMAN = 'german';
    case RUSSIAN_LIPPOVAN = 'russian_lippovan';
    case TURKISH = 'turkish';
    case TATAR = 'tatar';
    case SERBIAN = 'serbian';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.ethnicity';
    }
}
