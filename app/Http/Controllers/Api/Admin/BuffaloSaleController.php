<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuffaloBreed;
use App\Models\BuffaloSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BuffaloSaleController extends Controller
{
    public function index()
    {
        return response()->json(
            BuffaloSale::with('breed')->orderByDesc('created_at')->paginate(15)
        );
    }

    public function breeds()
    {
        return response()->json(
            BuffaloBreed::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request, true);

        $sale = new BuffaloSale();
        $this->fillAndSave($sale, $request, $data);

        return response()->json($sale->load('breed'), 201);
    }

    public function show(BuffaloSale $sale)
    {
        return response()->json($sale->load('breed'));
    }

    public function update(Request $request, BuffaloSale $sale)
    {
        $data = $this->validateData($request, false);
        $this->fillAndSave($sale, $request, $data);

        return response()->json($sale->load('breed'));
    }

    public function destroy(BuffaloSale $sale)
    {
        if ($sale->photo_path) {
            Storage::disk('public')->delete($sale->photo_path);
        }

        $sale->delete();

        return response()->json(['ok' => true]);
    }

    protected function validateData(Request $request, bool $isCreate): array
    {
        return $request->validate([
            'code' => [
                'required', 'string', 'max:30',
                $isCreate
                    ? 'unique:buffalo_sales,code'
                    : 'unique:buffalo_sales,code,' . $request->route('sale')?->id,
            ],
            'status'              => ['required', 'in:available,reserved,sold'],
            'price_crc'           => ['required', 'integer', 'min:0'],
            'age_years'           => ['nullable', 'integer', 'min:0', 'max:40'],
            'weight_kg'           => ['nullable', 'numeric', 'min:0', 'max:5000'],
            'breed_id'            => ['nullable', 'integer', 'exists:buffalo_breeds,id'],
            'sex'                 => ['required', 'in:female,male'],
            'father_breed_id'     => ['nullable', 'integer', 'exists:buffalo_breeds,id'],
            'mother_breed_id'     => ['nullable', 'integer', 'exists:buffalo_breeds,id'],
            'reproductive_status' => ['required', 'in:empty,pregnant,producing'],
            'gestation_months'    => ['nullable', 'integer', 'min:0', 'max:12'],
            'births_count'        => ['nullable', 'integer', 'min:0', 'max:30'],
            'milk_production'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vaccines_up_to_date' => ['sometimes', 'boolean'],
            'feeding_type'        => ['required', 'in:grazing,supplement,mixed'],
            'animal_condition'    => ['required', 'in:excellent,good,regular'],
            'phone'               => ['required', 'regex:/^\+58\d{10}$/'],
            'photo_file'          => [$isCreate ? 'required' : 'nullable', 'image', 'max:6144'],
        ], [
            'phone.regex'  => 'El teléfono debe estar en formato +58 seguido de 10 dígitos.',
            'code.unique'  => 'Este código de animal ya está registrado.',
        ]);
    }

    protected function fillAndSave(BuffaloSale $sale, Request $request, array $data): void
    {
        $sale->code               = $data['code'];
        $sale->status             = $data['status'];
        $sale->price_crc          = (int) $data['price_crc'];
        $sale->age_years          = isset($data['age_years']) ? (int) $data['age_years'] : null;
        $sale->weight_kg          = $data['weight_kg'] ?? null;
        $sale->breed_id           = isset($data['breed_id']) ? (int) $data['breed_id'] : null;
        $sale->sex                = $data['sex'];
        $sale->father_breed_id    = isset($data['father_breed_id']) ? (int) $data['father_breed_id'] : null;
        $sale->mother_breed_id    = isset($data['mother_breed_id']) ? (int) $data['mother_breed_id'] : null;
        $sale->reproductive_status = $data['reproductive_status'];
        $sale->gestation_months   = isset($data['gestation_months']) ? (int) $data['gestation_months'] : null;
        $sale->births_count       = isset($data['births_count']) ? (int) $data['births_count'] : null;
        $sale->milk_production    = $data['milk_production'] ?? null;
        $sale->vaccines_up_to_date = $request->boolean('vaccines_up_to_date');
        $sale->feeding_type       = $data['feeding_type'];
        $sale->animal_condition   = $data['animal_condition'];
        $sale->phone              = $data['phone'];
        $sale->is_active          = $request->boolean('is_active', true);

        if ($request->hasFile('photo_file')) {
            if ($sale->photo_path) {
                Storage::disk('public')->delete($sale->photo_path);
            }

            $file     = $request->file('photo_file');
            $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('uploads/sales', $filename, 'public');
            $sale->photo_path = $path;
        }

        $sale->save();
    }
}
