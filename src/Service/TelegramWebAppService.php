<?php

namespace Micromagicman\TelegramWebApp\Service;

use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use Micromagicman\TelegramWebApp\Util\Crypto;
use Micromagicman\TelegramWebApp\Util\Time;
use TelegramBot\Api\BotApi;

/**
 * Telegram MiniApp service functions
 */
class TelegramWebAppService
{
    /**
     * A name of hash query parameter from Telegram
     */
    private const string HASH_QUERY_PARAMETER_KEY = 'hash';

    /**
     * A name of user query parameter from Telegram
     */
    private const string USER_QUERY_PARAMETER_KEY = 'user';

    /**
     * A name of auth_date query parameter from Telegram WebApp
     */
    private const string AUTH_DATE_QUERY_PARAMETER_KEY = 'auth_date';

    /**
     * A key for hashing a key that will be used to calculate the hash from the data received via Telegram MiniApp
     */
    private const string SHA256_TOKEN_HASH_KEY = 'WebAppData';

    /**
     * Default {@link https://core.telegram.org/bots/webapps#webappinitdata Telegram initData} auth_date lifetime
     * (seconds)
     */
    private const int DEFAULT_AUTH_DATE_LIFETIME = 0;

    public function __construct(
        private readonly BotApi $telegramBotApi,
        private readonly Crypto $crypto,
        private readonly Time   $time ) {}

    public function abortWithError( array $errorMessageParams = [] ): void
    {
        abort(
            webAppConfig( 'error.status' ),
            __( webAppConfig( 'error.message' ), $errorMessageParams )
        );
    }

    /**
     * Validate data received via the Mini App, one should send the data from the Telegram.WebApp.initData field.
     * The data is a query string, which is composed of a series of field-value pairs.
     */
    public function verifyInitData( ?Request $request = null ): bool
    {
        $queryParams = $request->query();
        if ( !$this->telegramInitDataValid( $queryParams ) ) {
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
     * Proxy for {@link BotApi telegram native bot api} methods.
     */
    public function __call( $method, $arguments )
    {
        if ( method_exists( $this->telegramBotApi, $method ) ) {
            return $this->telegramBotApi->{$method}( ...$arguments );
        }
        throw new BadMethodCallException( "Method $method does not exists in Telegram bot api" );
    }

    /**
     * Verify integrity of the data received by comparing the received hash parameter with the hexadecimal
     * representation of the HMAC-SHA-256 signature of the data-check-string with the secret key, which is the
     * HMAC-SHA-256 signature of the bot's token with the constant string WebAppData used as a key.
     */
    private function createHashFromQueryString( array $queryParams ): string
    {
        $dataDigestKey = $this->crypto->hmacSHA256( telegramToken(), self::SHA256_TOKEN_HASH_KEY, true );
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
        return $this->crypto->hmacSHA256( $dataCheckString, $dataDigestKey );
    }

    /**
     * Checking that {@link https://core.telegram.org/bots/webapps#webappinitdata Telegram initData} is valid
     */
    private function telegramInitDataValid( array $telegramInitData ): bool
    {
        return array_key_exists( self::USER_QUERY_PARAMETER_KEY, $telegramInitData )
            && !$this->authDateExpired( $telegramInitData[ self::AUTH_DATE_QUERY_PARAMETER_KEY ] );
    }

    /**
     * Checking that Telegram {@link https://core.telegram.org/bots/webapps#webappinitdata auth_date} parameter has expired
     * The lifetime of {@link https://core.telegram.org/bots/webapps#webappinitdata Telegram initData}
     * is set by the telegram-webapp.authDateLifetimeSeconds parameter
     */
    private function authDateExpired( int $authDate ): bool
    {
        $authDateLifetime = webAppConfig( 'authDateLifetimeSeconds', self::DEFAULT_AUTH_DATE_LIFETIME );
        if ( $authDateLifetime <= self::DEFAULT_AUTH_DATE_LIFETIME ) {
            return false;
        }
        return $this->time->expired( $authDate + $authDateLifetime );
    }
}