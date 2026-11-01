<?php

namespace Micromagicman\TelegramWebApp\Tests\Dto;

use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TelegramUserTest extends TestCase
{
    #[Test]
    public function test_user_dto_maps_all_fields()
    {
        $user = new TelegramUser([
            'id' => 111111111,
            'is_bot' => false,
            'first_name' => 'Evgen',
            'last_name' => 'Evgen',
            'username' => 'micromagicman',
            'language_code' => 'ru',
            'is_premium' => true,
            'added_to_attachment_menu' => true,
            'allows_write_to_pm' => true,
            'photo_url' => 'https://t.me/i/userpic/320/evgen.jpg',
        ]);

        $this->assertSame(111111111, $user->getId());
        $this->assertFalse($user->isBot());
        $this->assertSame('Evgen', $user->getFirstName());
        $this->assertSame('Evgen', $user->getLastName());
        $this->assertSame('micromagicman', $user->getUsername());
        $this->assertSame('ru', $user->getLanguageCode());
        $this->assertTrue($user->isPremium());
        $this->assertTrue($user->isAddedToAttachmentMenu());
        $this->assertTrue($user->isAllowsWriteToPm());
        $this->assertSame('https://t.me/i/userpic/320/evgen.jpg', $user->getPhotoUrl());
    }

    #[Test]
    public function test_user_dto_optional_fields_default_to_null()
    {
        $user = new TelegramUser([
            'id' => 111111111,
            'first_name' => 'Evgen',
        ]);

        $this->assertSame(111111111, $user->getId());
        $this->assertSame('Evgen', $user->getFirstName());
        $this->assertNull($user->isBot());
        $this->assertNull($user->getLastName());
        $this->assertNull($user->getUsername());
        $this->assertNull($user->getLanguageCode());
        $this->assertNull($user->isPremium());
        $this->assertNull($user->isAddedToAttachmentMenu());
        $this->assertNull($user->isAllowsWriteToPm());
        $this->assertNull($user->getPhotoUrl());
    }
}
