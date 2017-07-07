<?php

namespace ITS\Trustpilot;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /**
     * @return false|string
     */
    protected static function getEnvApiKey()
    {
        return getenv('API_KEY');
    }

    /**
     * @return false|string
     */
    protected static function getEnvApiSecret()
    {
        return getenv('API_SECRET');
    }

    /**
     * @return false|string
     */
    protected static function getEnvUsername()
    {
        return getenv('USERNAME');
    }

    /**
     * @return false|string
     */
    protected static function getEnvPassword()
    {
        return getenv('PASSWORD');
    }

    /**
     * @return false|string
     */
    protected static function getEnvBusinessUnitId()
    {
        return getenv('BUSINESS_UNIT_ID');
    }
}