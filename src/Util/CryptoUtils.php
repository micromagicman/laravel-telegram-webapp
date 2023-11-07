<?php

namespace Micromagicman\TelegramWebApp\Util;

class CryptoUtils
{
    public const SHA_256_ALGORITHM = 'sha256';

    public static function hmacSHA256( string $plainText, string $key, bool $binary = false ): string
    {
        return hash_hmac( self::SHA_256_ALGORITHM, $plainText, $key, $binary );
    }
}