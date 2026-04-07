@extends('layouts.admin')

@section('title', 'Artículos')

@section('actions')
    @if(auth()->user()->tienePermiso('articulos', 'puede_agregar'))
    <a class="btn btn-primary" href="{{ route('admin.articles.create') }}">+ Nuevo</a>
    @endif
@endsection

@section('content')
    @if($articles->isEmpty())
        <p class="muted" style="margin:0;">No hay artículos cargados.</p>
    @else
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Slug</th>
                <th>Estado</th>
                <th>Publicado</th>
                <th style="width:260px;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($articles as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>{{ $a->title }}</td>
                    <td><span class="muted">{{ $a->slug }}</span></td>
                    <td>
                        @if($a->isPublished())
                            <span class="pill pill-published">Publicado</span>
                        @else
                            <span class="pill pill-draft">Borrador</span>
                        @endif
                    </td>
                    <td>
                        @if($a->published_at)
                            {{ $a->published_at->format('Y-m-d H:i') }}
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="row" style="flex-wrap:wrap;">
                            @if(auth()->user()->tienePermiso('articulos', 'puede_editar'))
                            <a class="btn btn-outline btn-small" href="{{ route('admin.articles.edit', $a) }}">Editar</a>

                            @if(!$a->isPublished())
                                <form method="POST" action="{{ route('admin.articles.publish', $a) }}">
                                    @csrf
                                    <button class="btn btn-primary btn-small" type="submit">Publicar</button>
                                </form>
                            @else
                                <a class="btn btn-outline btn-small" href="{{ url('/blog/'.$a->slug.'/') }}" target="_blank">Ver HTML</a>
                                <form method="POST" action="{{ route('admin.articles.unpublish', $a) }}">
                                    @csrf
                                    <button class="btn btn-outline btn-small" type="submit">Borrador</button>
                                </form>
                            @endif
                            @endif

                            @if(auth()->user()->tienePermiso('articulos', 'puede_eliminar'))
                            <form method="POST" action="{{ route('admin.articles.destroy', $a) }}" onsubmit="return confirm('¿Eliminar este artículo?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-small" type="submit">Eliminar</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $articles->links() }}
        </div>
    @endif
@endsection

