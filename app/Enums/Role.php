<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;

enum Role: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case COORDINATOR = 'Coordonator';
    case MANGER = 'Manager de caz';
    case CHEF_MANAGER = 'Șef manager de caz';
    case CHEF_SERVICE = 'Șef serviciu';
    case PSYCHOLOGICAL_ADVICE = 'Consilier Psihologic';
    //  case = 'Psihoterapeut';
    //  case = 'Psiholog Clinician';
    //  case = 'Psihopedagog';
    //  case = 'Asistent social';
    //  case = 'Consilier juridic';
    //  case = 'Facilitator';
    //  case = 'Formator; Medic';
    //  case = 'Asistent medical';
    //  case = 'Terapeut ocupațional';
    //  case = 'Alt Specialis't

    protected function labelKeyPrefix(): ?string
    {
        return 'enum.role';
    }
}
