<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoreValue;
use Illuminate\Http\JsonResponse;

class CoreValueController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            CoreValue::query()
                ->orderBy('title')
                ->get()
                ->map(fn (CoreValue $value): array => [
                    'title' => $value->title,
                    'description' => $value->description ?? '',
                ])
        );
    }
}
