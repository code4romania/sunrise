<?php

declare(strict_types=1);

namespace App\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough as BaseHasManyThrough;

class HasManyThrough extends BaseHasManyThrough
{
    public function save(Model $model): bool
    {
        return $model->save();
    }
}
