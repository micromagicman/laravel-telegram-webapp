<?php

namespace Micromagicman\TelegramWebApp\Dto;

/**
 * Full Telegram WebApp initData, mapped into typed fields.
 *
 * Mirrors the fields of the Telegram WebApp `initData` query string.
 * Only `user` is guaranteed to be present for a Mini App opened by the
 * current user; the remaining fields are null when Telegram omits them.
 */
class TelegramWebAppData
{
    public function __construct(
        private readonly ?string $query_id,
        private readonly ?TelegramUser $user,
        private readonly ?TelegramUser $receiver,
        private readonly ?TelegramChat $chat,
        private readonly ?string $chat_type,
        private readonly ?string $chat_instance,
        private readonly ?string $start_param,
        private readonly ?int $can_send_after,
        private readonly ?int $auth_date,
        private readonly ?string $signature,
    ) {}

    public function getQueryId(): ?string
    {
        return $this->query_id;
    }

    public function getUser(): ?TelegramUser
    {
        return $this->user;
    }

    public function getReceiver(): ?TelegramUser
    {
        return $this->receiver;
    }

    public function getChat(): ?TelegramChat
    {
        return $this->chat;
    }

    public function getChatType(): ?string
    {
        return $this->chat_type;
    }

    public function getChatInstance(): ?string
    {
        return $this->chat_instance;
    }

    public function getStartParam(): ?string
    {
        return $this->start_param;
    }

    public function getCanSendAfter(): ?int
    {
        return $this->can_send_after;
    }

    public function getAuthDate(): ?int
    {
        return $this->auth_date;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }
}
