@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<style>
    .ds{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:30px}
    .ds-card{padding:22px 20px;border-radius:14px;background:#fff;border:1px solid #e5e7eb;position:relative;overflow:hidden;transition:transform .18s,box-shadow .18s}
    .ds-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.07)}
    .ds-card::after{content:'';position:absolute;top:0;left:0;width:4px;height:100%;border-radius:4px 0 0 4px}
    .ds-card--olive::after{background:#9D9F70}.ds-card--green::after{background:#22c55e}
    .ds-card--amber::after{background:#f59e0b}.ds-card--red::after{background:#ef4444}
    .ds-card--blue::after{background:#3b82f6}.ds-card--purple::after{background:#8b5cf6}
    .ds-card--cyan::after{background:#06b6d4}
    .ds-card .ic{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:17px;margin-bottom:14px;color:#fff}
    .ds-card .lb{font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;margin:0 0 4px;font-weight:600}
    .ds-card .vl{font-size:1.85rem;font-weight:800;margin:0;color:#111827;line-height:1}
    .ds-card .sb{font-size:.73rem;color:#9ca3af;margin:5px 0 0}

    .ds-section{margin-bottom:30px}
    .ds-head{display:flex;align-items:center;gap:10px;margin-bottom:16px}
    .ds-head h3{font-size:1rem;font-weight:700;color:#1f2937;margin:0;display:flex;align-items:center;gap:8px}
    .ds-head h3 i{font-size:.9rem;color:#9D9F70}
    .ds-tag{font-size:.68rem;font-weight:600;padding:3px 10px;border-radius:999px;background:#f0f0e8;color:#7a7c52}

    .ds-cols{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:30px}
    @media(max-width:920px){.ds-cols{grid-template-columns:1fr}}

    .ds-bar-wrap{margin-bottom:30px;padding:18px 20px;background:#fff;border:1px solid #e5e7eb;border-radius:14px}
    .ds-bar{display:flex;height:12px;border-radius:999px;overflow:hidden;background:#f3f4f6}
    .ds-bar div{height:100%;transition:width .5s ease}
    .ds-bar .b-avail{background:linear-gradient(90deg,#22c55e,#4ade80)}.ds-bar .b-resv{background:linear-gradient(90deg,#f59e0b,#fbbf24)}.ds-bar .b-sold{background:linear-gradient(90deg,#ef4444,#f87171)}
    .ds-legend{display:flex;gap:20px;margin-top:10px;flex-wrap:wrap}
    .ds-legend span{display:flex;align-items:center;gap:6px;font-size:.78rem;color:#374151;font-weight:500}
    .ds-legend i.dot{width:10px;height:10px;border-radius:3px;display:inline-block}
    .ds-legend .d-avail{background:#22c55e}.ds-legend .d-resv{background:#f59e0b}.ds-legend .d-sold{background:#ef4444}

    .ds-panel{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;height:100%}

    .ds-rank{display:flex;flex-direction:column;gap:8px}
    .ds-rank-item{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;background:#fafafa;border:1px solid transparent;transition:all .15s}
    .ds-rank-item:hover{background:#f8faf0;border-color:#d4d6a8}
    .ds-rank-num{width:26px;height:26px;border-radius:7px;background:#9D9F70;color:#fff;font-size:.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ds-rank-num.n1{background:#9D9F70}.ds-rank-num.n2{background:#a8aa7d}.ds-rank-num.n3{background:#b3b58a}.ds-rank-num.n4{background:#bec097}.ds-rank-num.n5{background:#c9cba4}
    .ds-rank-photo{width:40px;height:40px;border-radius:8px;object-fit:cover;flex-shrink:0;background:#eee}
    .ds-rank-photo-placeholder{width:40px;height:40px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#9ca3af;font-size:16px}
    .ds-rank-info{flex:1;min-width:0}
    .ds-rank-code{font-weight:700;font-size:.85rem;color:#111827;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .ds-rank-meta{font-size:.72rem;color:#6b7280;margin:2px 0 0}
    .ds-rank-price{font-weight:700;font-size:.92rem;color:#15803d;white-space:nowrap}
    .ds-rank-badge{font-size:.62rem;font-weight:600;padding:2px 7px;border-radius:999px;text-transform:uppercase;display:inline-block;margin-top:2px}
    .ds-rank-badge.st-available{background:#dcfce7;color:#15803d}.ds-rank-badge.st-reserved{background:#fef3c7;color:#92400e}.ds-rank-badge.st-sold{background:#fee2e2;color:#b91c1c}

    .ds-tbl{width:100%;border-collapse:collapse;font-size:.82rem}
    .ds-tbl th{text-align:left;font-weight:600;color:#6b7280;padding:9px 10px;border-bottom:2px solid #f3f4f6;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em}
    .ds-tbl td{padding:10px;border-bottom:1px solid #f3f4f6;color:#374151}
    .ds-tbl tr:last-child td{border-bottom:none}
    .ds-tbl tr:hover td{background:#fafafa}
    .ds-st{display:inline-block;padding:2px 9px;border-radius:999px;font-size:.68rem;font-weight:600;text-transform:uppercase}
    .ds-st.st-available{background:#dcfce7;color:#15803d}.ds-st.st-reserved{background:#fef3c7;color:#92400e}.ds-st.st-sold{background:#fee2e2;color:#b91c1c}

    .ds-empty{color:#9ca3af;font-size:.84rem;text-align:center;padding:24px 0}
    .ds-empty i{display:block;font-size:1.6rem;margin-bottom:6px;color:#d1d5db}
</style>

{{-- ═════════ STAT CARDS ═════════ --}}
<div class="ds">
    <div class="ds-card ds-card--olive">
        <div class="ic" style="background:#9D9F70"><i class="fa-solid fa-cow"></i></div>
        <p class="lb">Animales registrados</p>
        <p class="vl">{{ $salesTotal }}</p>
        <p class="sb">Activos en el sistema</p>
    </div>
    <div class="ds-card ds-card--green">
        <div class="ic" style="background:#22c55e"><i class="fa-solid fa-circle-check"></i></div>
        <p class="lb">Disponibles</p>
        <p class="vl">{{ $salesAvailable }}</p>
        <p class="sb">Listas para venta</p>
    </div>
    <div class="ds-card ds-card--amber">
        <div class="ic" style="background:#f59e0b"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <p class="lb">Reservadas</p>
        <p class="vl">{{ $salesReserved }}</p>
        <p class="sb">Pendientes de cierre</p>
    </div>
    <div class="ds-card ds-card--red">
        <div class="ic" style="background:#ef4444"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <p class="lb">Vendidas</p>
        <p class="vl">{{ $salesSold }}</p>
        <p class="sb">Transacciones completadas</p>
    </div>
    <div class="ds-card ds-card--blue">
        <div class="ic" style="background:#3b82f6"><i class="fa-solid fa-newspaper"></i></div>
        <p class="lb">Artículos</p>
        <p class="vl">{{ $totalArticles }}</p>
        <p class="sb">{{ $publishedArticles }} publicados · {{ $draftArticles }} borradores</p>
    </div>
    <div class="ds-card ds-card--purple">
        <div class="ic" style="background:#8b5cf6"><i class="fa-solid fa-users"></i></div>
        <p class="lb">Personal</p>
        <p class="vl">{{ $staffCount }}</p>
        <p class="sb">Miembros del equipo</p>
    </div>
    <div class="ds-card ds-card--cyan">
        <div class="ic" style="background:#06b6d4"><i class="fa-solid fa-dna"></i></div>
        <p class="lb">Razas</p>
        <p class="vl">{{ $breedsCount }}</p>
        <p class="sb">Catálogo de razas</p>
    </div>
</div>

{{-- ═════════ BARRA DE DISTRIBUCIÓN ═════════ --}}
@if($salesTotal > 0)
<div class="ds-bar-wrap">
    <div class="ds-head" style="margin-bottom:12px">
        <h3><i class="fa-solid fa-chart-pie"></i> Distribución de ventas</h3>
        <span class="ds-tag">{{ $salesTotal }} total</span>
    </div>
    <div class="ds-bar">
        <div class="b-avail" style="width:{{ round($salesAvailable / $salesTotal * 100) }}%"></div>
        <div class="b-resv" style="width:{{ round($salesReserved / $salesTotal * 100) }}%"></div>
        <div class="b-sold" style="width:{{ round($salesSold / $salesTotal * 100) }}%"></div>
    </div>
    <div class="ds-legend">
        <span><i class="dot d-avail"></i> Disponibles ({{ $salesAvailable }})</span>
        <span><i class="dot d-resv"></i> Reservadas ({{ $salesReserved }})</span>
        <span><i class="dot d-sold"></i> Vendidas ({{ $salesSold }})</span>
    </div>
</div>
@endif

{{-- ═════════ TWO COLUMNS ═════════ --}}
<div class="ds-cols">
    {{-- TOP 5 --}}
    <div class="ds-panel">
        <div class="ds-head">
            <h3><i class="fa-solid fa-ranking-star"></i> Top 5 Destacadas</h3>
            <span class="ds-tag">Mayor valor</span>
        </div>
        @if($topSales->count())
            <div class="ds-rank">
                @foreach($topSales as $i => $sale)
                    @php
                        $photo = $sale->photo_path ? asset('storage/' . $sale->photo_path) : null;
                        $stLabel = match($sale->status) { 'reserved' => 'Reservada', 'sold' => 'Vendida', default => 'Disponible' };
                    @endphp
                    <div class="ds-rank-item">
                        <div class="ds-rank-num n{{ $i + 1 }}">{{ $i + 1 }}</div>
                        @if($photo)
                            <img class="ds-rank-photo" src="{{ $photo }}" alt="{{ $sale->code }}">
                        @else
                            <div class="ds-rank-photo-placeholder"><i class="fa-solid fa-image"></i></div>
                        @endif
                        <div class="ds-rank-info">
                            <p class="ds-rank-code">{{ $sale->code }}</p>
                            <p class="ds-rank-meta">{{ $sale->breed->name ?? '—' }} · {{ $sale->age_years ? $sale->age_years . ' años' : '—' }}</p>
                        </div>
                        <div style="text-align:right">
                            <p class="ds-rank-price">₡{{ number_format($sale->price_crc, 0, ',', '.') }}</p>
                            <span class="ds-rank-badge st-{{ $sale->status }}">{{ $stLabel }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="ds-empty">
                <i class="fa-solid fa-inbox"></i>
                No hay publicaciones de venta aún.
            </div>
        @endif
    </div>

    {{-- ÚLTIMAS VENTAS --}}
    <div class="ds-panel">
        <div class="ds-head">
            <h3><i class="fa-solid fa-clock"></i> Últimos registrados</h3>
            <span class="ds-tag">Recientes</span>
        </div>
        @if($latestSales->count())
            <table class="ds-tbl">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Raza</th>
                        <th>Estado</th>
                        <th>Precio</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestSales as $sale)
                        @php $stLabel = match($sale->status) { 'reserved' => 'Reservada', 'sold' => 'Vendida', default => 'Disponible' }; @endphp
                        <tr>
                            <td><strong>{{ $sale->code }}</strong></td>
                            <td>{{ $sale->breed->name ?? '—' }}</td>
                            <td><span class="ds-st st-{{ $sale->status }}">{{ $stLabel }}</span></td>
                            <td style="font-weight:600;color:#15803d">₡{{ number_format($sale->price_crc, 0, ',', '.') }}</td>
                            <td style="text-align:right">
                                <a href="{{ route('admin.sales.edit', $sale) }}" class="btn btn-outline btn-small">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="ds-empty">
                <i class="fa-solid fa-inbox"></i>
                No hay publicaciones aún.
            </div>
        @endif
    </div>
</div>

{{-- ═════════ ÚLTIMOS ARTÍCULOS ═════════ --}}
@if($latestPublished->count())
<div class="ds-panel ds-section">
    <div class="ds-head">
        <h3><i class="fa-solid fa-file-lines"></i> Últimos artículos publicados</h3>
        <span class="ds-tag">{{ $publishedArticles }} publicados</span>
    </div>
    <table class="ds-tbl">
        <thead>
            <tr>
                <th>Título</th>
                <th>Slug</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($latestPublished as $article)
                <tr>
                    <td><strong>{{ $article->title }}</strong></td>
                    <td style="color:#9ca3af">{{ $article->slug }}</td>
                    <td>
                        @if($article->published_at)
                            {{ $article->published_at->format('d/m/Y H:i') }}
                        @else
                            <span style="color:#9ca3af">—</span>
                        @endif
                    </td>
                    <td style="text-align:right">
                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-outline btn-small">Editar</a>
                        @if($article->isPublished())
                            <a href="{{ url('/blog/'.$article->slug.'/') }}" target="_blank" class="btn btn-primary btn-small">Ver</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
