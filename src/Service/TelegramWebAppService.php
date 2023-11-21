<?php

namespace Micromagicman\TelegramWebApp\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use Micromagicman\TelegramWebApp\Util\CryptoUtils;

/**
 * Telegram MiniApp service functions
 */
class TelegramWebAppService
{
    /**
     * A name of hash query parameter from Telegram
     */
    private const HASH_QUERY_PARAMETER_KEY = 'hash';

    /**
     * A name of user query parameter from Telegram
     */
    private const USER_QUERY_PARAMETER_KEY = 'user';

    /**
     * A key for hashing a key that will be used to calculate the hash from the data received via Telegram MiniApp
     */
    private const SHA256_TOKEN_HASH_KEY = 'WebAppData';

    public function abortWithError( array $errorMessageParams = [] ): void
    {
        $errorMessage = config( 'telegram-webapp.error.message' );
        $statusCode = config( 'telegram-webapp.error.status' );
        abort( $statusCode, __( $errorMessage, $errorMessageParams ) );
    }

    /**
     * Validate data received via the Mini App, one should send the data from the Telegram.WebApp.initData field.
     * The data is a query string, which is composed of a series of field-value pairs.
     */
    public function verifyInitData( ?Request $request = null ): bool
    {
        $queryParams = $request->query();
        if ( !array_key_exists( self::HASH_QUERY_PARAMETER_KEY, $queryParams ) ) {
            return false;
        }
        $requestHash = $queryParams[ self::HASH_QUERY_PARAMETER_KEY ];
        $hashFromQueryString = $this->createHashFromQueryString( $queryParams );
        return $requestHash === $hashFromQueryString;
    }

    /**
     * Get Telegram User who sent request
     */
    public function getWebAppUser( ?Request $request = null ): ?TelegramUser
    {
        $requestQuery = !$request ? \Illuminate\Support\Facades\Request::query() : $request->query();
        if ( !array_key_exists( self::USER_QUERY_PARAMETER_KEY, $requestQuery ) ) {
            return null;
        }
        $telegramUserData = json_decode( $requestQuery[ self::USER_QUERY_PARAMETER_KEY ], JSON_OBJECT_AS_ARRAY );
        if ( JSON_ERROR_NONE !== json_last_error() ) {
            Log::error( "Error parsing Telegram WebApp user data from json", [ $requestQuery ] );
            return null;
        }
        return new TelegramUser( $telegramUserData );
    }

    /**
     * Verify integrity of the data received by comparing the received hash parameter with the hexadecimal
     * representation of the HMAC-SHA-256 signature of the data-check-string with the secret key, which is the
     * HMAC-SHA-256 signature of the bot's token with the constant string WebAppData used as a key.
     */
    private function createHashFromQueryString( array $queryParams ): string
    {
        $telegramBotToken = config( 'telegram-webapp.botToken' );
        $dataDigestKey = CryptoUtils::hmacSHA256( $telegramBotToken, self::SHA256_TOKEN_HASH_KEY, true );
        $dataWithoutHash = array_filter(
            $queryParams,
            fn( $key ) => $key !== self::HASH_QUERY_PARAMETER_KEY,
            ARRAY_FILTER_USE_KEY
        );
        ksort( $dataWithoutHash );
        $dataCheckString = implode( // 3 * O(N) ~ O(N)
            PHP_EOL,
            array_map(
                fn( string $key, string $value ) => "$key=$value",
                array_keys( $dataWithoutHash ),
                $dataWithoutHash
            )
        );
        return CryptoUtils::hmacSHA256( $dataCheckString, $dataDigestKey );
    }
}