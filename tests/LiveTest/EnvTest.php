<?php

namespace ITS\Trustpilot\LiveTest;

use ITS\Trustpilot\BaseTest;

class EnvTest extends BaseTest
{
    public function testApiKey()
    {
        $value = self::getEnvApiKey();

        $this->assertNotEmpty($value);
        $this->assertInternalType('string', $value);
    }

    public function testApiSecret()
    {
        $value = self::getEnvApiSecret();

        $this->assertNotEmpty($value);
        $this->assertInternalType('string', $value);
    }

    public function testUsername()
    {
        $value = self::getEnvUsername();

        $this->assertNotEmpty($value);
        $this->assertInternalType('string', $value);
    }

    public function testPassword()
    {
        $value = self::getEnvPassword();

        $this->assertNotEmpty($value);
        $this->assertInternalType('string', $value);
    }

    public function testBusinessUnitId()
    {
        $value = self::getEnvBusinessUnitId();

        $this->assertNotEmpty($value);
        $this->assertInternalType('string', $value);
    }
}
