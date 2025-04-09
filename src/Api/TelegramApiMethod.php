<?php

namespace Micromagicman\TelegramWebApp\Api;

use UnexpectedValueException;

/**
 * Abstract Telegram API method.
 * @deprecated in favor {@link TelegramWebAppService TelegramBot\Api proxy}.
 */
abstract class TelegramApiMethod
{

    /**
     * Method name
     * By default, it's class name in lowerUpperCase
     */
    public function methodName(): string
    {
        $fullClassName = get_called_class();
        $lastBackslashIndex = strrpos( $fullClassName, '\\' );
        if ( -1 === $lastBackslashIndex ) {
            throw new UnexpectedValueException( "Cannot resolve method name from it\'s class ($fullClassName)" );
        }
        $className = substr( $fullClassName, $lastBackslashIndex + 1 );
        return strtolower( $className[ 0 ] ) . substr( $className, 1 );
    }

    /**
     * Request body for API method
     */
    public abstract function body(): array;
}