<?php

namespace Micromagicman\TelegramWebApp\Tests;

use Micromagicman\TelegramWebApp\Dto\TelegramUser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class TelegramUserTest extends TestCase
{

    /**
     * @throws ReflectionException - if getter is absent in {@link TelegramUser}
     */
    #[Test]
    public function testUserDtoHasGetterOnEachField()
    {
        $testUserData = [
            'id' => 111111111,
            'first_name' => 'Evgen',
            'last_name' => 'Evgen',
            'username' => 'micromagicman',
            'language_code' => 'xx',
            'is_premium' => true,
            'allows_write_to_pm' => true,
        ];
        $testUser = new TelegramUser( $testUserData );

        $reflection = new ReflectionClass( TelegramUser::class );
        foreach ( $reflection->getProperties() as $property ) {
            if ( !$property->isStatic() ) {
                $propertyType = $property->getType();
                $propertyName = $property->getName();
                $expectedValue = $testUserData[ $propertyName ];
                $methodPrefix = 'bool' === $propertyType->getName() ? 'is' : 'get';
                $propertyCamelCase = $this->snakeCaseToCamelCase( mb_strtolower( $property->getName() ) );
                $methodName = str_starts_with( $propertyCamelCase, 'is' )
                    ? $propertyCamelCase
                    : $methodPrefix . ucfirst( $propertyCamelCase );
                $method = $reflection->getMethod( $methodName );
                $this->assertEquals( $expectedValue, $method->invoke( $testUser ) );
            }
        }
    }

    private function snakeCaseToCamelCase( string $source ): string
    {
        return lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $source ) ) ) );
    }
}