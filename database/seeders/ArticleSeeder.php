<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Services\SlugService;
use App\Services\StaticBlogGenerator;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::truncate();

        $content1 = <<<HTML
<p>Este es el contenido del primer artículo de ejemplo. Podés editarlo desde el panel de administración.</p>
<p>Incluye texto con <strong>HTML básico</strong> para probar el renderizado.</p>
<h2>Sección</h2>
<p>Más contenido para SEO.</p>
HTML;

        $a1 = new Article();
        $a1->title = 'Primer artículo de ejemplo';
        $a1->slug = SlugService::uniqueArticleSlugFrom($a1->title);
        $a1->excerpt = 'Este es el primer artículo de ejemplo del blog.';
        $a1->tags = ['ejemplo', 'demo', 'cms', 'laravel'];
        $a1->content = $content1;
        $a1->cover_image_url = 'https://via.placeholder.com/1200x630.png?text=Articulo+1';
        $a1->cover_image_alt = 'Portada del primer artículo';
        $a1->status = 'published';
        $a1->published_at = now()->subDays(1);
        $a1->save();

        $content2 = <<<HTML
<p>Segundo artículo de prueba para el mini CMS.</p>
<p>Este queda en borrador por defecto.</p>
HTML;

        $a2 = new Article();
        $a2->title = 'Segundo artículo de ejemplo';
        $a2->slug = SlugService::uniqueArticleSlugFrom($a2->title);
        $a2->excerpt = null;
        $a2->tags = ['segundo', 'articulo', 'prueba'];
        $a2->content = $content2;
        $a2->cover_image_url = 'https://via.placeholder.com/1200x630.png?text=Articulo+2';
        $a2->cover_image_alt = 'Portada del segundo artículo';
        $a2->status = 'draft';
        $a2->published_at = null;
        $a2->save();

        app(StaticBlogGenerator::class)->generate($a1, null);
    }
}

