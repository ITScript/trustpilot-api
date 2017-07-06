<?php

namespace ITS\Trustpilot\API\OAuth2;

class AccessToken implements \Serializable
{
    /**
     * @var string
     */
    protected $access_token;

    /**
     * @var string
     */
    protected $refresh_token;

    /**
     * @var \DateTimeImmutable
     */
    protected $expiry_date;

    /**
     * @param string $access_token
     * @param string $refresh_token
     * @param \DateTimeInterface $expiry_date
     */
    public function __construct($access_token, $refresh_token, \DateTimeInterface $expiry_date)
    {
        $this->access_token  = $access_token;
        $this->refresh_token = $refresh_token;

        if ($expiry_date instanceof \DateTimeImmutable) {
            $this->expiry_date = $expiry_date;
        } else {
            $this->expiry_date = new \DateTimeImmutable('@' . $expiry_date->getTimestamp());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            'access_token'  => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'expiry_date'   => $this->expiry_date
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->access_token, $this->refresh_token, $this->expiry_date) = unserialize($serialized);
    }

    /**
     * @return bool
     */
    public function hasExpired()
    {
        return $this->expiry_date->getTimestamp() < time();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->access_token;
    }

    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    /**
     *
     * @return \DateTimeImmutable
     */
    public function getExpiryDate()
    {
        return $this->expiry_date;
    }
}