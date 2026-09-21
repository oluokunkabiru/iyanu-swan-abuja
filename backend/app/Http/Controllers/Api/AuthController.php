<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Notifications\PasswordResetConfirmed;
use App\Services\PasswordPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => app(PasswordPolicy::class)->rules(confirmed: true),
            'membership_number' => ['required', 'string', 'max:255', 'unique:member_profiles,membership_number'],
            // "Credential" here is the member's ICAN level — one of the
            // chapter's admin-configured Membership Levels (e.g. ACA,
            // FCA, AATWA), not a fixed ACA/FCA enum.
            'credential' => ['required', Rule::in(MembershipLevel::active()->pluck('name'))],
            'phone' => ['required', 'string', 'max:50'],
            'residential_address' => ['required', 'string', 'max:1000'],
            'place_of_work' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
        ]);

        $user = User::create([
            'name' => Str::squish(implode(' ', [
                $data['first_name'],
                $data['middle_name'] ?? '',
                $data['last_name'],
            ])),
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'member',
        ]);

        $user->memberProfile()->create([
            'membership_number' => $data['membership_number'],
            'credential' => $data['credential'],
            'phone' => $data['phone'],
            'residential_address' => $data['residential_address'],
            'place_of_work' => $data['place_of_work'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'membership_status' => 'pending',
        ]);

        $user->sendEmailVerificationNotification();

        $user->load('memberProfile');

        /** @var string $accessToken */
        $accessToken = Auth::guard('api')->login($user);

        return response()->json($this->authenticationPayload($user, $accessToken), 201);
    }

    /**
     * Does the actual verifying for the link in the verification email —
     * called by the frontend page the link points to, not opened
     * directly, so this only ever needs to answer "did it work" as
     * JSON and leave the page/UX entirely to the frontend. Not behind
     * auth:api — the caller may not carry a valid JWT yet — so
     * identity comes from the id/hash pair alone, which the "signed"
     * middleware guarantees hasn't been tampered with.
     */
    public function verifyEmail(int $id, string $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'This verification link is invalid.'], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $user->activateMembershipIfEligible();
        }

        return response()->json(['message' => 'Email verified.']);
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

        /** @var User|null $user */
        $user = User::query()
            ->where('email', $credentials['email'])
            ->first()
            ?? User::query()->where('official_email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user->load('memberProfile');

        /** @var string $accessToken */
        $accessToken = Auth::guard('api')->login($user);

        return response()->json($this->authenticationPayload($user, $accessToken));
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink(['email' => $data['email']]);

        return response()->json([
            'message' => 'If an account matches that email address, we have sent a password-reset link.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => app(PasswordPolicy::class)->rules(confirmed: true),
        ]);

        $status = Password::reset(
            $data,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'must_change_password' => false,
                ])->save();

                $user->notify(new PasswordResetConfirmed);
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 422);
        }

        return response()->json(['message' => 'Your password has been reset. You can now sign in.']);
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Logged out']);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => app(PasswordPolicy::class)->rules(confirmed: true),
        ]);

        /** @var User $user */
        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => $data['password'],
            'must_change_password' => false,
        ]);

        Auth::guard('api')->logout();

        /** @var string $accessToken */
        $accessToken = Auth::guard('api')->login($user);

        return response()->json([
            'message' => 'Password changed successfully.',
            ...$this->authenticationPayload($user->fresh('memberProfile'), $accessToken),
        ]);
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
            'residential_address' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'place_of_work' => ['sometimes', 'nullable', 'string', 'max:255'],
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

        $profileData = array_intersect_key($data, array_flip([
            'phone', 'residential_address', 'place_of_work', 'date_of_birth', 'is_directory_listed',
        ]));

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
            'firstName' => $user->first_name ?? Str::before($user->name, ' '),
            'email' => $user->email,
            'emailVerified' => $user->hasVerifiedEmail(),
            'personalEmail' => $user->personal_email,
            'officialEmail' => $user->official_email,
            'notificationEmailPreference' => $user->notification_email_preference,
            'credential' => $profile?->credential ?? 'ACA',
            'membershipNumber' => $profile?->membership_number ?? '',
            'membershipStatus' => $profile?->membership_status ?? 'pending',
            'role' => $user->role,
            'mustChangePassword' => $user->must_change_password,
            'joinedAt' => $profile?->joined_at?->toDateString(),
            'cpdTarget' => $profile?->cpd_target ?? 120,
            'photoUrl' => $profile?->photo_url,
            'isDirectoryListed' => $profile?->is_directory_listed ?? false,
            'dateOfBirth' => $profile?->date_of_birth?->toDateString(),
            'phone' => $profile?->phone,
            'residentialAddress' => $profile?->residential_address,
            'placeOfWork' => $profile?->place_of_work,
            'sector' => $profile?->sector,
            'specialisation' => $profile?->specialisation,
            'yearAdmitted' => $profile?->year_admitted,
            'chapterRole' => $profile?->chapter_role,
        ];
    }

    /** @return array{accessToken: string, tokenType: string, expiresIn: int, user: array<string, mixed>} */
    private function authenticationPayload(User $user, string $accessToken): array
    {
        return [
            'accessToken' => $accessToken,
            'tokenType' => 'Bearer',
            'expiresIn' => (int) config('jwt.ttl') * 60,
            'user' => $this->userPayload($user),
        ];
    }
}
