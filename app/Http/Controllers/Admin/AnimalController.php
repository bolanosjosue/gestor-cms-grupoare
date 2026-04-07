<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function index(Request $request)
    {
        $query = Animal::query();

        // Filtros
        if ($request->filled('agropecuaria')) {
            $query->where('agropecuaria', $request->input('agropecuaria'));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }
        if ($request->filled('estado_reproductivo')) {
            $query->where('estado_reproductivo', $request->input('estado_reproductivo'));
        }
        if ($request->filled('sexo')) {
            $query->where('sexo', $request->input('sexo'));
        }
        if ($request->filled('busqueda')) {
            $search = $request->input('busqueda');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_practico', 'like', "%{$search}%")
                  ->orWhere('identificacion_electronica', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortField = $request->input('sort', 'codigo_practico');
        $sortDir = $request->input('dir', 'asc');
        $allowedSorts = [
            'codigo_practico', 'nombre', 'agropecuaria', 'estado',
            'composicion_racial', 'ultimo_peso', 'estado_reproductivo', 'fecha_ultimo_parto',
        ];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'codigo_practico';
        }
        $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortDir);

        $perPage = min((int) $request->input('per_page', 25), 100) ?: 25;
        $animales = $query->paginate($perPage)->withQueryString();

        // Para peticiones AJAX retorna JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $animales->items(),
                'current_page' => $animales->currentPage(),
                'last_page' => $animales->lastPage(),
                'total' => $animales->total(),
            ]);
        }

        return view('admin.animales.index', compact('animales'));
    }

    public function show(Animal $animale)
    {
        return response()->json($animale);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request, true);

        $animal = new Animal();
        $this->fillAndSave($animal, $data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Animal registrado correctamente.', 'animal' => $animal], 201);
        }

        return redirect()->route('admin.animales.index')->with('status', 'Animal registrado correctamente.');
    }

    public function update(Request $request, Animal $animale)
    {
        $data = $this->validateData($request, false, $animale->id);

        // Filtrar solo los campos que el usuario tiene permiso de editar
        $user = $request->user();
        if ($user && !$user->is_super_admin) {
            $camposEditables = $user->getCamposEditablesAnimales();
            if (!empty($camposEditables)) {
                $data = array_intersect_key($data, array_flip($camposEditables));
            }
        }

        $this->fillAndSave($animale, $data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Animal actualizado correctamente.', 'animal' => $animale]);
        }

        return redirect()->route('admin.animales.index')->with('status', 'Animal actualizado correctamente.');
    }

    public function destroy(Request $request, Animal $animale)
    {
        $animale->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Animal eliminado correctamente.']);
        }

        return redirect()->route('admin.animales.index')->with('status', 'Animal eliminado correctamente.');
    }

    /**
     * Vista de la página de exportación.
     */
    public function exportacion()
    {
        return view('admin.animales.exportar');
    }

    /**
     * Retorna animales sin paginación para exportación.
     * Acepta ?ids=1,2,3 para seleccionados, o los mismos filtros del index.
     */
    public function exportar(Request $request)
    {
        $query = Animal::query();

        if ($request->filled('ids')) {
            $ids = array_map('intval', explode(',', $request->input('ids')));
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('agropecuaria')) {
                $query->where('agropecuaria', $request->input('agropecuaria'));
            }
            if ($request->filled('estado')) {
                $query->where('estado', $request->input('estado'));
            }
            if ($request->filled('estado_reproductivo')) {
                $query->where('estado_reproductivo', $request->input('estado_reproductivo'));
            }
            if ($request->filled('sexo')) {
                $query->where('sexo', $request->input('sexo'));
            }
            if ($request->filled('busqueda')) {
                $search = $request->input('busqueda');
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('codigo_practico', 'like', "%{$search}%")
                      ->orWhere('identificacion_electronica', 'like', "%{$search}%");
                });
            }
        }

        $query->orderBy('codigo_practico', 'asc');

        if ($request->filled('count_only')) {
            return response()->json(['count' => $query->count()]);
        }

        return response()->json($query->get());
    }

    /**
     * Valores únicos para los dropdowns de filtros.
     */
    public function filterOptions()
    {
        return response()->json([
            'agropecuarias' => Animal::distinct()->whereNotNull('agropecuaria')->pluck('agropecuaria')->sort()->values(),
            'estados' => Animal::distinct()->whereNotNull('estado')->pluck('estado')->sort()->values(),
            'estados_reproductivos' => Animal::distinct()->whereNotNull('estado_reproductivo')->pluck('estado_reproductivo')->sort()->values(),
            'sexos' => Animal::distinct()->whereNotNull('sexo')->pluck('sexo')->sort()->values(),
        ]);
    }

    protected function validateData(Request $request, bool $isCreate, ?int $ignoreId = null): array
    {
        $uniqueRule = $isCreate
            ? 'unique:animales,identificacion_electronica'
            : 'unique:animales,identificacion_electronica,' . $ignoreId;

        return $request->validate([
            'agropecuaria' => ['required', 'string', 'max:255'],
            'codigo_practico' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', 'max:255'],
            'identificacion_electronica' => ['nullable', 'string', 'max:20', $uniqueRule],
            'fecha_nacimiento' => ['nullable', 'date'],
            'padre_nombre' => ['nullable', 'string', 'max:255'],
            'codigo_madre' => ['nullable', 'string', 'max:255'],
            'ultima_locacion' => ['nullable', 'string', 'max:255'],
            'composicion_racial' => ['nullable', 'string', 'max:255'],
            'clasificacion_asociacion' => ['nullable', 'string', 'max:255'],
            'ultimo_peso' => ['nullable', 'numeric', 'min:0'],
            'estandarizacion_produccion' => ['nullable', 'string', 'max:255'],
            'fecha_ultimo_servicio' => ['nullable', 'date'],
            'estado_reproductivo' => ['nullable', 'string', 'max:255'],
            'numero_revisiones' => ['nullable', 'integer', 'min:0'],
            'fecha_ultimo_parto' => ['nullable', 'date', 'before_or_equal:today'],
            'fecha_secado' => ['nullable', 'date', 'before_or_equal:today'],
            'nombre' => ['nullable', 'string', 'max:255'],
            'sexo' => ['nullable', 'string', 'in:Hembra,Macho'],
            'codigo_reproductor' => ['nullable', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:255'],
            'codigo_nombre' => ['nullable', 'string', 'max:255'],
        ]);
    }

    protected function fillAndSave(Animal $animal, array $data): void
    {
        $animal->fill($data);
        $animal->save();
    }
}
