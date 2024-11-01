<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasGeneralStatus;
use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;
    use HasGeneralStatus;

    protected $fillable = [
        'name',
        'case_permissions',
        'ngo_admin_permissions',
    ];

    protected $casts = [
        'case_permissions' => AsEnumCollection::class . ':' . CasePermission::class,
        'ngo_admin_permissions' => AsEnumCollection::class . ':' . AdminPermission::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot('organization_id')
            ->wherePivotNotNull('organization_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'user_roles');
    }
}
