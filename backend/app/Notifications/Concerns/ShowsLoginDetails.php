<?php

namespace App\Notifications\Concerns;

use Illuminate\Support\HtmlString;

trait ShowsLoginDetails
{
    /**
     * Sent as one block of HTML because mail text is parsed as Markdown,
     * which would swallow symbols such as * and _ and leave the member
     * with a password that doesn't work.
     */
    private function loginDetails(string $email, string $password, string $passwordLabel = 'Temporary password'): HtmlString
    {
        return new HtmlString(
            '<div style="background-color:#f3f3fa;border-left:4px solid #000066;padding:14px 18px;margin:0 0 20px;font-size:15px;line-height:1.8;color:#000066;">'
            .'<strong>Your login details</strong><br>'
            .'Email: '.e($email).'<br>'
            .e($passwordLabel).': <code style="font-size:15px;font-weight:bold;">'.e($password).'</code>'
            .'</div>'
        );
    }
}
