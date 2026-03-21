<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class PublicArticleController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 1), 1);
        $size = max((int) $request->query('size', 9), 1);
        $size = min($size, 50);

        $query = Article::query()
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'totalCount' => $paginator->total(),
            'items' => $paginator->items(),
        ]);
    }

    public function show(string $slug)
    {
        $article = Article::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return response()->json($article);
    }
}

