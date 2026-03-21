@extends('layouts.admin')

@section('title', 'Editar personal')

@section('actions')
    <a class="btn btn-outline" href="{{ route('admin.staff.index') }}">Volver</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid">
            <div>
                <div class="mt-2">
                    <label for="name">Nombre *</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $staff->name) }}" required>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="role">Cargo *</label>
                    <input id="role" type="text" name="role" value="{{ old('role', $staff->role) }}" required>
                    @error('role')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="sort_order">Orden</label>
                    <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $staff->sort_order) }}">
                    @error('sort_order')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label class="row" style="font-weight:400;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $staff->is_active) ? 'checked' : '' }}>
                        Activo
                    </label>
                    @error('is_active')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <div class="mt-2">
                    <label for="photo_url">Foto (URL)</label>
                    <input id="photo_url" type="text" name="photo_url" value="{{ old('photo_url', $staff->photo_url) }}">
                    <small>http:// o https://</small>
                    @error('photo_url')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="photo_file">Foto (archivo)</label>
                    <input id="photo_file" type="file" name="photo_file" accept="image/*">
                    <small>Si subís archivo, se usa en lugar de la URL.</small>
                    @error('photo_file')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="mt-2">
                    <label for="photo_alt">Alt (recomendado)</label>
                    <input id="photo_alt" type="text" name="photo_alt" value="{{ old('photo_alt', $staff->photo_alt) }}">
                    @error('photo_alt')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                @if($staff->photo_url || $staff->photo_path)
                    <div class="mt-3">
                        <label>Vista previa</label>
                        @php
                            $src = $staff->photo_url ?: asset('storage/'.$staff->photo_path);
                        @endphp
                        <img src="{{ $src }}" alt="{{ $staff->photo_alt }}" style="max-width:140px;border-radius:0.6rem;border:1px solid #e5e7eb;">
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4 text-right">
            <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
    </form>
@endsection

