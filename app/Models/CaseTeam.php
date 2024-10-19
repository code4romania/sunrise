<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToBeneficiary;
use App\Concerns\LogsActivityOptions;
use App\Enums\Role;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class CaseTeam extends Model
{
    use HasFactory;
    use BelongsToBeneficiary;
    use LogsActivity;
    use LogsActivityOptions;

    protected $fillable = [
        'user_id',
        'roles',
    ];

    protected $casts = [
        'roles' => AsEnumCollection::class . ':' . Role::class,
    ];

    protected $with = [
        'user',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
//            ->whereHas('organizations', function ($query) {
//                $query->where('organization_id', Filament::getTenant()->id);
//            });
    }
}
