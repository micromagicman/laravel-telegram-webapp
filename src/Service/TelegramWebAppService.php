<?php

namespace Micromagicman\TelegramWebApp\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use Micromagicman\TelegramWebApp\Util\CryptoUtils;

class TelegramWebAppService
{
    private const HASH_QUERY_PARAMETER_KEY = 'hash';

    private const USER_QUERY_PARAMETER_KEY = 'user';

    private const SHA256_TOKEN_HASH_KEY = 'WebAppData';

    private string $telegramBotToken;

    private int $errorStatusCode;

    private string $errorMessage;

    public function __construct()
    {
        $this->telegramBotToken = config( 'telegram-webapp.botToken' );
        $this->errorStatusCode = config( 'telegram-webapp.error.status' );
        $this->errorMessage = config( 'telegram-webapp.error.message' );
    }

    public function abortWithError( array $errorMessageParams = [] ): void
    {
        abort( $this->errorStatusCode, __( $this->errorMessage, $errorMessageParams ) );
    }

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
        $dataDigestKey = CryptoUtils::hmacSHA256( $this->telegramBotToken, self::SHA256_TOKEN_HASH_KEY, true );
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