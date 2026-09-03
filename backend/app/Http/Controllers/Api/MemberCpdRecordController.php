<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CpdRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberCpdRecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()
                ->cpdRecords()
                ->orderByDesc('activity_date')
                ->orderByDesc('id')
                ->get()
                ->map(fn (CpdRecord $record): array => [
                    'id' => (string) $record->id,
                    'activity' => $record->activity,
                    'date' => $record->activity_date->toDateString(),
                    'hours' => (float) $record->hours,
                    'type' => $record->activity_type,
                    'verified' => $record->is_verified,
                ])
        );
    }
}
