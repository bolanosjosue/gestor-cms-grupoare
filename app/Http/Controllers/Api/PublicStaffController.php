<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;

class PublicStaffController extends Controller
{
    public function index()
    {
        $staff = Staff::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'id',
                'name',
                'role',
                'photo_url',
                'photo_path',
                'photo_alt',
                'sort_order',
            ]);

        return response()->json([
            'items' => $staff,
        ]);
    }
}

