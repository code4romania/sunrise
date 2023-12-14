<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns;

enum Studies: string
{
    use Concerns\Enums\Arrayable;
    use Concerns\Enums\Comparable;
    use Concerns\Enums\HasLabel;

    case NONE = 'none';
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
    case VOCATIONAL = 'vocational';
    case HIGHSCHOOL = 'highschool';
    case POSTSECONDARY = 'postsecondary';
    case HIGHEREDUCATION = 'highereducation';

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.studies';
    }
}
