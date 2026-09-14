<?php

namespace App\Services\Email;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Provisions an official @-domain mailbox for a member via cPanel's UAPI
 * (Email::add_pop) the moment their membership activates. Deliberately
 * best-effort from the caller's side: a cPanel outage or naming collision
 * must never block membership activation itself — see
 * User::activateMembershipIfEligible(), which catches whatever this throws
 * and proceeds without an official email rather than failing the whole
 * activation.
 */
class CpanelEmailProvisioner
{
    private const MAX_USERNAME_ATTEMPTS = 20;

    public function isConfigured(): bool
    {
        return filled(config('services.cpanel.host'))
            && filled(config('services.cpanel.username'))
            && filled(config('services.cpanel.api_token'))
            && filled(config('services.cpanel.email_domain'));
    }

    /**
     * @throws RuntimeException if cPanel rejects every username attempt, or
     *                          the API call itself fails for a reason other
     *                          than the mailbox name already being taken
     */
    public function provisionFor(User $user): ProvisionedMailbox
    {
        $domain = config('services.cpanel.email_domain');
        $password = Str::password(20);
        $localPart = $this->baseLocalPart($user->name);

        for ($attempt = 0; $attempt < self::MAX_USERNAME_ATTEMPTS; $attempt++) {
            $candidate = $attempt === 0 ? $localPart : "{$localPart}{$attempt}";

            $result = $this->createMailbox($candidate, $domain, $password);

            if ($result['success']) {
                return new ProvisionedMailbox("{$candidate}@{$domain}", $password);
            }

            if (! $result['isCollision']) {
                throw new RuntimeException("cPanel rejected mailbox creation for [{$candidate}@{$domain}]: {$result['message']}");
            }
        }

        throw new RuntimeException(
            "Could not find a free mailbox name for [{$user->name}] after ".self::MAX_USERNAME_ATTEMPTS.' attempts.'
        );
    }

    /**
     * @return array{success: bool, isCollision: bool, message: string}
     */
    private function createMailbox(string $localPart, string $domain, string $password): array
    {
        $response = Http::connectTimeout(5)
            ->timeout(15)
            ->withHeaders([
                'Authorization' => 'cpanel '.config('services.cpanel.username').':'.config('services.cpanel.api_token'),
            ])
            ->asForm()
            ->post(
                sprintf(
                    'https://%s:%d/execute/Email/add_pop',
                    config('services.cpanel.host'),
                    config('services.cpanel.port'),
                ),
                [
                    'email' => $localPart,
                    'domain' => $domain,
                    'password' => $password,
                    'quota' => config('services.cpanel.quota_mb'),
                ],
            );

        $payload = $response->json();
        $result = is_array($payload)
            ? (is_array($payload['result'] ?? null) ? $payload['result'] : $payload)
            : [];
        $status = (int) ($result['status'] ?? 0);
        $errors = $result['errors'] ?? [];
        $message = is_array($errors) ? implode(' ', $errors) : (string) $errors;

        if (! $response->successful() || $status !== 1) {
            if ($message === '') {
                Log::warning('cPanel mailbox provisioning returned an unexpected response.', [
                    'http_status' => $response->status(),
                    'content_type' => $response->header('Content-Type'),
                    'response_keys' => is_array($payload) ? array_keys($payload) : [],
                ]);
            }

            return [
                'success' => false,
                'isCollision' => str_contains(strtolower($message), 'already exists'),
                'message' => $message !== '' ? $message : "Unexpected cPanel UAPI response (HTTP {$response->status()})",
            ];
        }

        return ['success' => true, 'isCollision' => false, 'message' => ''];
    }

    /**
     * "Jane Doe" -> "jane.doe"; a single-word name just becomes that word.
     * Slugged separately per part (not as one slug across the full name) so
     * a hyphenated surname doesn't collapse the dot separator.
     */
    private function baseLocalPart(string $name): string
    {
        $parts = array_values(array_filter(preg_split('/\s+/', trim($name)) ?: []));

        $first = Str::slug($parts[0] ?? 'member', '');
        $last = count($parts) > 1 ? Str::slug($parts[array_key_last($parts)], '') : null;

        $localPart = filled($last) ? "{$first}.{$last}" : $first;

        return $localPart !== '' ? $localPart : 'member';
    }
}
