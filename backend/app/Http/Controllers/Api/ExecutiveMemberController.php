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
                ->with('media')
                ->where('is_active', true)
                ->orderBy('created_at')
                ->orderBy('id')
                ->get()
                ->map(fn (ExecutiveMember $member): array => [
                    'id' => (string) $member->id,
                    'name' => $member->name,
                    'credential' => $member->credential,
                    'position' => $member->position,
                    'bio' => $member->bio ?? '',
                    'photoUrl' => $member->photo_url,
                    'isPrincipal' => $member->is_principal,
                ])
        );
    }
}
