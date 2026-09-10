<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'member',
        ]);

        $user->memberProfile()->create([
            'phone' => $data['phone'] ?? null,
            'membership_status' => 'pending',
        ]);

        $settings = SiteSetting::current();

        $user->subscriptions()->create([
            'year' => now()->year,
            'subscription_amount' => $settings->membership_subscription_fee,
            'welfare_amount' => $settings->membership_welfare_fee,
            'status' => 'outstanding',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $user->load('memberProfile');

        return response()->json($this->userPayload($user), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();
        $user->load('memberProfile');

        return response()->json($this->userPayload($user));
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->load('memberProfile');

        return response()->json($this->userPayload($user));
    }

    public function updateMe(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'is_directory_listed' => ['sometimes', 'boolean'],
            'photo' => ['sometimes', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ]);

        $user->fill(['name' => $data['name'] ?? $user->name])->save();

        $profileData = array_intersect_key($data, array_flip(['phone', 'date_of_birth', 'is_directory_listed']));

        if ($profileData !== []) {
            $user->memberProfile()->updateOrCreate([], $profileData);
        }

        if ($request->hasFile('photo')) {
            $user->memberProfile()->firstOrCreate([])
                ->addMediaFromRequest('photo')
                ->toMediaCollection('photo');
        }

        return response()->json($this->userPayload($user->fresh('memberProfile')));
    }

    public function registrations(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()
                ->eventRegistrations()
                ->with(['event:id,title,slug', 'ticketType:id,label'])
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->get()
                ->map(fn ($registration): array => [
                    'id' => (string) $registration->id,
                    'eventTitle' => $registration->event->title,
                    'eventSlug' => $registration->event->slug,
                    'tier' => $registration->ticketType->label,
                    'amount' => $registration->amount,
                    'reference' => $registration->reference,
                    'status' => in_array($registration->payment_status, ['paid', 'confirmed'], true)
                        ? 'Confirmed'
                        : 'Pending',
                    'issuedAt' => ($registration->issued_at ?? $registration->created_at)->toDateString(),
                ])
        );
    }

    /** @return array<string, mixed> */
    private function userPayload(User $user): array
    {
        $profile = $user->memberProfile;

        return [
            'id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'credential' => $profile?->credential ?? 'ACA',
            'membershipNumber' => $profile?->membership_number ?? '',
            'membershipStatus' => $profile?->membership_status ?? 'pending',
            'role' => $user->role,
            'joinedAt' => $profile?->joined_at?->toDateString(),
            'cpdTarget' => $profile?->cpd_target ?? 120,
            'photoUrl' => $profile?->photo_url,
            'isDirectoryListed' => $profile?->is_directory_listed ?? false,
            'dateOfBirth' => $profile?->date_of_birth?->toDateString(),
        ];
    }
}
