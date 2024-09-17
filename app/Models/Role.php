<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'case_permissions',
        'ngo_admin_permissions',
    ];

    protected $casts = [
        'status' => GeneralStatus::class,
        'case_permissions' => AsEnumCollection::class . ':' . CasePermission::class,
        'ngo_admin_permissions' => AsEnumCollection::class . ':' . AdminPermission::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', GeneralStatus::ACTIVE);
    }
}
