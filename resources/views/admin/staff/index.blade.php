@extends('layouts.admin')

@section('title', 'Personal')

@section('actions')
    @if(auth()->user()->tienePermiso('personal', 'puede_agregar'))
    <a class="btn btn-primary" href="{{ route('admin.staff.create') }}">+ Nuevo</a>
    @endif
@endsection

@section('content')
    @if($staff->isEmpty())
        <p class="muted" style="margin:0;">No hay personal cargado.</p>
    @else
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cargo</th>
                <th>Orden</th>
                <th>Activo</th>
                <th>Foto</th>
                <th style="width:160px;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($staff as $m)
                <tr>
                    <td>{{ $m->id }}</td>
                    <td>{{ $m->name }}</td>
                    <td>{{ $m->role }}</td>
                    <td>{{ $m->sort_order }}</td>
                    <td>
                        @if($m->is_active)
                            <span class="pill pill-published">Sí</span>
                        @else
                            <span class="pill pill-draft">No</span>
                        @endif
                    </td>
                    <td>
                        @if($m->photo_url)
                            <a class="muted" target="_blank" href="{{ $m->photo_url }}">URL</a>
                        @elseif($m->photo_path)
                            <a class="muted" target="_blank" href="{{ asset('storage/'.$m->photo_path) }}">Archivo</a>
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="row">
                            @if(auth()->user()->tienePermiso('personal', 'puede_editar'))
                            <a class="btn btn-outline btn-small" href="{{ route('admin.staff.edit', $m) }}">Editar</a>
                            @endif
                            @if(auth()->user()->tienePermiso('personal', 'puede_eliminar'))
                            <form method="POST" action="{{ route('admin.staff.destroy', $m) }}" onsubmit="return confirm('¿Eliminar este miembro?');">
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
    @endif
@endsection

