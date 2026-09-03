<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExecutiveMember;
use Illuminate\Http\JsonResponse;

class ExecutiveMemberController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            ExecutiveMember::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
        );
    }
}
