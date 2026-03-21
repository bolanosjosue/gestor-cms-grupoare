@extends('layouts.admin')

@section('title', 'Editar artículo')

@section('actions')
    <a class="btn btn-outline" href="{{ route('admin.articles.index') }}">Volver</a>
@endsection

@section('content')
    @php
        $tagsValue = old('tags');
        if ($tagsValue === null) {
            $tagsValue = json_encode($article->tags ?? []);
        }
    @endphp

    <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid">
            <div>
                <div class="mt-2">
                    <label for="title">Título *</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $article->title) }}" required data-slug-source>
                    @error('title')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="slug">Slug</label>
                    <div class="row">
                        <input id="slug" type="text" name="slug" value="{{ old('slug', $article->slug) }}" data-slug-input>
                        <label class="row" style="font-weight:400;gap:0.35rem;white-space:nowrap;">
                            <input type="checkbox" name="slug_auto" value="1" {{ old('slug_auto') ? 'checked' : '' }} data-slug-auto>
                            Auto
                        </label>
                    </div>
                    <small>Si activás Auto, se genera desde el título.</small>
                    @error('slug')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="excerpt">Resumen / Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $article->excerpt) }}</textarea>
                    @error('excerpt')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2" data-tags-root>
                    <label for="tags_input">Tags</label>
                    <input id="tags_input" type="text" data-tags-input placeholder="Escribí y Enter">
                    <input type="hidden" name="tags" value="{{ $tagsValue }}" data-tags-hidden>
                    <div class="row mt-1">
                        <button class="btn btn-outline btn-small" type="button" data-tags-add>+ Agregar</button>
                    </div>
                    <small>Máximo 4 tags, cada uno con hasta 5 palabras.</small>
                    <div class="tags-list" data-tags-list></div>
                    @error('tags')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="status">Estado</label>
                    <select id="status" name="status">
                        <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Borrador</option>
                        <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>Publicado</option>
                    </select>
                    @error('status')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label>Artículos recomendados</label>
                    @php
                        $all = \App\Models\Article::where('id','!=',$article->id)->orderByDesc('published_at')->get();
                        $selected = old('recommended_article_ids', $article->recommended_article_ids ?? []);
                        if (!is_array($selected)) { $selected = json_decode($selected, true) ?: []; }
                    @endphp
                    <div style="max-height:220px;overflow:auto;border:1px solid #e5e7eb;padding:0.5rem;border-radius:6px;">
                        @foreach($all as $a)
                            <label style="display:block;margin-bottom:0.25rem;">
                                <input type="checkbox" name="recommended_article_ids[]" value="{{ $a->id }}" {{ in_array($a->id, $selected) ? 'checked' : '' }}>
                                {{ $a->title }} @if($a->published_at) <small>({{ $a->published_at->format('d/m/Y') }})</small>@endif
                            </label>
                        @endforeach
                    </div>
                    <small>Seleccioná hasta 3 artículos que aparecerán como recomendados en este artículo. No se mostrará el artículo actual.</small>
                    @error('recommended_article_ids')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="published_at">Fecha de publicación</label>
                    <input id="published_at" type="datetime-local" name="published_at"
                           value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\\TH:i')) }}">
                    @error('published_at')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <div class="mt-2">
                    <label for="content">Contenido *</label>
                    <textarea id="content" name="content" rows="14">{{ old('content', $article->content) }}</textarea>
                    @error('content')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="mt-4" style="border-top:1px solid #e5e7eb;padding-top:1rem;">
            <div class="grid">
                <div>
                    <div class="mt-2">
                        <label for="cover_image_url">Portada (URL)</label>
                        <input id="cover_image_url" type="text" name="cover_image_url" value="{{ old('cover_image_url', $article->cover_image_url) }}">
                        <small>Si subís archivo, se ignora la URL. Si dejás ambos vacíos, se mantiene la portada actual.</small>
                        @error('cover_image_url')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-2">
                        <label for="cover_image_file">Portada (archivo)</label>
                        <input id="cover_image_file" type="file" name="cover_image_file" accept="image/*">
                        @error('cover_image_file')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-2">
                        <label for="cover_image_alt">Alt</label>
                        <input id="cover_image_alt" type="text" name="cover_image_alt" value="{{ old('cover_image_alt', $article->cover_image_alt) }}">
                        @error('cover_image_alt')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    @if($article->cover_image_url || $article->cover_image_path)
                        <div class="mt-3">
                            <label>Vista previa</label>
                            @php
                                $src = $article->cover_image_url ?: asset('storage/'.$article->cover_image_path);
                            @endphp
                            <img src="{{ $src }}" alt="{{ $article->cover_image_alt }}" style="max-width:260px;border-radius:0.6rem;border:1px solid #e5e7eb;">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-4 text-right">
            <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
    </form>
@endsection

