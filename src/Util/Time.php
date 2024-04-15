<?php

namespace Micromagicman\TelegramWebApp\Util;

/**
 * Time management service
 */
class Time
{

    /**
     * Current {@link https://en.wikipedia.org/wiki/Unix_time unixtime} in seconds
     */
    public function now(): int
    {
        return time();
    }

    /**
     * Checking that the provided unixtime has passed
     */
    public function expired( int $unixTime ): int
    {
        return $unixTime < $this->now();
    }
}