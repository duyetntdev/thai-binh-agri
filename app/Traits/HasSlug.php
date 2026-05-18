<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Auto-generate slug from 'name' field before creating/updating.
 *
 * Usage: add `use HasSlug;` to any Model that has a `slug` column.
 * Override $slugSource to use a different source field.
 */
trait HasSlug
{
    protected string $slugSource = 'name';

    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->{$model->slugSource});
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty($model->slugSource) && empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->{$model->slugSource});
            }
        });
    }

    protected static function generateUniqueSlug(string $value): string
    {
        $slug     = Str::slug($value);
        $original = $slug;
        $count    = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
