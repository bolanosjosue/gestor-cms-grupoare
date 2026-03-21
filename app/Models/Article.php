<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'tags',
        'content',
        'cover_image_url',
        'cover_image_path',
        'cover_image_alt',
        'published_at',
        'status',
        'recommended_article_ids',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'recommended_article_ids' => 'array',
    ];

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getFinalCoverImageUrl(): ?string
    {
        // Si hay una ruta local de archivo, generar URL relativa desde ella
        if ($this->cover_image_path) {
            return '/storage/' . $this->cover_image_path;
        }
        // Si hay URL externa, devolverla
        return $this->cover_image_url;
    }
}

