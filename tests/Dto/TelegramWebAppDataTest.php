<?php

namespace Micromagicman\TelegramWebApp\Tests\Dto;

use Micromagicman\TelegramWebApp\Dto\TelegramChat;
use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use Micromagicman\TelegramWebApp\Dto\TelegramWebAppData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TelegramWebAppDataTest extends TestCase
{
    #[Test]
    public function test_chat_dto_maps_fields()
    {
        $chat = new TelegramChat([
            'id' => 333333333,
            'type' => 'group',
            'title' => 'Test group',
            'username' => 'testgroup',
            'photo_url' => 'https://t.me/i/chat/333333333.jpg',
        ]);

        $this->assertSame(333333333, $chat->getId());
        $this->assertSame('group', $chat->getType());
        $this->assertSame('Test group', $chat->getTitle());
        $this->assertSame('testgroup', $chat->getUsername());
        $this->assertSame('https://t.me/i/chat/333333333.jpg', $chat->getPhotoUrl());
    }

    #[Test]
    public function test_chat_dto_optional_fields_default_to_null()
    {
        $chat = new TelegramChat([
            'id' => 333333333,
            'type' => 'private',
        ]);

        $this->assertSame(333333333, $chat->getId());
        $this->assertSame('private', $chat->getType());
        $this->assertNull($chat->getTitle());
        $this->assertNull($chat->getUsername());
        $this->assertNull($chat->getPhotoUrl());
    }

    #[Test]
    public function test_web_app_data_dto_maps_fields()
    {
        $user = new TelegramUser(['id' => 111111111, 'first_name' => 'Evgen']);
        $receiver = new TelegramUser(['id' => 222222222, 'first_name' => 'Other']);
        $chat = new TelegramChat(['id' => 333333333, 'type' => 'group', 'title' => 'Test']);

        $data = new TelegramWebAppData(
            query_id: 'AAE0m7oLAAAAADSbugtKyT4p',
            user: $user,
            receiver: $receiver,
            chat: $chat,
            chat_type: 'group',
            chat_instance: '444444444',
            start_param: 'ref123',
            can_send_after: 1698815000,
            auth_date: 1698814911,
            signature: 'abc123',
        );

        $this->assertSame('AAE0m7oLAAAAADSbugtKyT4p', $data->getQueryId());
        $this->assertSame($user, $data->getUser());
        $this->assertSame($receiver, $data->getReceiver());
        $this->assertSame($chat, $data->getChat());
        $this->assertSame('group', $data->getChatType());
        $this->assertSame('444444444', $data->getChatInstance());
        $this->assertSame('ref123', $data->getStartParam());
        $this->assertSame(1698815000, $data->getCanSendAfter());
        $this->assertSame(1698814911, $data->getAuthDate());
        $this->assertSame('abc123', $data->getSignature());
    }

    #[Test]
    public function test_web_app_data_dto_optional_fields_default_to_null()
    {
        $user = new TelegramUser(['id' => 111111111, 'first_name' => 'Evgen']);

        $data = new TelegramWebAppData(
            query_id: null,
            user: $user,
            receiver: null,
            chat: null,
            chat_type: null,
            chat_instance: null,
            start_param: null,
            can_send_after: null,
            auth_date: null,
            signature: null,
        );

        $this->assertNull($data->getQueryId());
        $this->assertSame($user, $data->getUser());
        $this->assertNull($data->getReceiver());
        $this->assertNull($data->getChat());
        $this->assertNull($data->getChatType());
        $this->assertNull($data->getChatInstance());
        $this->assertNull($data->getStartParam());
        $this->assertNull($data->getCanSendAfter());
        $this->assertNull($data->getAuthDate());
        $this->assertNull($data->getSignature());
    }
}
