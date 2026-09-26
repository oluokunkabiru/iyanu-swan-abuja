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
                ->where('is_ex_officio', false)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn (ExecutiveMember $member): array => $this->present($member))
        );
    }

    public function chairperson(): JsonResponse
    {
        $chairperson = ExecutiveMember::query()
            ->with('media')
            ->where('is_active', true)
            ->where('is_chairperson', true)
            ->first();

        return response()->json($chairperson instanceof ExecutiveMember ? $this->present($chairperson) : null);
    }

    public function pastChairpersons(): JsonResponse
    {
        return response()->json(
            ExecutiveMember::query()
                ->with('media')
                ->where('position', 'Chairperson')
                ->where(function ($query): void {
                    $query
                        ->where('is_active', false)
                        ->orWhere('is_ex_officio', true);
                })
                ->orderByDesc('term_start_year')
                ->orderByDesc('id')
                ->get()
                ->map(fn (ExecutiveMember $member): array => $this->present($member))
        );
    }

    /** @return array<string, mixed> */
    private function present(ExecutiveMember $member): array
    {
        return [
            'id' => (string) $member->id,
            'name' => $member->name,
            'credential' => $member->credential,
            'position' => $member->position,
            'bio' => $member->bio ?? '',
            'photoUrl' => $member->photo_url,
            'isPrincipal' => $member->is_principal,
            'isChairperson' => $member->is_chairperson,
            'isExOfficio' => $member->is_ex_officio,
            'termStartYear' => $member->term_start_year,
            'termEndYear' => $member->term_end_year,
        ];
    }
}
