<?php

namespace ITS\Trustpilot\API\OAuth2;

class AccessToken implements \Serializable
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @var \DateTimeImmutable
     */
    protected $expiryDate;

    /**
     * @param string $accessToken
     * @param string $refreshToken
     * @param \DateTimeInterface $expiryDate
     */
    public function __construct($accessToken, $refreshToken, \DateTimeInterface $expiryDate)
    {
        $this->accessToken  = $accessToken;
        $this->refreshToken = $refreshToken;

        if ($expiryDate instanceof \DateTimeImmutable) {
            $this->expiryDate = $expiryDate;
        } else {
            $this->expiryDate = new \DateTimeImmutable('@' . $expiryDate->getTimestamp());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            'access_token'  => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expiry_date'   => $this->expiryDate
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->accessToken, $this->refreshToken, $this->expiryDate) = unserialize($serialized);
    }

    /**
     * @return bool
     */
    public function hasExpired()
    {
        return $this->expiryDate->getTimestamp() < time();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->accessToken;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     *
     * @return \DateTimeImmutable
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }
}