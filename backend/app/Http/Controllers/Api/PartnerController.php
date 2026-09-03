<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;

class PartnerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Partner::query()->orderBy('sort_order')->get()
                ->map(fn (Partner $partner): array => [
                    'id' => (string) $partner->id,
                    'name' => $partner->name,
                    'url' => $partner->url ?? '',
                    'scope' => $partner->scope ?? 'Affiliate',
                ])
        );
    }
}
