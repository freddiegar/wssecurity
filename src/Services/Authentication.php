<?php

namespace Freddiegar\Authentication\Services;

use Freddiegar\Authentication\Exceptions\AuthenticationException;

/**
 * Class Authentication
 *
 * @method string username($username = null)
 * @method string tranKey($tranKey = null)
 * @method string created($created = null)
 * @method string nonce($nonce = null)
 *
 * @package Freddiegar\Authentication\Services
 */
class Authentication
{
    public function __construct($username, $tranKey, $options = [])
    {
        if (!isset($username) || !isset($tranKey)) {
            throw new AuthenticationException('No username or tranKey provided on authentication');
        }

        $this->username($username);
        $this->tranKey($tranKey);
        $this->created($this->getCreated());
        $this->nonce($this->getNonce());

        // For testing
        if (isset($options['created']) && isset($options['nonce'])) {
            $this->created($options['created']);
            $this->nonce($options['nonce']);
        }
    }

    /**
     * Get time to compare in request
     *
     * @return false|string
     */
    private function getCreated()
    {
        return date('c');
    }

    /**
     * Get nonce to compare in request
     *
     * @param bool $encoded
     * @return int|string
     */
    private function getNonce($encoded = true)
    {
        if (function_exists('random_bytes')) {
            $nonce = bin2hex(random_bytes(16));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $nonce = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $nonce = mt_rand();
        }

        return ($encoded) ? base64_encode($nonce) : $nonce;
    }

    /**
     * Convert tranKey in plain password
     * @return string
     */
    public function passwordDigest()
    {
        return base64_encode(
            sha1(
                base64_decode($this->nonce()) . $this->created() . $this->tranKey(),
                true
            )
        );
    }

    /**
     * Valid if password is equal to data send
     * @param $password
     * @return bool
     */
    public function isValid($password)
    {
        return $this->passwordDigest() === $password;
    }

    /**
     * Data to array
     * @return array
     */
    public function toArray()
    {
        return [
            'username' => $this->username(),
            'password' => $this->passwordDigest(),
            'nonce' => $this->nonce(),
            'created' => $this->created(),
        ];
    }

    /**
     * Setter and Getter dynamic
     *
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        $property = strtolower($name);

        if (isset($arguments[0])) {
            $this->{$property} = $arguments[0];
            return $this;
        }

        return $this->{$property};
    }

    /**
     * Data to json string
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray(), 0);
    }
}
