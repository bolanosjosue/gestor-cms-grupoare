<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class StaticBlogGenerator
{
    public function generate(Article $article, ?string $oldSlug = null): void
    {
        $slug = $article->slug;
        $baseDir = storage_path('app/public/blog');
        
        if ($oldSlug && $oldSlug !== $slug) {
            $oldDir = $baseDir . DIRECTORY_SEPARATOR . $oldSlug;
            $newDir = $baseDir . DIRECTORY_SEPARATOR . $slug;

            if (File::isDirectory($oldDir)) {
                if (File::isDirectory($newDir)) {
                    File::deleteDirectory($newDir);
                }
                File::moveDirectory($oldDir, $newDir);
            }
        }

        $targetDir = $baseDir . DIRECTORY_SEPARATOR . $slug;
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $metaDescription = $article->excerpt ?: $this->excerptFromContent($article->content, 160);
        $appUrl = rtrim(config('app.url'), '/');

        $html = View::make('blog.template', [
            'article' => $article,
            'metaDescription' => $metaDescription,
            'appUrl' => $appUrl,
        ])->render();

        File::put($targetDir . DIRECTORY_SEPARATOR . 'index.html', $html);
    }

    public function deleteBySlug(string $slug): void
    {
        $dir = public_path('blog' . DIRECTORY_SEPARATOR . $slug);

        if (File::isDirectory($dir)) {
            File::deleteDirectory($dir);
        }
    }

    public function delete(Article $article, ?string $slug = null): void
    {
        $this->deleteBySlug($slug ?: $article->slug);
    }

    public function excerptFromContent(string $content, int $length = 160): string
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($content)));
        if ($plain === '') {
            return '';
        }

        if (mb_strlen($plain) <= $length) {
            return $plain;
        }

        return mb_substr($plain, 0, $length - 3) . '...';
    }
}

