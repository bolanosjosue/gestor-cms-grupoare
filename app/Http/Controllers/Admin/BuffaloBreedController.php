<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuffaloBreed;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuffaloBreedController extends Controller
{
    public function index()
    {
        $breeds = BuffaloBreed::query()
            ->orderBy('name')
            ->get();

        return view('admin.breeds.index', compact('breeds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:buffalo_breeds,name'],
        ]);

        BuffaloBreed::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('admin.breeds.index')->with('status', 'Raza creada correctamente.');
    }

    public function update(Request $request, BuffaloBreed $breed)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('buffalo_breeds', 'name')->ignore($breed->id)],
        ]);

        $breed->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('admin.breeds.index')->with('status', 'Raza actualizada correctamente.');
    }

    public function destroy(BuffaloBreed $breed)
    {
        if ($breed->sales()->exists()) {
            return redirect()
                ->route('admin.breeds.index')
                ->with('status', 'No se puede eliminar: la raza está asociada a publicaciones de venta.');
        }

        $breed->delete();

        return redirect()->route('admin.breeds.index')->with('status', 'Raza eliminada correctamente.');
    }
}
