<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Firm;
use Illuminate\Http\JsonResponse;

class FirmController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Firm::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->orderBy('id')
                ->get()
                ->map(fn (Firm $firm): array => [
                    'id' => (string) $firm->id,
                    'name' => $firm->name,
                    'principal' => $firm->principal,
                    'licenceNumber' => $firm->licence_number,
                    'services' => $firm->services,
                    'area' => $firm->area,
                    'licenceStatus' => $firm->licence_status,
                ])
        );
    }
}
