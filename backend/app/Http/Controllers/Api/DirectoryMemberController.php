<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use Illuminate\Http\JsonResponse;

class DirectoryMemberController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            MemberProfile::query()
                ->with('user:id,name')
                ->where('is_directory_listed', true)
                ->where('membership_status', 'active')
                ->whereNotNull('membership_number')
                ->orderBy('year_admitted')
                ->orderBy('id')
                ->get()
                ->map(fn (MemberProfile $profile): array => [
                    'id' => (string) $profile->id,
                    'name' => $profile->user->name,
                    'credential' => $profile->credential,
                    'membershipNumber' => $profile->membership_number,
                    'sector' => $profile->sector,
                    'specialisation' => $profile->specialisation,
                    'yearAdmitted' => $profile->year_admitted,
                    'chapterRole' => $profile->chapter_role,
                ])
        );
    }
}
