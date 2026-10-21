<?php

namespace App\Services;

use App\Models\MemberProfile;

/**
 * Parses ICAN membership numbers the way members actually type them —
 * "MB040071", "mb 40071", "40071" — into a letter prefix and a
 * six-digit, zero-padded number, so one member can't register twice
 * under different spellings of the same number.
 */
class IcanMembershipNumber
{
    public const DEFAULT_PREFIX = 'MB';

    public const DIGITS = 6;

    /**
     * @return array{prefix: ?string, digits: string}|null null when the input has no digits
     */
    public function parse(string $input): ?array
    {
        $compact = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $input) ?? '');

        if (! preg_match('/^([A-Z]*)(\d+)$/', $compact, $matches)) {
            return null;
        }

        $digits = ltrim($matches[2], '0');

        if (strlen($digits) > self::DIGITS) {
            return null;
        }

        return [
            'prefix' => $matches[1] !== '' ? $matches[1] : null,
            'digits' => str_pad($digits, self::DIGITS, '0', STR_PAD_LEFT),
        ];
    }

    /**
     * The canonical stored form, e.g. "MB040071". Only digits given →
     * the standard ICAN "MB" prefix.
     */
    public function normalize(string $input): ?string
    {
        $parsed = $this->parse($input);

        return $parsed ? ($parsed['prefix'] ?? self::DEFAULT_PREFIX).$parsed['digits'] : null;
    }

    /**
     * Matches on the number alone when no prefix was typed, and on
     * prefix + number when one was. Stored numbers are parsed the same
     * way, so older records saved as "ICAN/017234" or "40071" still match.
     */
    public function findProfile(string $input): ?MemberProfile
    {
        $parsed = $this->parse($input);

        if (! $parsed) {
            return null;
        }

        $significantDigits = ltrim($parsed['digits'], '0');

        return MemberProfile::query()
            ->with('user')
            ->where('membership_number', 'like', '%'.$significantDigits)
            ->get()
            ->first(function (MemberProfile $profile) use ($parsed): bool {
                $stored = $this->parse((string) $profile->membership_number);

                return $stored !== null
                    && $stored['digits'] === $parsed['digits']
                    && ($parsed['prefix'] === null || $stored['prefix'] === null || $stored['prefix'] === $parsed['prefix']);
            });
    }
}
