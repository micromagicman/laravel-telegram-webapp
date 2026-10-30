<?php

namespace Micromagicman\TelegramWebApp\Dto;

/**
 * Description of Telegram WebApp user.
 *
 * Optional fields are nullable and default to null when Telegram omits them.
 */
class TelegramUser
{
    /**
     * A unique identifier for the user.
     */
    private int $id;

    /**
     * True, if this user is a bot.
     */
    private ?bool $is_bot;

    /**
     * First name of the user.
     */
    private string $first_name;

    /**
     * Last name of the user.
     */
    private ?string $last_name;

    /**
     * Username of the user.
     */
    private ?string $username;

    /**
     * Telegram user's current language as 2-char code.
     */
    private ?string $language_code;

    /**
     * True, if this user is a Telegram Premium user.
     */
    private ?bool $is_premium;

    /**
     * True, if this user added the bot to the attachment menu.
     */
    private ?bool $added_to_attachment_menu;

    /**
     * True, if this user allowed the bot to message them.
     */
    private ?bool $allows_write_to_pm;

    /**
     * URL of the user's profile photo.
     */
    private ?string $photo_url;

    public function __construct(array $telegramUserData)
    {
        $this->id = $telegramUserData['id'];
        $this->is_bot = $telegramUserData['is_bot'] ?? null;
        $this->first_name = $telegramUserData['first_name'];
        $this->last_name = $telegramUserData['last_name'] ?? null;
        $this->username = $telegramUserData['username'] ?? null;
        $this->language_code = $telegramUserData['language_code'] ?? null;
        $this->is_premium = $telegramUserData['is_premium'] ?? null;
        $this->added_to_attachment_menu = $telegramUserData['added_to_attachment_menu'] ?? null;
        $this->allows_write_to_pm = $telegramUserData['allows_write_to_pm'] ?? null;
        $this->photo_url = $telegramUserData['photo_url'] ?? null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isBot(): ?bool
    {
        return $this->is_bot;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getLanguageCode(): ?string
    {
        return $this->language_code;
    }

    public function isPremium(): ?bool
    {
        return $this->is_premium;
    }

    public function isAddedToAttachmentMenu(): ?bool
    {
        return $this->added_to_attachment_menu;
    }

    public function isAllowsWriteToPm(): ?bool
    {
        return $this->allows_write_to_pm;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photo_url;
    }
}
