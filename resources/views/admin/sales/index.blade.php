@extends('layouts.admin')

@section('title', 'Ventas de búfalos')

@section('actions')
    <a class="btn btn-primary" href="{{ route('admin.sales.create') }}">+ Nueva publicación</a>
@endsection

@section('content')
    @if($sales->isEmpty())
        <p class="muted" style="margin:0;">No hay publicaciones de venta todavía.</p>
    @else
        <table>
            <thead>
            <tr>
                <th>Foto</th>
                <th>Código</th>
                <th>Raza</th>
                <th>Sexo</th>
                <th>Edad</th>
                <th>Estado</th>
                <th>Reprod.</th>
                <th>Precio</th>
                <th style="width:170px;">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>
                        @if($sale->photo_path)
                            <img src="{{ asset('storage/' . $sale->photo_path) }}" alt="{{ $sale->code }}" style="width:56px;height:56px;object-fit:cover;border-radius:10px;border:1px solid #e8e5de;">
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $sale->code }}</strong>
                        @if(!$sale->is_active)
                            <div><small class="muted">Oculta</small></div>
                        @endif
                    </td>
                    <td>{{ $sale->breed?->name ?? '—' }}</td>
                    <td>{{ $sale->sex === 'female' ? 'H' : 'M' }}</td>
                    <td>{{ $sale->age_years ? $sale->age_years . 'a' : '—' }}</td>
                    <td>
                        @if($sale->status === 'available')
                            <span class="pill pill-published">Disponible</span>
                        @elseif($sale->status === 'reserved')
                            <span class="pill pill-draft">Reservada</span>
                        @else
                            <span class="pill" style="background:#fceef1;color:#9b2c3e;border:1px solid #f5c8d0;">Vendida</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $repro = ['empty' => 'Vacía', 'pregnant' => 'Preñada', 'producing' => 'Producción'];
                        @endphp
                        {{ $repro[$sale->reproductive_status] ?? '—' }}
                    </td>
                    <td>₡{{ number_format($sale->price_crc, 0, ',', '.') }}</td>
                    <td>
                        <div class="row" style="flex-wrap:wrap;justify-content:flex-end;">
                            <a class="btn btn-outline btn-small" href="{{ route('admin.sales.edit', $sale) }}">Editar</a>
                            <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}" onsubmit="return confirm('¿Eliminar esta publicación?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-small" type="submit">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $sales->links() }}
        </div>
    @endif
@endsection
