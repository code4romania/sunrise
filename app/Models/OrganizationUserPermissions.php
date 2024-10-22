<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\BelongsToOrganization;
use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationUserPermissions extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $fillable = [
        'user_id',
        'case_permissions',
        'admin_permissions',
    ];

    protected $casts = [
        'case_permissions' => AsEnumCollection::class . ':' . CasePermission::class,
        'admin_permissions' => AsEnumCollection::class . ':' . AdminPermission::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
