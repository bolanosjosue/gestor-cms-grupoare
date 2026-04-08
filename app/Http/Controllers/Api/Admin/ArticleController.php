<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\SlugService;
use App\Services\StaticBlogGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    public function __construct(private readonly StaticBlogGenerator $generator)
    {
    }

    public function index()
    {
        return response()->json(
            Article::orderByDesc('created_at')->paginate(15)
        );
    }

    public function all()
    {
        return response()->json(
            Article::orderByDesc('published_at')->get(['id', 'title', 'published_at'])
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateStore($request);

        $article = new Article();
        $this->fillArticle($article, $request, $data, null);
        $article->save();

        if ($article->isPublished()) {
            $this->generator->generate($article, null);
        }

        return response()->json($article, 201);
    }

    public function show(Article $article)
    {
        return response()->json($article);
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validateUpdate($request, $article);

        $oldSlug   = $article->slug;
        $oldStatus = $article->status;

        $this->fillArticle($article, $request, $data, $oldSlug);
        $article->save();

        if ($article->isPublished()) {
            $this->generator->generate($article, $oldSlug);
        } elseif ($oldStatus === 'published') {
            $this->generator->deleteBySlug($oldSlug);
        }

        return response()->json($article);
    }

    public function destroy(Article $article)
    {
        if ($article->cover_image_path) {
            Storage::disk('public')->delete($article->cover_image_path);
        }

        if ($article->isPublished()) {
            $this->generator->delete($article);
        }

        $article->delete();

        return response()->json(['ok' => true]);
    }

    public function publish(Article $article)
    {
        $article->status       = 'published';
        $article->published_at = $article->published_at ?: now();
        $article->save();

        $this->generator->generate($article, null);

        return response()->json($article);
    }

    public function unpublish(Article $article)
    {
        $oldSlug = $article->slug;

        $article->status       = 'draft';
        $article->published_at = null;
        $article->save();

        $this->generator->deleteBySlug($oldSlug);

        return response()->json($article);
    }

    protected function validateStore(Request $request): array
    {
        return $request->validate([
            'title'                      => ['required', 'string', 'max:255'],
            'slug'                       => ['nullable', 'string', 'max:255'],
            'slug_auto'                  => ['nullable', 'in:1'],
            'excerpt'                    => ['nullable', 'string'],
            'tags'                       => ['nullable', 'string'],
            'content'                    => ['required', 'string'],
            'cover_image_url'            => ['nullable', 'url', 'regex:/^https?:\\/\\//i', 'required_without:cover_image_file'],
            'cover_image_file'           => ['nullable', 'image', 'max:6144', 'required_without:cover_image_url'],
            'cover_image_alt'            => ['nullable', 'string', 'max:255'],
            'published_at'               => ['nullable', 'date'],
            'status'                     => ['required', 'in:draft,published'],
            'recommended_article_ids'    => ['nullable', 'array', 'max:3'],
            'recommended_article_ids.*'  => ['integer', 'exists:articles,id'],
        ]);
    }

    protected function validateUpdate(Request $request, Article $article): array
    {
        $hasExistingCover = (bool) ($article->cover_image_url || $article->cover_image_path);

        $urlRules  = ['nullable', 'url', 'regex:/^https?:\\/\\//i'];
        $fileRules = ['nullable', 'image', 'max:6144'];

        if (! $hasExistingCover) {
            $urlRules[]  = 'required_without:cover_image_file';
            $fileRules[] = 'required_without:cover_image_url';
        }

        return $request->validate([
            'title'                      => ['required', 'string', 'max:255'],
            'slug'                       => ['nullable', 'string', 'max:255'],
            'slug_auto'                  => ['nullable', 'in:1'],
            'excerpt'                    => ['nullable', 'string'],
            'tags'                       => ['nullable', 'string'],
            'content'                    => ['required', 'string'],
            'cover_image_url'            => $urlRules,
            'cover_image_file'           => $fileRules,
            'cover_image_alt'            => ['nullable', 'string', 'max:255'],
            'published_at'               => ['nullable', 'date'],
            'status'                     => ['required', 'in:draft,published'],
            'recommended_article_ids'    => ['nullable', 'array', 'max:3'],
            'recommended_article_ids.*'  => ['integer', 'exists:articles,id'],
        ]);
    }

    protected function fillArticle(Article $article, Request $request, array $data, ?string $oldSlug): void
    {
        $article->title           = $data['title'];
        $article->excerpt         = $data['excerpt'] ?? null;
        $article->content         = $data['content'];
        $article->cover_image_alt = $data['cover_image_alt'] ?? null;
        $article->status          = $data['status'];

        $article->tags = $this->processTags($data['tags'] ?? null);

        if ($article->status === 'published') {
            $article->published_at = $data['published_at']
                ? Carbon::parse($data['published_at'])
                : ($article->published_at ?: now());
        } else {
            $article->published_at = null;
        }

        $useAuto    = $request->boolean('slug_auto') || empty($data['slug']);
        $slugSource = $useAuto ? $article->title : (string) $data['slug'];

        $slug = SlugService::slugify($slugSource);
        if ($slug === '') {
            throw ValidationException::withMessages(['slug' => 'Slug inválido.']);
        }

        $article->slug = SlugService::uniqueArticleSlug($slug, $article->exists ? $article->id : null);

        $this->handleCoverImage($article, $request, $data);

        $recommended = $request->input('recommended_article_ids', []);
        if (! is_array($recommended)) {
            $recommended = [];
        }

        if ($article->exists) {
            $recommended = array_values(array_filter($recommended, fn ($id) => (int) $id !== (int) $article->id));
        }

        $article->recommended_article_ids = array_slice($recommended, 0, 3);
    }

    protected function processTags(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            throw ValidationException::withMessages(['tags' => 'Formato de tags inválido.']);
        }

        if (count($decoded) > 4) {
            throw ValidationException::withMessages(['tags' => 'Máximo 4 tags por artículo.']);
        }

        $out = [];
        foreach ($decoded as $tag) {
            if (! is_string($tag)) continue;

            $tag = trim(preg_replace('/\\s+/', ' ', $tag));
            if ($tag === '') continue;

            if (str_word_count($tag) > 5) {
                throw ValidationException::withMessages([
                    'tags' => "Cada tag puede tener máximo 5 palabras. Tag inválido: \"{$tag}\".",
                ]);
            }

            $out[] = mb_strtolower($tag);
        }

        return array_slice($out, 0, 4);
    }

    protected function handleCoverImage(Article $article, Request $request, array $data): void
    {
        if ($request->hasFile('cover_image_file')) {
            if ($article->cover_image_path) {
                Storage::disk('public')->delete($article->cover_image_path);
            }

            $file     = $request->file('cover_image_file');
            $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('uploads/articles', $filename, 'public');

            $article->cover_image_path = $path;
            $article->cover_image_url  = null;
            return;
        }

        if (! empty($data['cover_image_url'])) {
            if ($article->cover_image_path) {
                Storage::disk('public')->delete($article->cover_image_path);
            }

            $article->cover_image_url  = $data['cover_image_url'];
            $article->cover_image_path = null;
        }
    }
}
