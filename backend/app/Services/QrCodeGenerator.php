<?php

namespace App\Services;

use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Thin wrapper around chillerlan/php-qrcode (already a transitive
 * dependency via filament/filament, which uses it for 2FA setup codes) so
 * the rest of the app doesn't need to know its option-array API.
 */
class QrCodeGenerator
{
    /**
     * Render $data as a QR code and return raw PNG bytes (not a base64
     * data URI), suitable for attaching to an email or writing to disk.
     */
    public static function png(string $data): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'imageBase64' => false,
            'scale' => 6,
        ]);

        return (new QRCode($options))->render($data);
    }
}
