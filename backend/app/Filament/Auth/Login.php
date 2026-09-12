<?php

namespace App\Filament\Auth;

use App\Models\SiteSetting;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable|null
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;

        return "{$chapterName} admin";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Sign in to manage members, events, and site content.';
    }
}
