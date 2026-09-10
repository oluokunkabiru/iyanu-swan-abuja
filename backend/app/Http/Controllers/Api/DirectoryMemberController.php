<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DirectoryMemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $viewerIsMember = $request->user() !== null;

        return response()->json(
            MemberProfile::query()
                ->with('user:id,name')
                ->where('is_directory_listed', true)
                ->where('membership_status', 'active')
                ->whereNotNull('membership_number')
                ->orderBy('year_admitted')
                ->orderBy('id')
                ->get()
                ->map(function (MemberProfile $profile) use ($viewerIsMember): array {
                    $public = [
                        'id' => (string) $profile->id,
                        'name' => $profile->user->name,
                        'credential' => $profile->credential,
                        'chapterRole' => $profile->chapter_role,
                        'sector' => $profile->sector,
                        'photoUrl' => $profile->photo_url,
                    ];

                    if (! $viewerIsMember) {
                        return $public;
                    }

                    return [
                        ...$public,
                        'membershipNumber' => $profile->membership_number,
                        'specialisation' => $profile->specialisation,
                        'yearAdmitted' => $profile->year_admitted,
                    ];
                })
        );
    }
}
