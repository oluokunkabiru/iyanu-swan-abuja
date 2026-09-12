<?php

namespace App\Filament\Auth;

use App\Models\SiteSetting;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected string $view = 'filament.auth.login';

    public function getHeading(): string|Htmlable|null
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;

        return "{$chapterName} admin";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Sign in to manage members, events, and site content.';
    }

    /** @return string[] */
    public function getHighlights(): array
    {
        return [
            'Track membership, dues, and event registrations in one place',
            'Publish news, events, and site content the moment it\'s ready',
            'Review payments and bank-transfer evidence before they\'re confirmed',
            'Decide exactly what each admin can do, with role-based permissions',
        ];
    }
}
