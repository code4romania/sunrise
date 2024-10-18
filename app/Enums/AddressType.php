<?php

declare(strict_types=1);

namespace App\Enums;

enum AddressType: string
{
    case EFFECTIVE_RESIDENCE = 'effective_residence';
    case LEGAL_RESIDENCE = 'legal_residence';
}
