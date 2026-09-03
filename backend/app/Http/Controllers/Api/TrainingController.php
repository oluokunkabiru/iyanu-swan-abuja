<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\JsonResponse;

class TrainingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Training::query()
                ->where('is_active', true)
                ->orderBy('starts_on')
                ->orderBy('id')
                ->get()
                ->map(fn (Training $training): array => [
                    'id' => (string) $training->id,
                    'title' => $training->title,
                    'provider' => $training->provider,
                    'deliveryMode' => $training->delivery_mode,
                    'date' => $training->starts_on->toDateString(),
                    'cpdHours' => $training->cpd_hours,
                    'fee' => $training->fee,
                    'memberFee' => $training->member_fee,
                    'seatsLeft' => $training->seats_available,
                ])
        );
    }
}
