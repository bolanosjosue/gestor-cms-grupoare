<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;

class SlugService
{
    public static function slugify(string $value): string
    {
        $slug = Str::slug($value);
        return $slug ?: 'articulo';
    }

    public static function uniqueArticleSlugFrom(string $value, ?int $ignoreId = null): string
    {
        $base = self::slugify($value);
        return self::uniqueArticleSlug($base, $ignoreId);
    }

    public static function uniqueArticleSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = self::slugify($baseSlug);
        $original = $slug;
        $counter = 2;

        while (self::articleSlugExists($slug, $ignoreId)) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected static function articleSlugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = Article::query()->where('slug', $slug);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}

