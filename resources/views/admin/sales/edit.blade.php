@extends('layouts.admin')

@section('title', 'Editar publicación — ' . ($sale->code ?? ''))

@section('actions')
    <a class="btn btn-outline" href="{{ route('admin.sales.index') }}">Volver</a>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.sales.update', $sale) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Sección 1: Identificación --}}
        <div class="form-section">
            <div class="form-section__header">
                <span class="form-section__icon">🔹</span>
                <div>
                    <h3 class="form-section__title">Identificación</h3>
                    <p class="form-section__subtitle">Datos obligatorios del animal</p>
                </div>
            </div>
            <div class="form-grid-3">
                <div>
                    <label for="code">Código / ID del Animal *</label>
                    <input id="code" type="text" name="code" value="{{ old('code', $sale->code) }}" required>
                    @error('code')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="status">Estado *</label>
                    <select id="status" name="status" required>
                        <option value="available" {{ old('status', $sale->status) === 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="reserved" {{ old('status', $sale->status) === 'reserved' ? 'selected' : '' }}>Reservada</option>
                        <option value="sold" {{ old('status', $sale->status) === 'sold' ? 'selected' : '' }}>Vendida</option>
                    </select>
                    @error('status')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="price_crc">Precio (₡) *</label>
                    <input id="price_crc" type="number" name="price_crc" min="0" step="1" value="{{ old('price_crc', $sale->price_crc) }}" required>
                    @error('price_crc')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Sección 2: Datos del animal --}}
        <div class="form-section">
            <div class="form-section__header">
                <span class="form-section__icon">🐃</span>
                <div>
                    <h3 class="form-section__title">Datos del Animal</h3>
                    <p class="form-section__subtitle">Información básica</p>
                </div>
            </div>
            <div class="form-grid-2">
                <div>
                    <label for="age_years">Edad (años)</label>
                    <input id="age_years" type="number" name="age_years" min="0" max="40" value="{{ old('age_years', $sale->age_years) }}">
                    @error('age_years')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="weight_kg">Peso (kg)</label>
                    <input id="weight_kg" type="number" name="weight_kg" min="0" step="0.01" value="{{ old('weight_kg', $sale->weight_kg) }}">
                    @error('weight_kg')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="breed_id">Raza</label>
                    <select id="breed_id" name="breed_id">
                        <option value="">Seleccionar raza</option>
                        @foreach($breeds as $breed)
                            <option value="{{ $breed->id }}" {{ (string) old('breed_id', $sale->breed_id) === (string) $breed->id ? 'selected' : '' }}>{{ $breed->name }}</option>
                        @endforeach
                    </select>
                    @error('breed_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="sex">Sexo *</label>
                    <select id="sex" name="sex" required>
                        <option value="female" {{ old('sex', $sale->sex) === 'female' ? 'selected' : '' }}>Hembra</option>
                        <option value="male" {{ old('sex', $sale->sex) === 'male' ? 'selected' : '' }}>Macho</option>
                    </select>
                    @error('sex')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Sección 3: Genética --}}
        <div class="form-section">
            <div class="form-section__header">
                <span class="form-section__icon">🧬</span>
                <div>
                    <h3 class="form-section__title">Genética</h3>
                    <p class="form-section__subtitle">Raza del padre y la madre</p>
                </div>
            </div>
            <div class="form-grid-2">
                <div>
                    <label for="father_breed_id">Raza del Padre</label>
                    <select id="father_breed_id" name="father_breed_id">
                        <option value="">Seleccionar raza del padre</option>
                        @foreach($breeds as $breed)
                            <option value="{{ $breed->id }}" {{ (string) old('father_breed_id', $sale->father_breed_id) === (string) $breed->id ? 'selected' : '' }}>{{ $breed->name }}</option>
                        @endforeach
                    </select>
                    @error('father_breed_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="mother_breed_id">Raza de la Madre</label>
                    <select id="mother_breed_id" name="mother_breed_id">
                        <option value="">Seleccionar raza de la madre</option>
                        @foreach($breeds as $breed)
                            <option value="{{ $breed->id }}" {{ (string) old('mother_breed_id', $sale->mother_breed_id) === (string) $breed->id ? 'selected' : '' }}>{{ $breed->name }}</option>
                        @endforeach
                    </select>
                    @error('mother_breed_id')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Sección 4: Reproducción --}}
        <div class="form-section">
            <div class="form-section__header">
                <span class="form-section__icon">🍼</span>
                <div>
                    <h3 class="form-section__title">Reproducción</h3>
                    <p class="form-section__subtitle">Información reproductiva (clave para búfalas)</p>
                </div>
            </div>
            <div class="form-grid-2">
                <div>
                    <label for="reproductive_status">Estado Reproductivo *</label>
                    <select id="reproductive_status" name="reproductive_status" required>
                        <option value="empty" {{ old('reproductive_status', $sale->reproductive_status) === 'empty' ? 'selected' : '' }}>Vacía</option>
                        <option value="pregnant" {{ old('reproductive_status', $sale->reproductive_status) === 'pregnant' ? 'selected' : '' }}>Preñada</option>
                        <option value="producing" {{ old('reproductive_status', $sale->reproductive_status) === 'producing' ? 'selected' : '' }}>En producción</option>
                    </select>
                    @error('reproductive_status')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="gestation_months">Meses de Gestación</label>
                    <input id="gestation_months" type="number" name="gestation_months" min="0" max="12" value="{{ old('gestation_months', $sale->gestation_months) }}">
                    @error('gestation_months')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="births_count">Número de Partos</label>
                    <input id="births_count" type="number" name="births_count" min="0" max="30" value="{{ old('births_count', $sale->births_count) }}">
                    @error('births_count')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="milk_production">Producción de Leche (litros/día)</label>
                    <input id="milk_production" type="number" name="milk_production" min="0" step="0.01" value="{{ old('milk_production', $sale->milk_production) }}">
                    @error('milk_production')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Sección 5: Salud --}}
        <div class="form-section">
            <div class="form-section__header">
                <span class="form-section__icon">💉</span>
                <div>
                    <h3 class="form-section__title">Salud y Manejo</h3>
                    <p class="form-section__subtitle">Condición sanitaria del animal</p>
                </div>
            </div>
            <div class="form-grid-3">
                <div>
                    <label for="vaccines_up_to_date_check">Vacunas al día</label>
                    <select name="vaccines_up_to_date" id="vaccines_up_to_date_check">
                        <option value="0" {{ !old('vaccines_up_to_date', $sale->vaccines_up_to_date) ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('vaccines_up_to_date', $sale->vaccines_up_to_date) ? 'selected' : '' }}>Sí</option>
                    </select>
                    @error('vaccines_up_to_date')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="feeding_type">Alimentación *</label>
                    <select id="feeding_type" name="feeding_type" required>
                        <option value="grazing" {{ old('feeding_type', $sale->feeding_type) === 'grazing' ? 'selected' : '' }}>Pastoreo</option>
                        <option value="supplement" {{ old('feeding_type', $sale->feeding_type) === 'supplement' ? 'selected' : '' }}>Suplemento</option>
                        <option value="mixed" {{ old('feeding_type', $sale->feeding_type) === 'mixed' ? 'selected' : '' }}>Mixto</option>
                    </select>
                    @error('feeding_type')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="animal_condition">Condición *</label>
                    <select id="animal_condition" name="animal_condition" required>
                        <option value="excellent" {{ old('animal_condition', $sale->animal_condition) === 'excellent' ? 'selected' : '' }}>Excelente</option>
                        <option value="good" {{ old('animal_condition', $sale->animal_condition) === 'good' ? 'selected' : '' }}>Buena</option>
                        <option value="regular" {{ old('animal_condition', $sale->animal_condition) === 'regular' ? 'selected' : '' }}>Regular</option>
                    </select>
                    @error('animal_condition')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Sección 6: Contacto --}}
        <div class="form-section">
            <div class="form-section__header">
                <span class="form-section__icon">📞</span>
                <div>
                    <h3 class="form-section__title">Contacto</h3>
                    <p class="form-section__subtitle">Número de teléfono de Venezuela</p>
                </div>
            </div>
            <div>
                <label for="phone">Teléfono *</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $sale->phone) }}" required>
                <small>Formato: +58 seguido de 10 dígitos</small>
                @error('phone')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Sección 7: Imagen --}}
        <div class="form-section">
            <div class="form-section__header">
                <span class="form-section__icon">📷</span>
                <div>
                    <h3 class="form-section__title">Imagen</h3>
                    <p class="form-section__subtitle">Fotografía del animal</p>
                </div>
            </div>
            <div>
                <label for="photo_file">Imagen del Animal</label>
                <input id="photo_file" type="file" name="photo_file" accept="image/*">
                <small>Dejar vacío para mantener la imagen actual</small>
                @error('photo_file')<div class="field-error">{{ $message }}</div>@enderror
                @if($sale->photo_path)
                    <div class="mt-1">
                        <img src="{{ asset('storage/' . $sale->photo_path) }}" alt="Imagen actual" style="max-width:180px;border-radius:10px;border:1px solid #e8e5de;">
                    </div>
                @endif
            </div>
        </div>

        {{-- Visibilidad --}}
        <div class="form-section">
            <label class="row" style="font-weight:400;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $sale->is_active) ? 'checked' : '' }}>
                Mostrar en página pública
            </label>
            @error('is_active')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="mt-3 text-right">
            <button class="btn btn-primary" type="submit">Actualizar publicación</button>
        </div>
    </form>
@endsection
