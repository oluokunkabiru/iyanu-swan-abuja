<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Validation\Rules\Password;

class PasswordPolicy
{
    /** @return array<int, mixed> */
    public function rules(bool $confirmed = false, bool $required = true): array
    {
        $settings = SiteSetting::current();
        $rule = Password::min(max(8, (int) $settings->password_min_length));

        if ($settings->password_require_mixed_case) {
            $rule->mixedCase();
        }

        if ($settings->password_require_numbers) {
            $rule->numbers();
        }

        if ($settings->password_require_symbols) {
            $rule->symbols();
        }

        return [
            $required ? 'required' : 'nullable',
            'string',
            ...($confirmed ? ['confirmed'] : []),
            $rule,
        ];
    }

    /** @return array{minLength: int, requireMixedCase: bool, requireNumbers: bool, requireSymbols: bool} */
    public function details(): array
    {
        $settings = SiteSetting::current();

        return [
            'minLength' => max(8, (int) $settings->password_min_length),
            'requireMixedCase' => (bool) $settings->password_require_mixed_case,
            'requireNumbers' => (bool) $settings->password_require_numbers,
            'requireSymbols' => (bool) $settings->password_require_symbols,
        ];
    }
}
