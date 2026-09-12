<?php

namespace App\Services\Email;

/**
 * The one-time result of successfully creating a cPanel mailbox. The
 * password only ever exists here and in the welcome email — nothing
 * persists it, cPanel is the sole holder of the real credential afterward.
 */
readonly class ProvisionedMailbox
{
    public function __construct(
        public string $address,
        public string $password,
    ) {}
}
