<?php

namespace Micromagicman\TelegramWebApp\Dto;

/**
 * Description of Telegram WebApp user
 */
class TelegramUser
{
    /**
     * Identifier (chat id)
     * @var int
     */
    private int $id;

    private string $first_name;

    private string $last_name;

    private string $username;

    private string $language_code;

    private bool $is_premium;

    private bool $allows_write_to_pm;

    public function __construct( array $telegramUserData )
    {
        foreach ( $telegramUserData as $key => $value ) {
            $this->{$key} = $value;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->language_code;
    }

    /**
     * @return bool
     */
    public function isPremium(): bool
    {
        return $this->is_premium;
    }

    /**
     * @return bool
     */
    public function isAllowsWriteToPm(): bool
    {
        return $this->allows_write_to_pm;
    }
}