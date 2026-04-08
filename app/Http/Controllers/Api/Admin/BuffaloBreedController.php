<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuffaloBreed;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BuffaloBreedController extends Controller
{
    public function index()
    {
        return response()->json(
            BuffaloBreed::orderBy('name')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:buffalo_breeds,name'],
        ]);

        $breed = BuffaloBreed::create([
            'name' => $request->input('name'),
        ]);

        return response()->json($breed, 201);
    }

    public function update(Request $request, BuffaloBreed $breed)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('buffalo_breeds', 'name')->ignore($breed->id)],
        ]);

        $breed->update([
            'name' => $request->input('name'),
        ]);

        return response()->json($breed);
    }

    public function destroy(BuffaloBreed $breed)
    {
        if ($breed->sales()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: la raza está asociada a publicaciones de venta.',
            ], 422);
        }

        $breed->delete();

        return response()->json(['ok' => true]);
    }
}
