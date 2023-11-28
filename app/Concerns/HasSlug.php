<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    public function initializeHasSlug(): void
    {
        $this->fillable[] = $this->getSlugColumnName();
    }

    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model) {
            $model->slug = Str::slug($model->slug);

            if (! $model->slug || ! $model->slugAlreadyUsed($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });
    }

    protected function getSlugColumnName(): string
    {
        return 'slug';
    }

    protected function getSlugSource(): string
    {
        return $this->title;
    }

    public function scopeWhereSlug(Builder $query, string $slug): Builder
    {
        return $query->where($this->getSlugColumnName(), $slug);
    }

    public function generateSlug(): string
    {
        $base = $slug = Str::slug($this->getSlugSource());
        $suffix = 1;

        while ($this->slugAlreadyUsed($slug)) {
            $slug = Str::slug($base . '_' . $suffix++);
        }

        return $slug;
    }

    protected function slugAlreadyUsed(string $slug): bool
    {
        $query = static::whereSlug($slug)
            ->withoutGlobalScopes();

        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        return $query->exists();
    }
}
