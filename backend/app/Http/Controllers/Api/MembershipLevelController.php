<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipLevel;
use Illuminate\Http\JsonResponse;

class MembershipLevelController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            MembershipLevel::active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(fn (MembershipLevel $level): array => [
                    'id' => (string) $level->id,
                    'name' => $level->name,
                    'description' => $level->description,
                    'subscriptionAmount' => $level->subscription_amount,
                    'welfareAmount' => $level->welfare_amount,
                ])
        );
    }
}
