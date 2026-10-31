<?php

namespace Micromagicman\TelegramWebApp\Util;

/**
 * Service with util methods for cryptography
 */
class Crypto
{
    /**
     * SHA256 hashing algorithm name for {@link hash_hmac} function
     */
    public const SHA_256_ALGORITHM = 'sha256';

    /**
     * Generate a SHA256 hash value
     *
     * @param  bool  $binary  - When set to true, outputs raw binary data. false outputs lowercase hexits
     */
    public function hmacSHA256(string $plainText, string $key, bool $binary = false): string
    {
        return hash_hmac(self::SHA_256_ALGORITHM, $plainText, $key, $binary);
    }

    /**
     * Verify an Ed25519 signature over a message using a hex-encoded public key.
     *
     * The signature is base64url-encoded and may omit trailing padding, as
     * Telegram emits it. Returns false (rather than throwing) on any malformed
     * input so callers can treat it as a plain validation failure.
     *
     * @param  string  $message  - The data-check-string that was signed
     * @param  string  $signature  - base64url-encoded Ed25519 signature
     * @param  string  $publicKeyHex  - hex-encoded Ed25519 public key
     */
    public function verifyEd25519(string $message, string $signature, string $publicKeyHex): bool
    {
        try {
            $publicKey = sodium_hex2bin($publicKeyHex);
            $signatureBytes = $this->base64UrlDecode($signature);
            if ($signatureBytes === false) {
                return false;
            }

            return sodium_crypto_sign_verify_detached($signatureBytes, $message, $publicKey);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Decode a base64url string (which may omit `=` padding) to raw bytes.
     */
    private function base64UrlDecode(string $value): string|false
    {
        $base64 = strtr($value, '-_', '+/');
        $base64 = str_pad($base64, (int) ceil(strlen($base64) / 4) * 4, '=');

        return base64_decode($base64, true);
    }
}
