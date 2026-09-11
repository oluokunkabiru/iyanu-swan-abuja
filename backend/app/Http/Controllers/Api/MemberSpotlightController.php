<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberSpotlight;
use Illuminate\Http\JsonResponse;

class MemberSpotlightController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            MemberSpotlight::query()->orderBy('name')->get()
        );
    }
}
