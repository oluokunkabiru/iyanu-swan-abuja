<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
            'date_of_birth' => ['nullable', 'date', 'before:today'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'member',
        ]);

        $user->memberProfile()->create([
            'phone' => $data['phone'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'membership_status' => 'pending',
        ]);

        $user->sendEmailVerificationNotification();

        Auth::login($user);
        $request->session()->regenerate();

        $user->load('memberProfile');

        return response()->json($this->userPayload($user), 201);
    }

    /**
     * Landing point for the signed link in the verification email. Not
     * behind auth:sanctum — the browser opening it may not carry this
     * app's session — so identity comes from the id/hash pair alone,
     * which the "signed" middleware guarantees hasn't been tampered with.
     */
    public function verifyEmail(int $id, string $hash): RedirectResponse
    {
        $user = User::findOrFail($id);
        $frontendUrl = rtrim(config('app.frontend_url'), '/');

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect("{$frontendUrl}/email/verified?status=invalid");
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $user->activateMembershipIfEligible();
        }

        return redirect("{$frontendUrl}/email/verified?status=success");
    }

    public function resendVerification(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email is already verified.'], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);
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

        if (($request->has('personal_email') || $request->has('official_email')) && ! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Verify your registered email before adding another one.',
            ], 422);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'is_directory_listed' => ['sometimes', 'boolean'],
            'photo' => ['sometimes', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
            'personal_email' => ['sometimes', 'nullable', 'email', 'max:255', 'different:email', Rule::unique('users', 'personal_email')->ignore($user->id)],
            'official_email' => ['sometimes', 'nullable', 'email', 'max:255', 'different:email', Rule::unique('users', 'official_email')->ignore($user->id)],
            'notification_email_preference' => ['sometimes', 'nullable', Rule::in(['registered', 'personal', 'official', 'all'])],
        ]);

        $user->fill([
            'name' => $data['name'] ?? $user->name,
            ...array_intersect_key($data, array_flip(['personal_email', 'official_email', 'notification_email_preference'])),
        ])->save();

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
            'emailVerified' => $user->hasVerifiedEmail(),
            'personalEmail' => $user->personal_email,
            'officialEmail' => $user->official_email,
            'notificationEmailPreference' => $user->notification_email_preference,
            'credential' => $profile?->credential ?? 'ACA',
            'membershipNumber' => $profile?->membership_number ?? '',
            'membershipStatus' => $profile?->membership_status ?? 'pending',
            'role' => $user->role,
            'joinedAt' => $profile?->joined_at?->toDateString(),
            'cpdTarget' => $profile?->cpd_target ?? 120,
            'photoUrl' => $profile?->photo_url,
            'isDirectoryListed' => $profile?->is_directory_listed ?? false,
            'dateOfBirth' => $profile?->date_of_birth?->toDateString(),
            'phone' => $profile?->phone,
            'sector' => $profile?->sector,
            'specialisation' => $profile?->specialisation,
            'yearAdmitted' => $profile?->year_admitted,
            'chapterRole' => $profile?->chapter_role,
        ];
    }
}
