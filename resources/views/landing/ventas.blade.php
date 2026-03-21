<!doctype html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Ventas Grupo Are">
	<title>Grupo Are | Ventas</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/about.css">
	<link rel="stylesheet" href="css/responsive.css">
	<link rel="stylesheet" href="css/instalaciones.css">
	<link rel="stylesheet" href="css/contact.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
		crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body>

	<header id="header" class="header-section">
		<div class="container">
			<nav class="navbar-custom">
				<a href="{{ route('landing.home') }}" class="navbar-brand">
					<img src="images/logo.png" alt="Grupo Are">
				</a>

				<button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
					☰
				</button>

				<div id="navmenu" class="mainmenu">
					<ul class="nav-list">
						<li><a href="{{ route('landing.home') }}">Inicio</a></li>
						<li><a href="{{ route('landing.about') }}">Nosotros</a></li>
						<li><a href="{{ route('landing.personal') }}">Personal</a></li>
						<li><a href="{{ route('landing.ventas') }}">Ventas</a></li>
						<li><a href="{{ route('landing.recursos') }}">Eventos</a></li>
						<li><a href="{{ route('landing.contact') }}">Contacto</a></li>
					</ul>
				</div>
			</nav>
		</div>
	</header>

	<section id="home" class="hero-section">
		<div class="container">
			<div class="hero-wrap row align-items-center">
				<div class="col-lg-7 col-md-9 col-12">
					<div class="hero-content">
						<h3 class="sub-heading">Catálogo actual</h3>
						<h1 class="heading">Ventas</h1>
						<p class="desc">
							Publicaciones de búfalas disponibles para venta con información clara y contacto directo.
						</p>
						<a href="#portfolio" class="default-btn">Ver búfalas disponibles</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="portfolio" class="ventas-section">
		<div class="container">
			<div class="section-title text-center sec-title-animation animation-style1">
				<div class="section-title__tagline-box">
					<span class="icon-pharmacy"><i class="fa-solid fa-tag"></i></span>
					<p class="section-title__tagline">Ventas</p>
				</div>
				<h2 class="section-title__title title-animation">Búfalas disponibles para venta</h2>
			</div>

			<div class="row g-4">
				@forelse($sales as $sale)
					@php
						$photo = $sale->photo_path ? asset('storage/' . $sale->photo_path) : asset('images/mediterranea.jpg');
						$waPhone = preg_replace('/\D+/', '', (string) $sale->phone);
						$message = rawurlencode('Hola, me interesa el animal ' . $sale->code . ' (Código: ' . $sale->code . ')');
						$statusText = match($sale->status) {
							'reserved' => 'Reservada',
							'sold' => 'Vendida',
							default => 'Disponible',
						};
						$sexText = $sale->sex === 'male' ? 'Macho' : 'Hembra';
					@endphp
					<div class="col-xl-4 col-md-6">
						<article class="venta-card">
							<div class="venta-card__image">
								<img src="{{ $photo }}" alt="{{ $sale->code }}">
								<span class="venta-card__badge venta-card__badge--{{ $sale->status }}">{{ $statusText }}</span>
							</div>
							<div class="venta-card__body">
								<div class="venta-card__code">{{ $sale->code }}</div>
								<ul class="venta-card__details">
									<li><span>Sexo:</span> {{ $sexText }}</li>
									@if($sale->breed)<li><span>Raza:</span> {{ $sale->breed->name }}</li>@endif
									@if($sale->age_years)<li><span>Edad:</span> {{ $sale->age_years }} años</li>@endif
									@if($sale->fatherBreed)<li><span>Raza Padre:</span> {{ $sale->fatherBreed->name }}</li>@endif
									@if($sale->motherBreed)<li><span>Raza Madre:</span> {{ $sale->motherBreed->name }}</li>@endif
									<li><span>Precio:</span> ₡{{ number_format($sale->price_crc, 0, ',', '.') }}</li>
								</ul>
								<div class="venta-card__actions">
									<a href="https://wa.me/{{ $waPhone }}?text={{ $message }}" class="venta-card__wa" target="_blank" rel="noopener noreferrer">
										<i class="fa-brands fa-whatsapp"></i>
										<span>Contactar</span>
									</a>
									<button class="venta-card__eye"
									onclick="openSaleDetail(this)"
									aria-label="Ver detalles"
									data-photo="{{ $photo }}"
									data-code="{{ $sale->code }}"
									data-status-key="{{ $sale->status }}"
									data-status="{{ $statusText }}"
									data-sex="{{ $sexText }}"
									data-breed="{{ $sale->breed?->name ?? '—' }}"
									data-age="{{ $sale->age_years ? $sale->age_years . ' años' : '—' }}"
									data-weight="{{ $sale->weight_kg ? number_format((float) $sale->weight_kg, 0, ',', '.') . ' kg' : '—' }}"
									data-father="{{ $sale->fatherBreed?->name ?? '—' }}"
									data-mother="{{ $sale->motherBreed?->name ?? '—' }}"
									data-repro="{{ match($sale->reproductive_status) { 'pregnant' => 'Preñada' . ($sale->gestation_months ? ' (' . $sale->gestation_months . ' meses)' : ''), 'producing' => 'En producción', default => 'Vacía' } }}"
									data-milk="{{ $sale->milk_production ? $sale->milk_production . ' L/día' : '—' }}"
									data-births="{{ $sale->births_count ?? '—' }}"
									data-vaccines="{{ $sale->vaccines_up_to_date ? 'Sí' : 'No' }}"
									data-feeding="{{ match($sale->feeding_type) { 'grazing' => 'Pastoreo', 'supplement' => 'Suplemento', 'mixed' => 'Mixto', default => '—' } }}"
									data-condition="{{ match($sale->animal_condition) { 'excellent' => 'Excelente', 'regular' => 'Regular', default => 'Buena' } }}"
									data-price="₡{{ number_format($sale->price_crc, 0, ',', '.') }}"
									data-wa="https://wa.me/{{ $waPhone }}?text={{ $message }}">
										<i class="fa-regular fa-eye"></i>
									</button>
								</div>
							</div>
						</article>
					</div>
				@empty
					<div class="col-12">
						<p class="desc text-center" style="max-width: 720px; margin: 0 auto;">
							Aún no hay publicaciones de venta disponibles. Muy pronto tendrás nuevas búfalas publicadas.
						</p>
					</div>
				@endforelse
			</div>
		</div>
	</section>

	<footer class="site-footer">
		<div class="container">
			<div class="row g-4 align-items-start">
				<div class="col-lg-4 col-md-6">
					<div class="footer-brand">
						<img src="images/logo.png" alt="Grupo Are" class="footer-logo">
						<p>
							Más de 25 años impulsando el sector bufalero con visión moderna,
							calidad productiva y compromiso sostenible.
						</p>
					</div>
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="footer-links">
						<h4>Enlaces rápidos</h4>
						<ul>
							<li><a href="{{ route('landing.home') }}">Inicio</a></li>
							<li><a href="{{ route('landing.about') }}">Nosotros</a></li>
							<li><a href="{{ route('landing.personal') }}">Personal</a></li>
							<li><a href="{{ route('landing.ventas') }}">Ventas</a></li>
							<li><a href="{{ route('landing.recursos') }}">Eventos</a></li>
							<li><a href="{{ route('landing.contact') }}">Contacto</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-4 col-md-12">
					<div class="footer-contact">
						<h4>Contacto</h4>
						<p>Grupo Are</p>
						<p>Email: contacto@grupoare.com</p>
						<p>Tel: +57 300 000 0000</p>
						<div class="footer-social">
							<a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
							<a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
							<a href="#" aria-label="Whatsapp"><i class="fa-brands fa-whatsapp"></i></a>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-bottom">
				<p>&copy; 2026 Grupo Are. Todos los derechos reservados.</p>
			</div>
		</div>
	</footer>

	<script>
		const menuToggle = document.getElementById('menuToggle');
		const navmenu = document.getElementById('navmenu');

		menuToggle.addEventListener('click', () => {
			navmenu.classList.toggle('active');
		});
	</script>

	{{-- Modal de detalle --}}
	<div id="saleDetailModal" class="sd-overlay" style="display:none;" onclick="if(event.target===this)closeSaleDetail()">
		<div class="sd-modal">
			<button class="sd-close" onclick="closeSaleDetail()" aria-label="Cerrar">
				<i class="fa-solid fa-xmark"></i>
			</button>

			<div class="sd-layout">
				{{-- LEFT: Photo --}}
				<div class="sd-photo">
					<img id="sdPhoto" src="" alt="">
					<div class="sd-photo__overlay"></div>
					<span id="sdBadge" class="sd-badge"></span>
				</div>

				{{-- RIGHT: Info --}}
				<div class="sd-info">
					<div class="sd-info__header">
						<span class="sd-info__tag">Ficha técnica</span>
						<h3 class="sd-info__code" id="sdCode"></h3>
					</div>

					<div class="sd-columns">
						<div class="sd-col">
							<div class="sd-info__section">
								<h4 class="sd-info__section-title"><i class="fa-solid fa-dna"></i> Datos del animal</h4>
								<div class="sd-data">
									<div class="sd-data__row"><span class="sd-data__label">Sexo</span><span class="sd-data__value" id="sdSex"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Raza</span><span class="sd-data__value" id="sdBreed"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Edad</span><span class="sd-data__value" id="sdAge"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Peso</span><span class="sd-data__value" id="sdWeight"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Condición</span><span class="sd-data__value" id="sdCondition"></span></div>
								</div>
							</div>
							<div class="sd-info__section">
								<h4 class="sd-info__section-title"><i class="fa-solid fa-wheat-awn"></i> Manejo y producción</h4>
								<div class="sd-data">
									<div class="sd-data__row"><span class="sd-data__label">Leche</span><span class="sd-data__value" id="sdMilk"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Vacunas al día</span><span class="sd-data__value" id="sdVaccines"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Alimentación</span><span class="sd-data__value" id="sdFeeding"></span></div>
								</div>
							</div>
						</div>
						<div class="sd-col">
							<div class="sd-info__section">
								<h4 class="sd-info__section-title"><i class="fa-solid fa-heart-pulse"></i> Genética y reproducción</h4>
								<div class="sd-data">
									<div class="sd-data__row"><span class="sd-data__label">Raza Padre</span><span class="sd-data__value" id="sdFather"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Raza Madre</span><span class="sd-data__value" id="sdMother"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Reproducción</span><span class="sd-data__value" id="sdRepro"></span></div>
									<div class="sd-data__row"><span class="sd-data__label">Partos</span><span class="sd-data__value" id="sdBirths"></span></div>
								</div>
							</div>
						</div>
					</div>

					<div class="sd-footer">
						<div class="sd-footer__price-wrap">
							<span class="sd-footer__label">Precio</span>
							<span class="sd-footer__price" id="sdPrice"></span>
						</div>
						<a id="sdWa" href="#" class="sd-footer__cta" target="_blank" rel="noopener noreferrer">
							<i class="fa-brands fa-whatsapp"></i>
							<span>Consultar disponibilidad</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		function openSaleDetail(btn) {
			const d = btn.dataset;
			document.getElementById('sdPhoto').src = d.photo;
			const badge = document.getElementById('sdBadge');
			badge.textContent = d.status;
			badge.className = 'sd-badge sd-badge--' + d.statusKey;
			document.getElementById('sdCode').textContent = d.code;
			document.getElementById('sdSex').textContent = d.sex;
			document.getElementById('sdBreed').textContent = d.breed;
			document.getElementById('sdAge').textContent = d.age;
			document.getElementById('sdWeight').textContent = d.weight;
			document.getElementById('sdFather').textContent = d.father;
			document.getElementById('sdMother').textContent = d.mother;
			document.getElementById('sdRepro').textContent = d.repro;
			document.getElementById('sdMilk').textContent = d.milk;
			document.getElementById('sdBirths').textContent = d.births;
			document.getElementById('sdVaccines').textContent = d.vaccines;
			document.getElementById('sdFeeding').textContent = d.feeding;
			document.getElementById('sdCondition').textContent = d.condition;
			document.getElementById('sdPrice').textContent = d.price;
			document.getElementById('sdWa').href = d.wa;
			document.getElementById('saleDetailModal').style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeSaleDetail() {
			document.getElementById('saleDetailModal').style.display = 'none';
			document.body.style.overflow = '';
		}

		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') closeSaleDetail();
		});
	</script>

</body>

</html>

