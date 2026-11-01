<?php

namespace Micromagicman\TelegramWebApp\Dto;

/**
 * Description of Telegram chat from which the Mini App was opened.
 */
class TelegramChat
{
    private int $id;

    private string $type;

    private ?string $title;

    private ?string $username;

    private ?string $photo_url;

    public function __construct(array $telegramChatData)
    {
        $this->id = $telegramChatData['id'];
        $this->type = $telegramChatData['type'];
        $this->title = $telegramChatData['title'] ?? null;
        $this->username = $telegramChatData['username'] ?? null;
        $this->photo_url = $telegramChatData['photo_url'] ?? null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photo_url;
    }
}
