<?php

namespace ITS\Trustpilot\API;

/**
 * Debug helper class
 */
class HttpDebug
{
    /**
     * @var string
     */
    public $lastRequestBody;

    /**
     * @var array
     */
    public $lastRequestHeaders;

    /**
     * @var int
     */
    public $lastResponseCode;

    /**
     * @var array
     */
    public $lastResponseHeaders;

    /**
     * @var \Exception
     */
    public $lastResponseError;

    /**
     * @return string
     */
    public function __toString()
    {
        if (! is_string($lastError = $this->lastResponseError)) {
            $lastError = json_encode($lastError);
        }

        $output = 'LastResponseCode: ' . $this->lastResponseCode
            . ', LastResponseError: ' . $lastError
            . ', LastResponseHeaders: ' . $this->lastResponseHeaders
            . ', LastRequestHeaders: ' . $this->lastRequestHeaders
            . ', LastRequestBody: ' . $this->lastRequestBody;

        return $output;
    }
}