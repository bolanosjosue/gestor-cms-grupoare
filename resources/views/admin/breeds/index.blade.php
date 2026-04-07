@extends('layouts.admin')

@section('title', 'Razas')

@section('actions')
    @if(auth()->user()->tienePermiso('razas', 'puede_agregar'))
    <button class="btn btn-primary" onclick="openBreedModal()">+ Nueva raza</button>
    @endif
@endsection

@section('content')
    @if($breeds->isEmpty())
        <p class="muted" style="margin:0;">No hay razas registradas todavía. Agrega la primera.</p>
    @else
        <table>
            <thead>
            <tr>
                <th>Raza</th>
                <th style="width:170px;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($breeds as $breed)
                <tr>
                    <td><strong>{{ $breed->name }}</strong></td>
                    <td>
                        <div class="row" style="flex-wrap:wrap;justify-content:flex-end;">
                            @if(auth()->user()->tienePermiso('razas', 'puede_editar'))
                            <button class="btn btn-outline btn-small" onclick="openBreedModal({{ $breed->id }}, '{{ addslashes($breed->name) }}')">Editar</button>
                            @endif
                            @if(auth()->user()->tienePermiso('razas', 'puede_eliminar'))
                            <form method="POST" action="{{ route('admin.breeds.destroy', $breed) }}" onsubmit="return confirm('¿Eliminar esta raza?');">
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

    {{-- Modal para crear / editar raza --}}
    <div id="breedModal" class="admin-modal-overlay" style="display:none;">
        <div class="admin-modal">
            <div class="admin-modal__header">
                <h3 id="breedModalTitle">Nueva raza</h3>
                <button type="button" class="admin-modal__close" onclick="closeBreedModal()">&times;</button>
            </div>
            <form id="breedForm" method="POST" action="{{ route('admin.breeds.store') }}">
                @csrf
                <input type="hidden" name="_method" id="breedFormMethod" value="POST">
                <div class="admin-modal__body">
                    <label for="breed_name">Nombre de la raza *</label>
                    <input id="breed_name" type="text" name="name" required autofocus>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="admin-modal__footer">
                    <button type="button" class="btn btn-outline" onclick="closeBreedModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="breedSubmitBtn">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openBreedModal(id, name) {
            const modal   = document.getElementById('breedModal');
            const form    = document.getElementById('breedForm');
            const title   = document.getElementById('breedModalTitle');
            const method  = document.getElementById('breedFormMethod');
            const input   = document.getElementById('breed_name');
            const btn     = document.getElementById('breedSubmitBtn');

            if (id) {
                title.textContent = 'Editar raza';
                btn.textContent   = 'Actualizar';
                method.value      = 'PUT';
                form.action       = '{{ route("admin.breeds.index") }}/' + id;
                input.value       = name || '';
            } else {
                title.textContent = 'Nueva raza';
                btn.textContent   = 'Guardar';
                method.value      = 'POST';
                form.action       = '{{ route("admin.breeds.store") }}';
                input.value       = '';
            }

            modal.style.display = 'flex';
            setTimeout(() => input.focus(), 50);
        }

        function closeBreedModal() {
            document.getElementById('breedModal').style.display = 'none';
        }

        document.getElementById('breedModal').addEventListener('click', function(e) {
            if (e.target === this) closeBreedModal();
        });

        @if($errors->any())
            openBreedModal();
        @endif
    </script>
@endsection
