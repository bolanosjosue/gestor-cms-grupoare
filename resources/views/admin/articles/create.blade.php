@extends('layouts.admin')

@section('title', 'Nuevo artículo')

@section('actions')
    <a class="btn btn-outline" href="{{ route('admin.articles.index') }}">Volver</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid">
            <div>
                <div class="mt-2">
                    <label for="title">Título *</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required data-slug-source>
                    @error('title')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="slug">Slug</label>
                    <div class="row">
                        <input id="slug" type="text" name="slug" value="{{ old('slug') }}" data-slug-input>
                        <label class="row" style="font-weight:400;gap:0.35rem;white-space:nowrap;">
                            <input type="checkbox" name="slug_auto" value="1" {{ old('slug_auto', '1') ? 'checked' : '' }} data-slug-auto>
                            Auto
                        </label>
                    </div>
                    <small>Se normaliza (minúsculas, sin acentos, guiones).</small>
                    @error('slug')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="excerpt">Resumen / Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                    @error('excerpt')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2" data-tags-root>
                    <label for="tags_input">Tags</label>
                    <input id="tags_input" type="text" data-tags-input placeholder="Escribí y Enter">
                    <input type="hidden" name="tags" value="{{ old('tags', '[]') }}" data-tags-hidden>
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
                        <option value="draft" {{ old('status','draft') === 'draft' ? 'selected' : '' }}>Borrador</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                    </select>
                    @error('status')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label>Artículos recomendados</label>
                    @php $all = \App\Models\Article::orderByDesc('published_at')->get(); @endphp
                    <div style="max-height:220px;overflow:auto;border:1px solid #e5e7eb;padding:0.5rem;border-radius:6px;">
                        @foreach($all as $a)
                            <label style="display:block;margin-bottom:0.25rem;">
                                <input type="checkbox" name="recommended_article_ids[]" value="{{ $a->id }}" {{ in_array($a->id, old('recommended_article_ids', [])) ? 'checked' : '' }}>
                                {{ $a->title }} @if($a->published_at) <small>({{ $a->published_at->format('d/m/Y') }})</small>@endif
                            </label>
                        @endforeach
                    </div>
                    <small>Seleccioná hasta 3 artículos que aparecerán como recomendados en este artículo. No se mostrará este mismo artículo.</small>
                    @error('recommended_article_ids')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="published_at">Fecha de publicación</label>
                    <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at') }}">
                    <small>Si está vacío y publicás, usa la fecha actual.</small>
                    @error('published_at')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <div class="mt-2">
                    <label for="content">Contenido *</label>
                    <textarea id="content" name="content" rows="14">{{ old('content') }}</textarea>
                    @error('content')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="mt-4" style="border-top:1px solid #e5e7eb;padding-top:1rem;">
            <div class="grid">
                <div>
                    <div class="mt-2">
                        <label for="cover_image_url">Portada (URL) *</label>
                        <input id="cover_image_url" type="text" name="cover_image_url" value="{{ old('cover_image_url') }}">
                        <small>Obligatoria si no subís archivo (http/https).</small>
                        @error('cover_image_url')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-2">
                        <label for="cover_image_file">Portada (archivo) *</label>
                        <input id="cover_image_file" type="file" name="cover_image_file" accept="image/*">
                        <small>Si subís archivo, se usa en lugar de la URL.</small>
                        @error('cover_image_file')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-2">
                        <label for="cover_image_alt">Alt (recomendado)</label>
                        <input id="cover_image_alt" type="text" name="cover_image_alt" value="{{ old('cover_image_alt') }}">
                        @error('cover_image_alt')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-right">
            <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
    </form>
@endsection

