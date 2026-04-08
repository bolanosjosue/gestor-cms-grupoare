<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index()
    {
        return response()->json(
            Staff::orderBy('sort_order')->orderBy('id')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'role'       => ['required', 'string', 'max:255'],
            'photo_url'  => ['nullable', 'url', 'regex:/^https?:\\/\\//i'],
            'photo_file' => ['nullable', 'image', 'max:4096'],
            'photo_alt'  => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['sometimes', 'boolean'],
        ]);

        $staff = new Staff();
        $this->fillAndSave($staff, $request, $data);

        return response()->json($staff, 201);
    }

    public function update(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'role'       => ['required', 'string', 'max:255'],
            'photo_url'  => ['nullable', 'url', 'regex:/^https?:\\/\\//i'],
            'photo_file' => ['nullable', 'image', 'max:4096'],
            'photo_alt'  => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['sometimes', 'boolean'],
        ]);

        $this->fillAndSave($staff, $request, $data);

        return response()->json($staff);
    }

    public function destroy(Staff $staff)
    {
        if ($staff->photo_path) {
            Storage::disk('public')->delete($staff->photo_path);
        }

        $staff->delete();

        return response()->json(['ok' => true]);
    }

    protected function fillAndSave(Staff $staff, Request $request, array $data): void
    {
        $staff->name       = $data['name'];
        $staff->role       = $data['role'];
        $staff->photo_alt  = $data['photo_alt'] ?? null;
        $staff->sort_order = $data['sort_order'] ?? ($staff->sort_order ?? 0);
        $staff->is_active  = $request->boolean('is_active', true);

        if ($request->hasFile('photo_file')) {
            if ($staff->photo_path) {
                Storage::disk('public')->delete($staff->photo_path);
            }

            $file     = $request->file('photo_file');
            $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('uploads/staff', $filename, 'public');

            $staff->photo_path = $path;
            $staff->photo_url  = null;
        } elseif (! empty($data['photo_url'])) {
            if ($staff->photo_path) {
                Storage::disk('public')->delete($staff->photo_path);
            }
            $staff->photo_url  = $data['photo_url'];
            $staff->photo_path = null;
        }

        $staff->save();
    }
}
