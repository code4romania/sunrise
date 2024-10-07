<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums;
use Filament\Support\Contracts\HasLabel;

enum Citizenship: string implements HasLabel
{
    use Enums\Arrayable;
    use Enums\Comparable;
    use Enums\HasLabel;

    case ROMANIAN = 'romanian';
    case MOLDAVIAN = 'moldavian';
    case ITALIAN = 'italian';
    case GERMAN = 'german';
    case UKRAINIAN = 'ukrainian';
    case HUNGARIAN = 'hungarian';
    case TURKISH = 'turkish';
    case SYRIAN = 'syrian';
    case CHINESE = 'chinese';
    case FRENCH = 'french';
    case BULGARIAN = 'bulgarian';
    case ISRAELI = 'israeli';
    case SERBIAN = 'serbian';
    case GREEK = 'greek';
    case RUSSIAN = 'russian';
    case LEBANESE = 'lebanese';
    case OTHER = 'other';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.citizenship';
    }
}
