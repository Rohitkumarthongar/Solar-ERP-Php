<?php

$emails = env('TRUSTED_ADMIN_EMAILS');

// The local demo operator remains usable unless an explicit allowlist is supplied.
// Production never receives an implicit trusted identity.
if (($emails === null || $emails === '') && env('APP_ENV', 'production') !== 'production') {
    $emails = 'admin@solarerp.com';
}

return [
    'trusted_admin_emails' => array_values(array_filter(
        array_map('trim', explode(',', (string) $emails)),
        static fn (string $email): bool => $email !== ''
    )),
];
