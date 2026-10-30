<?php

namespace Micromagicman\TelegramWebApp\Dto;

/**
 * Description of Telegram WebApp user
 */
class TelegramUser
{
    /**
     * A unique identifier for the user.
     */
    private int $id;

    /**
     * First name of the user.
     */
    private string $first_name;

    /**
     * Last name of the user.
     */
    private string $last_name;

    /**
     * Username of the user.
     */
    private string $username;

    /**
     * Telegram user's current language as 2-char code
     */
    private string $language_code;

    /**
     * true, if this user is a Telegram Premium user
     */
    private bool $is_premium;

    /**
     * true, if this user allowed the bot to message them
     */
    private bool $allows_write_to_pm;

    public function __construct(array $telegramUserData)
    {
        foreach ($telegramUserData as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getLanguageCode(): string
    {
        return $this->language_code;
    }

    public function isPremium(): bool
    {
        return $this->is_premium;
    }

    public function isAllowsWriteToPm(): bool
    {
        return $this->allows_write_to_pm;
    }
}
